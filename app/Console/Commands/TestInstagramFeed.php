<?php

namespace App\Console\Commands;

use App\Models\HomeSection;
use App\Services\InstagramFeedService;
use App\Services\InstagramTokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TestInstagramFeed extends Command
{
    protected $signature = 'instagram:test-feed';

    protected $description = 'Diagnose Instagram feed API and post loading';

    public function handle(InstagramFeedService $service, InstagramTokenService $tokens): int
    {
        $token = config('services.instagram.access_token');

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

        $section = HomeSection::where('key', 'instagram_feed')->with('items')->first();

        if (! $section) {
            $this->error('instagram_feed section is missing. Run: php artisan home:ensure-instagram-feed');

            return self::FAILURE;
        }

        $this->info('Fallback items in database: ' . $section->items->count());

        Cache::forget('instagram.feed');
        Cache::store('file')->forget('instagram.feed');

        $version = config('services.instagram.graph_version', 'v21.0');
        $tokenType = strtoupper((string) ($debug['type'] ?? ''));
        $igUserId = config('services.instagram.user_id');

        if ($tokenType === 'PAGE' && $igUserId) {
            $this->line('Token type: PAGE (expected for production)');
            $this->line('Skipping /me/accounts because page tokens use INSTAGRAM_USER_ID directly.');

            $profile = Http::timeout(20)->get("https://graph.facebook.com/{$version}/{$igUserId}", [
                'fields' => 'username,media_count',
                'access_token' => $token,
            ]);

            $this->line('Instagram profile API status: ' . $profile->status());

            if (! $profile->successful()) {
                $this->warn($profile->body());
            } else {
                $this->info('Instagram account: @' . ($profile->json('username') ?? 'unknown'));
            }
        } else {
            $pages = Http::timeout(20)->get("https://graph.facebook.com/{$version}/me/accounts", [
                'fields' => 'instagram_business_account,access_token',
                'access_token' => $token,
            ]);

            $this->line('Facebook pages API status: ' . $pages->status());

            if (! $pages->successful()) {
                $this->warn($pages->body());
            }
        }

        $posts = $service->getPosts($section);
        $firstUrl = (string) ($posts->first()['media_url'] ?? '');

        $this->info('Posts returned by service: ' . $posts->count());
        $this->info('First post source: ' . (str_starts_with($firstUrl, 'http') ? 'Instagram CDN' : 'local fallback'));

        if ($posts->count() > 6 && str_starts_with($firstUrl, 'http')) {
            $this->info('Instagram feed is working correctly.');
        }

        if ($posts->count() <= 6 && str_starts_with($firstUrl, 'frontend_assets')) {
            $this->warn('Live Instagram API is not being used. Check storage/logs/laravel.log for "Instagram feed:" messages.');
            $this->warn('If the token expires quickly, run: php artisan instagram:refresh-token');
        }

        return self::SUCCESS;
    }
}
