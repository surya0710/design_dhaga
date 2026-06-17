<?php

namespace App\Console\Commands;

use App\Services\SitemapUrlService;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'generate:sitemap';

    protected $description = 'Generate sitemap.xml';

    public function handle(SitemapUrlService $sitemapUrlService)
    {
        $sitemap = Sitemap::create();
        $items = $sitemapUrlService->collect();

        foreach ($items as $item) {
            $priority = match (true) {
                $item['title'] === 'Home' => 1.0,
                $item['type'] === 'Product' || ($item['type'] === 'Static Page' && $item['title'] === 'Shop') => 0.9,
                $item['type'] === 'Blog' => 0.7,
                default => 0.8,
            };

            $frequency = $item['type'] === 'Product' || ($item['type'] === 'Static Page' && $item['title'] === 'Shop')
                ? Url::CHANGE_FREQUENCY_DAILY
                : Url::CHANGE_FREQUENCY_WEEKLY;

            $sitemap->add(
                Url::create($item['url'])
                    ->setPriority($priority)
                    ->setChangeFrequency($frequency)
                    ->setLastModificationDate(now())
            );
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully with ' . $items->count() . ' URLs.');
    }
}
