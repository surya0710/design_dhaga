<?php

namespace App\Console\Commands;

use App\Models\HomeSection;
use App\Services\InstagramCredentialService;
use App\Services\InstagramFeedService;
use App\Services\InstagramTokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TestInstagramFeed extends Command
{
    protected $signature = 'instagram:test-feed';

    protected $description = 'Diagnose Instagram feed API and post loading';

    public function handle(
        InstagramFeedService $service,
        InstagramTokenService $tokens,
        InstagramCredentialService $credentials
    ): int {
        $credentials->syncFromEnvIfEmpty();
        $credentials->applyToConfig();

        $token = $credentials->accessToken();

        if (! $token) {
            $this->error('INSTAGRAM_ACCESS_TOKEN is missing from config.');

            return self::FAILURE;
        }

        $this->info('Token: loaded (' . strlen($token) . ' chars)');

        $debug = $tokens->debugToken($token);

        if ($debug) {
            $this->line('Token valid: ' . (($debug['is_valid'] ?? false) ? 'yes' : 'no'));
            $this->line('Token expires: ' . $tokens->formatExpiry($debug['expires_at'] ?? 0));
        } else {
            $this->warn('Add INSTAGRAM_APP_ID and INSTAGRAM_APP_SECRET to inspect token expiry.');
        }

        $link = $tokens->resolveLinkedInstagramAccount(
            $token,
            $credentials->pageId() ?: null,
            $credentials->userId() ?: null
        );

        $this->newLine();
        $this->info('Page / Instagram linkage');
        $this->line('Facebook page: ' . ($link['page_name'] ?: 'unknown') . ' (' . ($link['page_id'] ?: 'not found') . ')');
        $this->line('Saved INSTAGRAM_USER_ID: ' . ($credentials->userId() ?: 'not set'));
        $this->line('Linked Instagram ID: ' . ($link['instagram_user_id'] ?: 'not linked'));
        $this->line('Linked Instagram username: ' . ($link['instagram_username'] ? '@' . $link['instagram_username'] : 'not linked'));

        foreach ($link['errors'] as $error) {
            $this->warn($error);
        }

        $section = HomeSection::where('key', 'instagram_feed')->with('items')->first();

        if (! $section) {
            $this->error('instagram_feed section is missing. Run: php artisan home:ensure-instagram-feed');

            return self::FAILURE;
        }

        $this->info('Fallback items in database: ' . $section->items->count());

        Cache::forget('instagram.feed');
        Cache::store('file')->forget('instagram.feed');

        $posts = $service->getPosts($section);
        $firstUrl = (string) ($posts->first()['media_url'] ?? '');

        $this->newLine();
        $this->info('Posts returned by service: ' . $posts->count());
        $this->info('First post source: ' . (str_starts_with($firstUrl, 'http') ? 'Instagram CDN' : 'local fallback'));

        if ($posts->count() > 6 && str_starts_with($firstUrl, 'http')) {
            $this->info('Instagram feed is working correctly.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->error('Live Instagram API is not being used.');

        if (! ($link['linked'] ?? false)) {
            $this->line('Root cause: Facebook Page is not linked to Instagram, or saved INSTAGRAM_USER_ID is wrong.');
            $this->line('Run: php artisan instagram:diagnose-link');
        } else {
            $this->line('Page is linked, but media API still failed. Regenerate token with instagram_basic permission.');
        }

        return self::FAILURE;
    }
}
