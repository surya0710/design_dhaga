<?php

namespace App\Console\Commands;

use App\Services\InstagramTokenService;
use Illuminate\Console\Command;

class RefreshInstagramToken extends Command
{
    protected $signature = 'instagram:refresh-token';

    protected $description = 'Generate a long-lived Facebook page token for the Instagram feed';

    public function handle(InstagramTokenService $tokens): int
    {
        $inputToken = config('services.instagram.access_token');

        if (! $inputToken) {
            $this->error('INSTAGRAM_ACCESS_TOKEN is missing from .env');

            return self::FAILURE;
        }

        if (! config('services.instagram.app_id') || ! config('services.instagram.app_secret')) {
            $this->error('Add INSTAGRAM_APP_ID and INSTAGRAM_APP_SECRET to .env first.');
            $this->line('Find them in Meta Developers -> Your App -> App settings -> Basic.');

            return self::FAILURE;
        }

        $this->info('Checking current token...');
        $currentDebug = $tokens->debugToken($inputToken);

        if ($currentDebug) {
            $this->line('Current token valid: ' . (($currentDebug['is_valid'] ?? false) ? 'yes' : 'no'));
            $this->line('Current token expires: ' . $tokens->formatExpiry($currentDebug['expires_at'] ?? 0));
        }

        $this->newLine();
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
        $this->comment('Page tokens created from a long-lived user token usually do not expire.');
        $this->comment('After updating .env, run: php artisan config:clear && php artisan config:cache');

        return self::SUCCESS;
    }
}
