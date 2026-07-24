<?php

namespace App\Console\Commands;

use App\Services\InstagramCredentialService;
use App\Services\InstagramTokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshInstagramToken extends Command
{
    protected $signature = 'instagram:refresh-token {--save : Save the refreshed token to the database}';

    protected $description = 'Generate a long-lived Facebook page token for the Instagram feed';

    public function handle(InstagramTokenService $tokens, InstagramCredentialService $credentials): int
    {
        if (! config('services.instagram.app_id') || ! config('services.instagram.app_secret')) {
            $this->error('Add INSTAGRAM_APP_ID and INSTAGRAM_APP_SECRET to .env first.');
            $this->line('Find them in Meta Developers -> Your App -> App settings -> Basic.');

            return self::FAILURE;
        }

        $credentials->syncFromEnvIfEmpty();
        $credentials->applyToConfig();

        $inputToken = $credentials->accessToken();

        if (! $inputToken) {
            $this->error('INSTAGRAM_ACCESS_TOKEN is missing from .env or database.');

            return self::FAILURE;
        }

        $this->info('Checking current token...');
        $currentDebug = $tokens->debugToken($inputToken);

        if ($currentDebug) {
            $this->line('Current token valid: ' . (($currentDebug['is_valid'] ?? false) ? 'yes' : 'no'));
            $this->line('Current token expires: ' . $tokens->formatExpiry($currentDebug['expires_at'] ?? 0));
        }

        $this->newLine();

        if ($this->option('save')) {
            $this->info('Refreshing and saving production page token...');

            $result = $tokens->refreshStoredToken($credentials, true);

            foreach ($result['errors'] ?? [] as $error) {
                $this->warn($error);
            }

            if (! ($result['success'] ?? false)) {
                $this->error($result['message'] ?? 'Unable to generate a page token.');

                return self::FAILURE;
            }

            Cache::forget('instagram.feed');
            Cache::store('file')->forget('instagram.feed');

            $page = $result['page'] ?? [];
            $pageDebug = $result['page_debug'] ?? [];

            $this->newLine();
            $this->info('Token saved to database for automatic refresh.');
            $this->line('INSTAGRAM_ACCESS_TOKEN=' . ($page['page_access_token'] ?? ''));
            $this->line('INSTAGRAM_USER_ID=' . ($page['instagram_user_id'] ?? ''));
            $this->line('INSTAGRAM_PAGE_ID=' . ($page['page_id'] ?? ''));
            $this->newLine();
            $this->info('Page: ' . ($page['page_name'] ?? 'unknown') . ' (' . ($page['page_id'] ?? '') . ')');
            $this->info('Page token expiry: ' . $tokens->formatExpiry($pageDebug['expires_at'] ?? 0));
            $this->comment('Scheduled refresh runs daily via instagram:auto-refresh.');

            return self::SUCCESS;
        }

        $this->info('Building production page token...');

        $result = $tokens->buildProductionToken($inputToken);

        foreach ($result['errors'] ?? [] as $error) {
            $this->warn($error);
        }

        $page = $result['page'] ?? null;

        if (! $page) {
            $this->error('Unable to generate a page token.');

            return self::FAILURE;
        }

        $pageDebug = $result['page_debug'] ?? [];

        $this->newLine();
        $this->info('Use these values in production .env:');
        $this->line('');
        $this->line('INSTAGRAM_ACCESS_TOKEN=' . $page['page_access_token']);
        $this->line('INSTAGRAM_USER_ID=' . $page['instagram_user_id']);
        $this->line('INSTAGRAM_PAGE_ID=' . $page['page_id']);
        $this->newLine();
        $this->info('Page: ' . $page['page_name'] . ' (' . $page['page_id'] . ')');
        $this->info('Page token expiry: ' . $tokens->formatExpiry($pageDebug['expires_at'] ?? 0));
        $this->newLine();
        $this->comment('Run with --save to store the token in the database for automatic refresh.');

        return self::SUCCESS;
    }
}
