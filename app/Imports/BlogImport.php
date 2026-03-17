<?php

namespace App\Imports;

use App\Models\Blog;
use App\Models\Tag;
use Carbon\Carbon;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
// use Maatwebsite\Excel\Concerns\ToModel;
set_time_limit(0); // 0 = unlimited time
// class BlogImport implements ToModel
class BlogImport implements OnEachRow, WithHeadingRow
{
private function parseDate($dateString)
{
    $formats = ['m/d/Y h:i A', 'm/d/Y', 'Y-m-d', 'Y-m-d H:i:s'];

    foreach ($formats as $format) {
        try {
            return \Carbon\Carbon::createFromFormat($format, trim($dateString));
        } catch (\Exception $e) {
            continue;
        }
    }

    return now();
}
    public function onRow(Row $row)
    {
        $row = $row->toArray();
        // dd(array_keys($row));
        // Skip if SKU or Name is empty
        if (empty($row['slug']) || empty($row['title'])) {
            return;
        }


        // Step 1: Read and process tags
        $tagsField = $row['tags'] ?? ''; // Adjust column name if needed
        $tagNames = array_filter(array_map('trim', explode('|', $tagsField))); // e.g., "health,fitness"

        // Step 2: Get or create tag IDs
        $tagIds = [];

        foreach ($tagNames as $tagName) {
            if ($tagName === '') continue;

            $slug = Str::slug($tagName);
            $tag = Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $tagName]
            );
            $tagIds[] = $tag->id;
        }

        // Step 3: Attach tags to the blog
        $blog = Blog::where('title', $row['title'])->first();

        if ($blog && !empty($tagIds)) {
            $blog->tags()->sync($tagIds);
        }


        $imageField = $row['image_url'] ?? '';
        // $categoryName = trim($row['categories'] ?? '');
        // $categoryId = null;

        // if ($categoryName !== '') {
        //     $category = Category::firstOrCreate(
        //         ['name' => $categoryName],
        //         ['slug' => Str::slug($categoryName)] 
        //     );
        //     $categoryId = $category->id;
        // }

        // Split the images by comma (even if there's only one)
        $imageUrls = array_filter(explode('|', $imageField));

        $imageNames = [];

        foreach ($imageUrls as $url) {
            $url = trim($url);

            $imageName = basename(parse_url($url, PHP_URL_PATH));

            $originalPath = public_path('uploads/blogs/' . $imageName);
            $thumbnailPath = public_path('uploads/blogs/thumbnails/' . $imageName);

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

                    // Generate and save thumbnail
                    $img->cover(410, 307, 'center')->save($thumbnailPath);

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
        $baseSlug = Str::slug($row['title'] ?? '');
        $slug = $baseSlug;
        $counter = 1;

        while (Blog::where('slug', $slug)
            ->where('id', '!=', $row['id']) // Exclude current SKU on update
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        
        $createdAt = $this->parseDate($row['date']);
        $updatedAt = $this->parseDate($row['post_modified_date']);

        Blog::updateOrCreate(

            ['title' => $row['title']], // Unique identifier
            [
                'title' => $row['title'],
                'slug' => $row['slug'],
                'content' => $row['content'],
                'image' => $imageString,
            ]
        );
        

        // Set timestamps manually
        $blog->created_at = $createdAt;
        $blog->updated_at = $updatedAt;

        // Disable automatic timestamps
        $blog->timestamps = false;

        $blog->save();
    }
}
