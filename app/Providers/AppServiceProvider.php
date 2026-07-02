<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use App\Models\HomepageHighlight;
use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        if ($this->app->runningInConsole()) {
            return;
        }

        $settings = Schema::hasTable('settings')
            ? Cache::remember('site.settings', 60, fn () => Setting::first())
            : null;

        View::share('settings', $settings);
        View::share('categories', $this->activeParentCategories());
        View::share('menu', $this->activeMenus());
        View::share('highlights', $this->activeHighlights());
    }

    private function activeParentCategories()
    {
        if (!Schema::hasTable('categories')) {
            return collect();
        }

        return Cache::remember('site.active_parent_categories', 60, function () {
            return Category::where('status', 1)
                ->where(function ($query) {
                    $query->whereNull('parent_id')
                        ->orWhere('parent_id', 0);
                })
                ->with('children')
                ->get();
        });
    }

    private function activeMenus()
    {
        if (!Schema::hasTable('menus')) {
            return collect();
        }

        return Cache::remember('site.active_menus', 60, function () {
            return Menu::where('is_active', 1)->orderBy('created_at', 'asc')->get();
        });
    }

    private function activeHighlights()
    {
        if (!Schema::hasTable('homepage_highlights')) {
            return collect();
        }

        return Cache::remember('site.homepage_highlights', 60, function () {
            return HomepageHighlight::where('status', 1)->get();
        });
    }
}
