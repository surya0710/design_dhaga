<?php

namespace App\Console\Commands;

use App\Models\HomeSection;
use App\Services\InstagramFeedService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TestInstagramFeed extends Command
{
    protected $signature = 'instagram:test-feed';

    protected $description = 'Diagnose Instagram feed API and post loading';

    public function handle(): int
    {
        $token = config('services.instagram.access_token');

        if (! $token) {
            $this->error('INSTAGRAM_ACCESS_TOKEN is missing from config.');

            return self::FAILURE;
        }

        $this->info('Token: loaded (' . strlen($token) . ' chars)');

        $section = HomeSection::where('key', 'instagram_feed')->with('items')->first();

        if (! $section) {
            $this->error('instagram_feed section is missing. Run: php artisan home:ensure-instagram-feed');

            return self::FAILURE;
        }

        $this->info('Fallback items in database: ' . $section->items->count());

        Cache::forget('instagram.feed');
        Cache::store('file')->forget('instagram.feed');

        $version = config('services.instagram.graph_version', 'v21.0');
        $pages = Http::timeout(20)->get("https://graph.facebook.com/{$version}/me/accounts", [
            'fields' => 'instagram_business_account,access_token',
            'access_token' => $token,
        ]);

        $this->line('Facebook pages API status: ' . $pages->status());

        if (! $pages->successful()) {
            $this->warn($pages->body());
        }

        $service = app(InstagramFeedService::class);
        $posts = $service->getPosts($section);
        $firstUrl = (string) ($posts->first()['media_url'] ?? '');

        $this->info('Posts returned by service: ' . $posts->count());
        $this->info('First post source: ' . (str_starts_with($firstUrl, 'http') ? 'Instagram CDN' : 'local fallback'));

        if ($posts->count() <= 6 && str_starts_with($firstUrl, 'frontend_assets')) {
            $this->warn('Live Instagram API is not being used. Check storage/logs/laravel.log for "Instagram feed:" messages.');
        }

        return self::SUCCESS;
    }
}
