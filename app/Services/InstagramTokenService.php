<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class InstagramTokenService
{
    public function debugToken(string $token): ?array
    {
        $appId = config('services.instagram.app_id');
        $appSecret = config('services.instagram.app_secret');

        if (! $appId || ! $appSecret) {
            return null;
        }

        $response = Http::timeout(20)->get($this->graphUrl('debug_token'), [
            'input_token' => $token,
            'access_token' => $appId . '|' . $appSecret,
        ]);

        if (! $response->successful()) {
            return [
                'valid' => false,
                'error' => $response->json('error.message', $response->body()),
            ];
        }

        return $response->json('data', []);
    }

    public function exchangeLongLivedUserToken(string $shortLivedToken): ?string
    {
        $appId = config('services.instagram.app_id');
        $appSecret = config('services.instagram.app_secret');

        if (! $appId || ! $appSecret) {
            return null;
        }

        $response = Http::timeout(20)->get($this->graphUrl('oauth/access_token'), [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'fb_exchange_token' => $shortLivedToken,
        ]);

        if (! $response->successful()) {
            return null;
        }

        return $response->json('access_token');
    }

    public function getPageAccessToken(string $userToken, ?string $pageId = null): ?array
    {
        $version = config('services.instagram.graph_version', 'v21.0');

        $response = Http::timeout(20)->get("https://graph.facebook.com/{$version}/me/accounts", [
            'fields' => 'id,name,access_token,instagram_business_account',
            'access_token' => $userToken,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $pages = collect($response->json('data', []));

        if ($pageId) {
            $page = $pages->firstWhere('id', $pageId);
        } else {
            $page = $pages->first(fn ($item) => ! empty($item['instagram_business_account']['id']));
        }

        if (! $page || empty($page['access_token'])) {
            return null;
        }

        return [
            'page_id' => (string) $page['id'],
            'page_name' => (string) ($page['name'] ?? ''),
            'page_access_token' => (string) $page['access_token'],
            'instagram_user_id' => (string) ($page['instagram_business_account']['id'] ?? ''),
        ];
    }

    public function resolveLinkedInstagramAccount(string $token, ?string $pageId = null, ?string $savedUserId = null): array
    {
        $debug = $this->debugToken($token) ?? [];
        $pageId = (string) ($pageId ?: config('services.instagram.page_id') ?: ($debug['profile_id'] ?? ''));

        $result = [
            'page_id' => $pageId,
            'page_name' => null,
            'instagram_user_id' => null,
            'instagram_username' => null,
            'linked' => false,
            'token_type' => $debug['type'] ?? null,
            'token_valid' => (bool) ($debug['is_valid'] ?? false),
            'scopes' => $debug['scopes'] ?? [],
            'errors' => [],
        ];

        if ($pageId === '') {
            $result['errors'][] = 'No Facebook page ID found on token or in config.';

            return $result;
        }

        $version = config('services.instagram.graph_version', 'v21.0');
        $pageResponse = Http::timeout(20)->get("https://graph.facebook.com/{$version}/{$pageId}", [
            'fields' => 'id,name,instagram_business_account{id,username}',
            'access_token' => $token,
        ]);

        if (! $pageResponse->successful()) {
            $result['errors'][] = 'Could not load Facebook page: ' . $pageResponse->json('error.message', $pageResponse->body());

            return $result;
        }

        $result['page_name'] = $pageResponse->json('name');
        $igAccount = $pageResponse->json('instagram_business_account');

        if (! empty($igAccount['id'])) {
            $result['instagram_user_id'] = (string) $igAccount['id'];
            $result['instagram_username'] = (string) ($igAccount['username'] ?? '');
            $result['linked'] = true;

            return $result;
        }

        $storedUserId = (string) ($savedUserId ?? config('services.instagram.user_id', ''));

        if ($storedUserId !== '') {
            $result['errors'][] = 'Saved INSTAGRAM_USER_ID=' . $storedUserId . ' does not match this page. Clear it from .env/database and link Instagram in Meta Business Suite.';
        } else {
            $result['errors'][] = 'Facebook page is not linked to an Instagram Professional account. Link them in Meta Business Suite -> Settings -> Linked accounts.';
        }

        $requiredScopes = ['pages_show_list', 'pages_read_engagement', 'instagram_basic'];
        $missingScopes = array_values(array_diff($requiredScopes, $result['scopes']));

        if ($missingScopes !== []) {
            $result['errors'][] = 'Token is missing permissions: ' . implode(', ', $missingScopes) . '. Re-generate token in Graph API Explorer with these scopes.';
        }

        return $result;
    }

    public function buildProductionToken(string $inputToken): array
    {
        $errors = [];
        $debug = $this->debugToken($inputToken);

        if (($debug['type'] ?? '') === 'PAGE' && ($debug['is_valid'] ?? false)) {
            $link = $this->resolveLinkedInstagramAccount(
                $inputToken,
                config('services.instagram.page_id'),
                config('services.instagram.user_id')
            );
            $errors = array_merge($errors, $link['errors']);

            return [
                'errors' => $errors,
                'debug' => $debug,
                'page_debug' => $debug,
                'link' => $link,
                'page' => [
                    'page_id' => $link['page_id'],
                    'page_name' => $link['page_name'] ?: 'Existing page access token',
                    'page_access_token' => $inputToken,
                    'instagram_user_id' => $link['instagram_user_id'] ?? '',
                ],
            ];
        }

        $userToken = $inputToken;
        $longLived = $this->exchangeLongLivedUserToken($inputToken);

        if ($longLived) {
            $userToken = $longLived;
            $debug = $this->debugToken($userToken);
        } else {
            $errors[] = 'Could not exchange token for a long-lived user token. Use a fresh Graph Explorer token with pages_show_list permission.';
        }

        $page = $this->getPageAccessToken(
            $userToken,
            config('services.instagram.page_id')
        );

        if (! $page) {
            $errors[] = 'Could not load a Facebook page token. Make sure the token has pages_show_list permission.';

            return [
                'errors' => $errors,
                'debug' => $debug,
            ];
        }

        $pageDebug = $this->debugToken($page['page_access_token']);

        return [
            'errors' => $errors,
            'page_debug' => $pageDebug,
            'debug' => $debug,
            'page' => $page,
        ];
    }

    public function formatExpiry(?int $timestamp): string
    {
        if (! $timestamp || $timestamp <= 0) {
            return 'does not expire';
        }

        return date('Y-m-d H:i:s T', $timestamp) . ' (' . max(0, $timestamp - time()) . ' seconds remaining)';
    }

    public function shouldRefresh(?array $debug, int $daysBeforeExpiry = 7): bool
    {
        if (! $debug || ! ($debug['is_valid'] ?? false)) {
            return true;
        }

        $expiresAt = (int) ($debug['expires_at'] ?? 0);

        if ($expiresAt <= 0) {
            return false;
        }

        return $expiresAt <= (time() + ($daysBeforeExpiry * 86400));
    }

    public function refreshStoredToken(InstagramCredentialService $credentials, bool $force = false): array
    {
        $credentials->applyToConfig();

        $currentToken = $credentials->accessToken();

        if ($currentToken === '') {
            return [
                'success' => false,
                'message' => 'No Instagram access token configured.',
            ];
        }

        $debug = $this->debugToken($currentToken);

        if (! $force && $debug && ($debug['is_valid'] ?? false) && ! $this->shouldRefresh($debug)) {
            return [
                'success' => true,
                'skipped' => true,
                'message' => 'Token is still valid.',
                'debug' => $debug,
            ];
        }

        $inputToken = $currentToken;
        $exchanged = $this->exchangeLongLivedUserToken($currentToken);

        if ($exchanged) {
            $inputToken = $exchanged;
            $debug = $this->debugToken($inputToken);
        }

        $result = $this->buildProductionToken($inputToken);
        $errors = $result['errors'] ?? [];
        $page = $result['page'] ?? null;

        if (! $page || empty($page['page_access_token'])) {
            return [
                'success' => false,
                'message' => 'Unable to generate a page token.',
                'errors' => $errors,
                'debug' => $debug,
            ];
        }

        $pageDebug = $result['page_debug'] ?? [];
        $expiresAt = (int) ($pageDebug['expires_at'] ?? 0);

        $credentials->store([
            'access_token' => $page['page_access_token'],
            'user_id' => $page['instagram_user_id'] ?? null,
            'page_id' => $page['page_id'] ?? null,
            'expires_at' => $expiresAt > 0 ? now()->setTimestamp($expiresAt) : null,
            'last_refreshed_at' => now(),
        ]);

        return [
            'success' => true,
            'message' => 'Instagram token refreshed.',
            'errors' => $errors,
            'debug' => $debug,
            'page_debug' => $pageDebug,
            'page' => $page,
        ];
    }

    private function graphUrl(string $path): string
    {
        $version = config('services.instagram.graph_version', 'v21.0');

        return "https://graph.facebook.com/{$version}/{$path}";
    }
}
