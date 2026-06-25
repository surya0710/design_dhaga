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

    public function buildProductionToken(string $inputToken): array
    {
        $errors = [];
        $debug = $this->debugToken($inputToken);

        if (($debug['type'] ?? '') === 'PAGE' && ($debug['is_valid'] ?? false)) {
            return [
                'errors' => $errors,
                'debug' => $debug,
                'page_debug' => $debug,
                'page' => [
                    'page_id' => (string) (config('services.instagram.page_id') ?: ($debug['profile_id'] ?? '')),
                    'page_name' => 'Existing page access token',
                    'page_access_token' => $inputToken,
                    'instagram_user_id' => (string) (config('services.instagram.user_id') ?: ''),
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

    private function graphUrl(string $path): string
    {
        $version = config('services.instagram.graph_version', 'v21.0');

        return "https://graph.facebook.com/{$version}/{$path}";
    }
}
