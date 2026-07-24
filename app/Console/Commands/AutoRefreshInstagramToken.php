<?php

namespace App\Console\Commands;

use App\Services\InstagramCredentialService;
use App\Services\InstagramTokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AutoRefreshInstagramToken extends Command
{
    protected $signature = 'instagram:auto-refresh {--force : Refresh even if the token is still valid}';

    protected $description = 'Refresh the Instagram page token before it expires and store it in the database';

    public function handle(
        InstagramCredentialService $credentials,
        InstagramTokenService $tokens
    ): int {
        if (! config('services.instagram.app_id') || ! config('services.instagram.app_secret')) {
            $this->error('Add INSTAGRAM_APP_ID and INSTAGRAM_APP_SECRET to .env first.');

            return self::FAILURE;
        }

        $credentials->syncFromEnvIfEmpty();
        $credentials->applyToConfig();

        $currentToken = $credentials->accessToken();

        if ($currentToken === '') {
            $this->error('No Instagram access token found in database or .env.');

            return self::FAILURE;
        }

        $debug = $tokens->debugToken($currentToken);

        if ($debug) {
            $this->line('Current token valid: ' . (($debug['is_valid'] ?? false) ? 'yes' : 'no'));
            $this->line('Current token expires: ' . $tokens->formatExpiry($debug['expires_at'] ?? 0));
        }

        if (! $this->option('force') && $debug && ($debug['is_valid'] ?? false) && ! $tokens->shouldRefresh($debug)) {
            $this->info('Token is still valid. Skipping refresh.');

            return self::SUCCESS;
        }

        $this->info('Refreshing Instagram token...');

        $result = $tokens->refreshStoredToken($credentials, (bool) $this->option('force'));

        foreach ($result['errors'] ?? [] as $error) {
            $this->warn($error);
        }

        if (! ($result['success'] ?? false)) {
            $this->error($result['message'] ?? 'Unable to refresh Instagram token.');
            Log::warning('Instagram token auto-refresh failed.', $result);

            return self::FAILURE;
        }

        if ($result['skipped'] ?? false) {
            $this->info($result['message']);

            return self::SUCCESS;
        }

        Cache::forget('instagram.feed');
        Cache::store('file')->forget('instagram.feed');

        $page = $result['page'] ?? [];
        $pageDebug = $result['page_debug'] ?? [];

        $this->newLine();
        $this->info('Instagram token refreshed and saved to database.');
        $this->line('Page: ' . ($page['page_name'] ?? 'unknown') . ' (' . ($page['page_id'] ?? '') . ')');
        $this->line('Instagram user ID: ' . ($page['instagram_user_id'] ?? ''));
        $this->line('Page token expiry: ' . $tokens->formatExpiry($pageDebug['expires_at'] ?? 0));

        Log::info('Instagram token auto-refresh succeeded.', [
            'page_id' => $page['page_id'] ?? null,
            'instagram_user_id' => $page['instagram_user_id'] ?? null,
        ]);

        return self::SUCCESS;
    }
}
