<?php

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

function getIconsByCategory($category){
    $categoryIcons = [
        "1" => "Natural Fibre",
        "2" => "Hand Painted",
        "3" => "Made in India",
        "4" => "Limited Edition",
        "5" => "Timeless Appeal",
        "6" => "Pack of 1"
    ];
    return $categoryIcons;
}

function getCartItemsCount(){
    $cartItemsCount = 0;
    if (Auth::check() && Auth::user()->utype === 'USR') {
        $cartItemsCount = Cart::where('user_id', Auth::id())->count();
    }
    return $cartItemsCount;
}

if (!function_exists('versionedAsset')) {
    function versionedAsset(string $path): string
    {
        $path = ltrim($path, '/');
        $filePath = public_path($path);
        $version = File::exists($filePath) ? File::lastModified($filePath) : time();
        $separator = str_contains($path, '?') ? '&' : '?';

        return asset($path) . $separator . 'v=' . $version;
    }
}

if (!function_exists('getProductUrl')) {
    function getProductUrl($product)
    {
        if (!$product || !$product->category) {
            return '#';
        }

        $category = $product->category;
        $parent   = $category->parent; // key change

        $parentSlug = $parent ? $parent->slug : $category->slug;

        $subcategorySlug = $parent ? $category->slug : null;

        return route('shop.product', [
            'category' => $parentSlug,
            'subcategory' => $subcategorySlug,
            'product' => $product->slug
        ]);
    }
}

if (!function_exists('responsiveImage')) {
    function responsiveImage(?string $path, array $widths, string $disk = 'public'): array
    {
        $path = trim((string) $path);
        $path = ltrim($path, '/');

        if ($path === '') {
            return ['src' => '', 'srcset' => ''];
        }

        $isStorage = $disk === 'storage';
        $sourcePath = $isStorage
            ? storage_path('app/public/' . $path)
            : public_path($path);
        $sourceUrl = $isStorage
            ? Storage::url($path)
            : asset($path);

        if (! File::exists($sourcePath) || strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION)) === 'svg') {
            return ['src' => $sourceUrl, 'srcset' => ''];
        }

        $imageSize = @getimagesize($sourcePath);
        $sourceWidth = (int) ($imageSize[0] ?? 0);

        if ($sourceWidth <= 0) {
            return ['src' => $sourceUrl, 'srcset' => ''];
        }

        $widths = collect($widths)
            ->map(fn ($width) => (int) $width)
            ->filter(fn ($width) => $width > 0 && $width < $sourceWidth)
            ->unique()
            ->sort()
            ->values();

        $srcset = [];
        $src = $sourceUrl;
        $manager = null;

        foreach ($widths as $width) {
            $variant = responsiveImageVariantPath($path, $width);
            $variantPath = $isStorage
                ? storage_path('app/public/' . $variant)
                : public_path($variant);

            if (! File::exists($variantPath)) {
                try {
                    File::ensureDirectoryExists(dirname($variantPath));
                    $manager ??= new ImageManager(new Driver());
                    $manager->read($sourcePath)
                        ->scale(width: $width)
                        ->toWebp(quality: 90)
                        ->save($variantPath);
                } catch (Throwable) {
                    continue;
                }
            }

            if (File::exists($variantPath)) {
                $variantUrl = $isStorage
                    ? Storage::url($variant)
                    : asset($variant);

                $src = $src === $sourceUrl ? $variantUrl : $src;
                $srcset[] = $variantUrl . ' ' . $width . 'w';
            }
        }

        $srcset[] = $sourceUrl . ' ' . $sourceWidth . 'w';

        return [
            'src' => $src,
            'srcset' => implode(', ', $srcset),
        ];
    }
}

if (!function_exists('responsiveImageVariantPath')) {
    function responsiveImageVariantPath(string $path, int $width): string
    {
        $directory = trim(pathinfo($path, PATHINFO_DIRNAME), '.');
        $filename = pathinfo($path, PATHINFO_FILENAME);

        return trim($directory . '/responsive/' . $filename . '-' . $width . 'w.webp', '/');
    }
}