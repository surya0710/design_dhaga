<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioCategory;
use App\Models\PortfolioGallery;
use App\Models\PortfolioSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    public function categories()
    {
        $categories = PortfolioCategory::withCount(['subcategories', 'galleries'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.portfolio.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:portfolio_categories,slug',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $data['slug'] = $this->uniqueSlug(PortfolioCategory::class, $data['slug'] ?? $data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeImage($request, 'uploads/portfolio-categories');
        }

        PortfolioCategory::create($data);

        return back()->with('status', 'Portfolio category created successfully.');
    }

    public function updateCategory(Request $request, PortfolioCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:portfolio_categories,slug,' . $category->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $data['slug'] = $this->uniqueSlug(PortfolioCategory::class, $data['slug'] ?? $data['name'], $category->id);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $this->deleteImage($category->image);
            $data['image'] = $this->storeImage($request, 'uploads/portfolio-categories');
        }

        $category->update($data);

        return back()->with('status', 'Portfolio category updated successfully.');
    }

    public function deleteCategory(PortfolioCategory $category)
    {
        $category->galleries()->get()->each(function (PortfolioGallery $gallery) {
            $this->deleteImage($gallery->image);
        });
        $this->deleteImage($category->image);

        $category->delete();

        return back()->with('status', 'Portfolio category deleted successfully.');
    }

    public function subcategories()
    {
        $categories = PortfolioCategory::orderBy('sort_order')->orderBy('name')->get();
        $subcategories = PortfolioSubcategory::with('category')
            ->orderBy('portfolio_category_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.portfolio.subcategories', compact('categories', 'subcategories'));
    }

    public function storeSubcategory(Request $request)
    {
        $data = $this->validateSubcategory($request);
        $data['slug'] = $this->uniqueSubcategorySlug($data['portfolio_category_id'], $data['slug'] ?? $data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        PortfolioSubcategory::create($data);

        return back()->with('status', 'Portfolio subcategory created successfully.');
    }

    public function updateSubcategory(Request $request, PortfolioSubcategory $subcategory)
    {
        $data = $this->validateSubcategory($request, $subcategory);
        $data['slug'] = $this->uniqueSubcategorySlug(
            $data['portfolio_category_id'],
            $data['slug'] ?? $data['name'],
            $subcategory->id
        );
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $subcategory->update($data);

        return back()->with('status', 'Portfolio subcategory updated successfully.');
    }

    public function deleteSubcategory(PortfolioSubcategory $subcategory)
    {
        $subcategory->delete();

        return back()->with('status', 'Portfolio subcategory deleted successfully.');
    }

    public function gallery(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $galleries = PortfolioGallery::with(['category', 'subcategory'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('alt_text', 'like', '%' . $search . '%')
                        ->orWhereHas('category', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('subcategory', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('sort_order')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.portfolio.gallery', compact('galleries'));
    }

    public function createGallery()
    {
        return view('admin.portfolio.gallery-form', $this->galleryFormData());
    }

    public function storeGallery(Request $request)
    {
        $data = $this->validateGallery($request);
        $data['image'] = $this->storeImage($request, 'uploads/portfolio-gallery');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        PortfolioGallery::create($data);

        return redirect()->route('admin.portfolio.gallery.index')->with('status', 'Portfolio image uploaded successfully.');
    }

    public function editGallery(PortfolioGallery $gallery)
    {
        return view('admin.portfolio.gallery-form', $this->galleryFormData($gallery));
    }

    public function updateGallery(Request $request, PortfolioGallery $gallery)
    {
        $data = $this->validateGallery($request, $gallery);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $this->deleteImage($gallery->image);
            $data['image'] = $this->storeImage($request, 'uploads/portfolio-gallery');
        }

        $gallery->update($data);

        return redirect()->route('admin.portfolio.gallery.index')->with('status', 'Portfolio image updated successfully.');
    }

    public function deleteGallery(PortfolioGallery $gallery)
    {
        $this->deleteImage($gallery->image);
        $gallery->delete();

        return back()->with('status', 'Portfolio image deleted successfully.');
    }

    public function subcategoriesForCategory(PortfolioCategory $category)
    {
        return response()->json(
            $category->subcategories()
                ->where('status', 1)
                ->get(['id', 'name'])
        );
    }

    private function galleryFormData(?PortfolioGallery $gallery = null): array
    {
        $categories = PortfolioCategory::where('status', 1)->orderBy('sort_order')->orderBy('name')->get();
        $subcategories = PortfolioSubcategory::where('status', 1)->orderBy('sort_order')->orderBy('name')->get();

        return compact('categories', 'subcategories', 'gallery');
    }

    private function validateSubcategory(Request $request, ?PortfolioSubcategory $subcategory = null): array
    {
        return $request->validate([
            'portfolio_category_id' => 'required|exists:portfolio_categories,id',
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('portfolio_subcategories', 'slug')
                    ->where('portfolio_category_id', $request->portfolio_category_id)
                    ->ignore($subcategory),
            ],
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
        ]);
    }

    private function validateGallery(Request $request, ?PortfolioGallery $gallery = null): array
    {
        $imageRule = $gallery ? 'nullable' : 'required';

        return $request->validate([
            'portfolio_category_id' => 'required|exists:portfolio_categories,id',
            'portfolio_subcategory_id' => [
                'nullable',
                Rule::exists('portfolio_subcategories', 'id')
                    ->where('portfolio_category_id', $request->portfolio_category_id),
            ],
            'title' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
            'image' => $imageRule . '|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);
    }

    private function uniqueSlug(string $modelClass, string $value, ?int $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $slug = $slug !== '' ? $slug : Str::random(8);
        $original = $slug;
        $counter = 1;

        while ($modelClass::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }

    private function uniqueSubcategorySlug(int $categoryId, string $value, ?int $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $slug = $slug !== '' ? $slug : Str::random(8);
        $original = $slug;
        $counter = 1;

        while (PortfolioSubcategory::where('portfolio_category_id', $categoryId)
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }

    private function storeImage(Request $request, string $folder): string
    {
        $directory = public_path($folder);

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $image = $request->file('image');
        $filename = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
        $image->move($directory, $filename);

        return $folder . '/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
