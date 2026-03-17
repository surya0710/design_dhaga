<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
// use Maatwebsite\Excel\Concerns\ToModel;
set_time_limit(0); // 0 = unlimited time
// class ProductImport implements ToModel
class ProductImport implements OnEachRow, WithHeadingRow
{

    public function onRow(Row $row)
    {
        $row = $row->toArray();
        // dd(array_keys($row));
        // Skip if SKU or Name is empty
        if (empty($row['sku']) || empty($row['name'])) {
            return;
        }


        $imageField = $row['images'] ?? '';
        $categoryName = trim($row['categories'] ?? '');
        $categoryId = null;

        if ($categoryName !== '') {
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['slug' => Str::slug($categoryName)] // You can also generate a slug
            );
            $categoryId = $category->id;
        }

        // Split the images by comma (even if there's only one)
        $imageUrls = array_filter(explode(',', $imageField));

        $imageNames = [];

        foreach ($imageUrls as $url) {
            $url = trim($url);

            $imageName = basename(parse_url($url, PHP_URL_PATH));

            $originalPath = public_path('uploads/products/' . $imageName);
            $originalPathThumb = public_path('uploads/products/thumbbig/' . $imageName);
            $thumbnailPath = public_path('uploads/products/thumbnails/' . $imageName);

            if (!file_exists($originalPath)) {
                // Download original image
                try {
                    $imageData = file_get_contents($url);
                    file_put_contents($originalPath, $imageData);
                } catch (\Exception $e) {
                    continue; // Skip failed downloads
                }
            }

            // ✅ Generate thumbnail
            if (!file_exists($thumbnailPath)) {
                try {
                    // ✅ Use local file path instead of URL
                    $img = Image::read($originalPath);

                    // Main image (optional resize if needed)
                    $img->cover(500, 500, 'center')->save($originalPathThumb); // If you want to resize original

                    // Generate and save thumbnail
                    $img->cover(300, 300, 'center')->save($thumbnailPath);

                } catch (\Exception $e) {
                    // Optional: Log the error
                    \Log::error("Thumbnail generation failed for $originalPath: " . $e->getMessage());
                }
            }

            $imageNames[] = $imageName;
        }

        // Join names with pipe separator for DB storage
        $imageString = implode(',', $imageNames);


        // for slugs
        $baseSlug = Str::slug($row['name'] ?? '');
        $slug = $baseSlug;
        $counter = 1;

        while (Product::where('slug', $slug)
            ->where('sku', '!=', $row['sku']) // Exclude current SKU on update
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }


        Product::updateOrCreate(

            ['sku' => $row['sku']], // Unique identifier
            [
                'name' => $row['name'],
                'slug' => $slug,
                'category_id' => $categoryId,
                'short_description' => $row['short_description'],
                'description' => $row['description'],
                'regular_price' => is_numeric($row['regular_price']) ? $row['regular_price'] : 0,
                'sale_price' => is_numeric($row['sale_price']) ? $row['sale_price'] : 0,
                'sku' => $row['sku'],
                'stock_status' => ($row['in_stock'] ?? '0') == '1' ? 'instock' : 'outofstock',
                'featured' => ($row['is_featured'] ?? '0') == '1',
                'quantity' => $row['stock'],
                'images' => $imageString,
                'carat_weight' => is_numeric($row['attribute_1_values']) ? $row['attribute_1_values'] : null,
                'ratti_weight' => is_numeric($row['attribute_2_values']) ? $row['attribute_2_values'] : null,
                'weight' => is_numeric($row['attribute_9_values']) ? $row['attribute_9_values'] : null,
                'dimension' => $row['attribute_3_values'],
                'origin_country' => $row['attribute_4_values'],
                'refractive_index' => $row['attribute_5_values'],
                'shape' => $row['attribute_6_values'],
                'species' => $row['attribute_7_values'],
                'specific_gravity' => is_numeric($row['attribute_8_values']) ? $row['attribute_8_values'] : null,
                'color' => $row['attribute_10_values'],
                'certification' => $row['attribute_11_values'],
            ]
        );
    }
}
