<?php

namespace App\Http\Controllers;

use App\Imports\BlogImport;
use App\Models\AskQuestion;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductAttribute;
use App\Models\Subscribe;
use App\Models\Tag;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Story;
use App\Models\Sliders;
use App\Models\ProductIcon;
use App\Models\ProductVariant;
use App\Models\Pages;
use App\Services\SitemapUrlService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use intervention\image\Laravel\Facades\Image;   // kept for Brand / Category / Blog only
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Services\ShiprocketService;
use App\Models\AboutSection;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    protected $shiprocket;

    public function __construct(ShiprocketService $shiprocket)
    {
        $this->shiprocket = $shiprocket;
    }

    private function resizeAndSaveImage($sourcePath, $destinationPath, $maxWidth, $maxHeight)
    {
        list($width, $height, $type) = getimagesize($sourcePath);

        $ratio = min($maxWidth / $width, $maxHeight / $height);

        $newWidth  = (int) ($width * $ratio);
        $newHeight = (int) ($height * $ratio);

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        switch ($type) {

            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;

            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                break;

            case IMAGETYPE_WEBP:
                $sourceImage = imagecreatefromwebp($sourcePath);
                break;

            default:
                return false;
        }

        imagecopyresampled(
            $newImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $width,
            $height
        );

        switch ($type) {

            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $destinationPath, 90);
                break;

            case IMAGETYPE_PNG:
                imagepng($newImage, $destinationPath);
                break;

            case IMAGETYPE_WEBP:
                imagewebp($newImage, $destinationPath, 90);
                break;
        }

        /* IMPORTANT: FREE MEMORY */

        imagedestroy($sourceImage);
        imagedestroy($newImage);
    }

    // =========================================================================
    // AUTH
    // =========================================================================

    public function login()
    {
        return view('auth.login');
    }

    public function loginAttempt(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('admin.index');
        }

        return redirect()->back()->withErrors(['email' => 'Invalid credentials.']);
    }

    // =========================================================================
    // DASHBOARD
    // =========================================================================

    public function index()
    {
        $completedOrderStatuses = ['delivered', 'completed'];

        $totalOrders     = Order::count();
        $recentOrders    = Order::where('payment_status', 'paid')->with('items')->orderBy('created_at', 'desc')->take(5)->get();
        $deliveredOrders = Order::where('order_status', 'delivered')->count();
        $pendingOrders   = Order::where('order_status', 'pending')->count();
        $cancelledOrders = Order::where('order_status', 'cancelled')->count();
        $totalAmount     = Order::whereIn('order_status', $completedOrderStatuses)->sum('total');
        $deliveredAmount = Order::where('order_status', 'delivered')->sum('total');
        $cancelledAmount = Order::where('order_status', 'cancelled')->sum('total');

        $sitemapUrls        = app(SitemapUrlService::class)->collect();
        $sitemapTypeCounts  = $sitemapUrls->groupBy('type')->map->count();
        $totalPagesCount    = $sitemapUrls->count();
        $frontendPagesCount = ($sitemapTypeCounts['Page'] ?? 0) + ($sitemapTypeCounts['Static Page'] ?? 0);
        $productsCount      = $sitemapTypeCounts['Product'] ?? 0;
        $blogsCount         = $sitemapTypeCounts['Blog'] ?? 0;
        $categoriesCount    = $sitemapTypeCounts['Category'] ?? 0;
        $portfolioCategoriesCount = $sitemapTypeCounts['Portfolio Category'] ?? 0;

        $startOfWeek   = Carbon::now()->startOfWeek();
        $endOfWeek     = Carbon::now()->endOfWeek();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd   = Carbon::now()->subWeek()->endOfWeek();

        $thisWeekRevenue = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('total');
        $lastWeekRevenue = Order::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->sum('total');
        $thisWeekOrders  = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        $lastWeekOrders  = Order::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();

        $revenueChange = $lastWeekRevenue > 0
            ? (($thisWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100
            : 0;
        $orderChange = $lastWeekOrders > 0
            ? (($thisWeekOrders - $lastWeekOrders) / $lastWeekOrders) * 100
            : 0;

        $months    = collect(range(1, 12))->map(fn($m) => Carbon::create()->month($m)->format('M'));
        $totalData = $pendingData = $deliveredData = $canceledData = array_fill(0, 12, 0);

        $orders = Order::selectRaw('MONTH(created_at) as month, order_status, SUM(total) as total')
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->groupBy('month', 'order_status')
            ->get();

        foreach ($orders as $order) {
            $index = $order->month - 1;
            switch ($order->status) {
                case 'pending':
                    $pendingData[$index]   = (float) $order->total;
                    break;
                case 'delivered':
                    $deliveredData[$index] = (float) $order->total;
                    break;
                case 'canceled':
                    $canceledData[$index]  = (float) $order->total;
                    break;
            }
            $totalData[$index] += (float) $order->total;
        }

        return view('admin.index', compact(
            'totalOrders',
            'deliveredOrders',
            'pendingOrders',
            'cancelledOrders',
            'totalAmount',
            'deliveredAmount',
            'cancelledAmount',
            'frontendPagesCount',
            'productsCount',
            'blogsCount',
            'categoriesCount',
            'portfolioCategoriesCount',
            'totalPagesCount',
            'recentOrders',
            'thisWeekRevenue',
            'lastWeekRevenue',
            'thisWeekOrders',
            'lastWeekOrders',
            'revenueChange',
            'orderChange',
            'months',
            'totalData',
            'pendingData',
            'deliveredData',
            'canceledData'
        ));
    }

    public function totalPages()
    {
        $items = app(SitemapUrlService::class)->collect();
        $typeCounts = $items->groupBy('type')->map->count();

        return view('admin.total-pages', [
            'items' => $items,
            'frontendPagesCount' => ($typeCounts['Page'] ?? 0) + ($typeCounts['Static Page'] ?? 0),
            'productsCount' => $typeCounts['Product'] ?? 0,
            'blogsCount' => $typeCounts['Blog'] ?? 0,
            'categoriesCount' => $typeCounts['Category'] ?? 0,
            'portfolioCategoriesCount' => $typeCounts['Portfolio Category'] ?? 0,
            'totalPagesCount' => $items->count(),
        ]);
    }

    // =========================================================================
    // BRANDS  (Intervention is fine here — single image, small size)
    // =========================================================================

    public function brands(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $brands = Brand::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'DESC')
            ->paginate()
            ->withQueryString();

        return view('admin.brands', compact('brands'));
    }

    public function add_brand()
    {
        return view('admin.brand-add');
    }

    public function brand_store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'slug'  => 'required|unique:brands,slug',
            'image' => 'required|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        $brand       = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->slug);

        $image     = $request->file('image');
        $file_name = Carbon::now()->timestamp . '.' . $image->getClientOriginalExtension();
        $this->GenerateBrandThumbnailsImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand has been added successfully');
    }

    public function GenerateBrandThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        if (!File::exists($destinationPath)) File::makeDirectory($destinationPath, 0755, true);
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top')->save($destinationPath . '/' . $imageName);
        unset($img);
    }

    public function brand_edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'slug'  => 'required|unique:brands,slug,' . $request->id,
            'image' => 'nullable|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        $brand       = Brand::findOrFail($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->slug);

        if ($request->hasFile('image')) {
            $oldPath = public_path('uploads/brands/' . $brand->image);
            if ($brand->image && File::exists($oldPath)) File::delete($oldPath);

            $image     = $request->file('image');
            $file_name = Carbon::now()->timestamp . '.' . $image->getClientOriginalExtension();
            $this->GenerateBrandThumbnailsImage($image, $file_name);
            $brand->image = $file_name;
        }

        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been updated successfully');
    }

    public function brand_delete($id)
    {
        $brand   = Brand::findOrFail($id);
        $oldPath = public_path('uploads/brands/' . $brand->image);
        if ($brand->image && File::exists($oldPath)) File::delete($oldPath);
        $brand->delete();
        return redirect()->back()->with('status', 'Brand has been deleted successfully!');
    }

    public function categories(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $categories = Category::with(['parent'])
            ->withCount('products')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('meta_title', 'like', '%' . $search . '%')
                        ->orWhereHas('parent', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(30)
            ->withQueryString();

        return view('admin.category', compact('categories'));
    }

    public function category_add()
    {
        $categories = Category::all();
        return view('admin.category-add', compact('categories'));
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'name'             => 'required|min:3',
            'slug'             => 'required|unique:categories,slug',
            'image'            => 'nullable|mimes:png,jpg,jpeg,webp|max:2048',
            'meta_title'       => 'nullable|max:255|unique:categories,meta_title',
            'meta_keywords'    => 'nullable|max:255',
            'meta_description' => 'nullable',
            'show_on_home'     => 'nullable',
            'content'          => 'required',
            'page_heading'     => 'required',
            'alt_tag'          => 'nullable|string|max:255',
        ]);

        $category                    = new Category();
        $category->parent_id         = $request->parent_id;
        $category->name              = $request->name;
        $category->slug              = Str::slug($request->name);
        $category->meta_title        = $request->meta_title;
        $category->meta_keywords     = $request->meta_keywords;
        $category->meta_description  = $request->meta_description;
        $category->show_on_home      = $request->show_on_home;
        $category->content           = $request->content;
        $category->page_heading      = $request->page_heading;
        $category->image             = '';
        $category->alt_tag           = $request->alt_tag;

        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $file_name = Carbon::now()->timestamp . '.' . $image->getClientOriginalExtension();
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
            $category->image = $file_name;
        }

        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category added successfully');
    }

    public function GenerateCategoryThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        if (!File::exists($destinationPath)) File::makeDirectory($destinationPath, 0755, true);
        $img = Image::read($image->path());
        $img->save($destinationPath . '/' . $imageName);
        unset($img);
    }

    public function category_edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name'             => 'required|min:3',
            'slug'             => 'required|string|max:255|unique:categories,slug,' . $request->id,
            'image'            => 'nullable|mimes:png,jpg,jpeg,webp|max:2048',
            'meta_title'       => 'nullable|unique:categories,meta_title,' . $request->id,
            'meta_keywords'    => 'nullable|max:255',
            'meta_description' => 'nullable',
            'show_on_home'     => 'nullable',
            'content'          => 'required',
            'page_heading'     => 'required',
            'alt_tag'          => 'nullable|string|max:255',
        ]);

        $category = Category::findOrFail($request->id);

        $category->name             = $request->name;
        $category->slug             = Str::slug($request->slug ?? $request->name);
        $category->meta_title       = $request->meta_title;
        $category->meta_keywords    = $request->meta_keywords;
        $category->meta_description = $request->meta_description;
        $category->show_on_home     = $request->show_on_home ?? 1;
        $category->content          = $request->content;
        $category->page_heading     = $request->page_heading;
        $category->alt_tag          = $request->alt_tag;

        if ($request->hasFile('image')) {

            // Delete old image
            if ($category->image) {
                $oldPath = public_path('uploads/categories/' . $category->image);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $image = $request->file('image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();

            // SAVE DIRECTLY (bypass function for testing)
            $image->move(public_path('uploads/categories'), $image_name);

            $category->image = $image_name;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category updated successfully' . $category->image);
    }

    public function category_delete($id)
    {
        $category = Category::findOrFail($id);
        $oldPath  = public_path('uploads/categories/' . $category->image);
        if ($category->image && File::exists($oldPath)) File::delete($oldPath);
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category deleted successfully');
    }

    // =========================================================================
    // PRODUCTS
    // =========================================================================

    public function products(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $product = Product::with(['category', 'activeVariants:id,product_id,price'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%')
                        ->orWhere('stock_status', 'like', '%' . $search . '%')
                        ->orWhere('regular_price', 'like', '%' . $search . '%')
                        ->orWhere('sale_price', 'like', '%' . $search . '%')
                        ->orWhereHas('category', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.products', compact('product'));
    }

    public function product_add()
    {
        $categories = Category::where('status', 1)->select('id', 'name')->orderBy('name')->limit(20)->get();
        return view('admin.product-add', compact('categories'));
    }

    public function product_store(Request $request)
    {
        $validate = $this->makeProductValidator($request);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        DB::beginTransaction();

        try {

            $data = [
                'name'                      => $request->name,
                'slug'                      => Str::slug($request->slug),
                'short_description'         => $request->short_description,
                'description'               => $request->description,
                'regular_price'             => $request->regular_price,
                'sale_price'                => $request->input('sale_price'),
                'sku'                       => $request->sku,
                'quantity'                  => $request->quantity ?? 0,
                'stock_status'              => $request->stock_status ?? 1,
                'featured'                  => $request->featured ?? 0,
                'category_id'               => $request->input('category_id') ?: null,
                'type'                      => $request->purchase_type,
                'image'                     => $request->image ?: null,
                'weight'                    => $request->weight,
                'dimension'                 => $request->dimension,
                'color'                     => $request->color,
                'tags'                      => $request->tags,
                'hand_painted_details'      => $request->hand_painted_details,
                'care_instructions'         => $request->care_instructions,
                'manufacturing_details'     => $request->manufacturing_details,
                'square_banner'             => $request->square_banner,
                'square_banner_title'       => $request->square_banner_title,
                'square_banner_description' => $request->square_banner_description,
                'artisan_heading'           => $request->artisan_heading,
                'meta_title'                => $request->meta_title,
                'meta_keywords'             => $request->meta_keywords,
                'meta_description'          => $request->meta_description,
                'status'                    => $request->status ?? 1,
            ];

            $product = Product::create($data);

            // ── Gallery Images ────────────────────────────────────────────────
            // Arrives as a comma-separated string of media-library paths
            if ($request->filled('gallery')) {
                $paths = array_filter(array_map('trim', explode(',', $request->gallery)));

                foreach ($paths as $path) {
                    DB::table('product_images')->insert([
                        'product_id' => $product->id,
                        'image'      => $path,
                        'type'       => 'gallery',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // ── Artisan Gallery ───────────────────────────────────────────────
            $artisanGallery = $request->input('artisan_gallery', []);

            if (!empty($artisanGallery)) {
                foreach ($artisanGallery as $slot) {

                    // Safety cast — Laravel can return ParameterBag for nested inputs
                    if (!is_array($slot)) {
                        $slot = $slot->all();
                    }

                    $imagePath = trim($slot['image'] ?? '');
                    $hasImage  = !empty($imagePath);

                    // Preserve all line breaks — only strip trailing whitespace on description
                    $title       = isset($slot['title'])       ? trim($slot['title'])        : null;
                    $description = isset($slot['description']) ? rtrim($slot['description']) : null;
                    $hasText     = !empty($title) || !empty($description);

                    // Skip completely blank slots
                    if (!$hasImage && !$hasText) continue;

                    ProductImage::create([
                        'product_id'  => $product->id,
                        'image'       => $hasImage ? $imagePath : null,
                        'title'       => $title,
                        'description' => $description,   // line breaks stored as-is
                        'type'        => 'artisan',       // ← was missing; artisanImages() scope needs this
                    ]);
                }
            }

            // ── Attributes ────────────────────────────────────────────────────
            $attributes = $request->input('attributes', []);

            if (!empty($attributes['key'])) {
                foreach ($attributes['key'] as $i => $key) {
                    $key = trim($key);
                    if (empty($key)) continue;

                    ProductAttribute::create([
                        'product_id' => $product->id,
                        'key'        => $key,
                        'value'      => trim($attributes['value'][$i] ?? ''),
                    ]);
                }
            }

            // ── Product Icons ─────────────────────────────
            $this->syncProductVariants($product, $request->input('variants', []));

            $iconsData = [];

            foreach ($request->product_icons as $position => $icon) {
                $iconsData[] = [
                    'product_id' => $product->id,
                    'image'      => $icon['image'],
                    'text'       => $icon['text'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            ProductIcon::insert($iconsData);

            DB::commit();

            return redirect()->route('admin.products')
                ->with('success', 'Product added successfully.');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('product_store failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong. Please check the highlighted fields and try again.');
        }
    }

    // -------------------------------------------------------------------------
    // PRODUCT EDIT
    // -------------------------------------------------------------------------
    public function product_edit($id)
    {
        $product = Product::with(['galleryImages', 'artisanImages', 'productAttributes', 'variants', 'icons'])->findOrFail($id);

        $categories = Category::where('status', 1)->select('id', 'name')->orderBy('name')->limit(20)->get();

        return view('admin.product-edit', compact('product', 'categories'));
    }

    // -------------------------------------------------------------------------
    // PRODUCT UPDATE
    // -------------------------------------------------------------------------
    public function product_update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validate = $this->makeProductValidator($request, (int) $id);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        DB::beginTransaction();

        try {

            // ── 1. Core fields ────────────────────────────────────────────────
            $product->update([
                'name'                      => $request->name,
                'slug'                      => Str::slug($request->slug),
                'short_description'         => $request->short_description,
                'description'               => $request->description,
                'regular_price'             => $request->regular_price,
                'sale_price'                => $request->input('sale_price'),
                'sku'                       => $request->sku,
                'quantity'                  => $request->quantity ?? 0,
                'stock_status'              => $request->stock_status ?? 1,
                'featured'                  => $request->featured ?? 0,
                'category_id'               => $request->input('category_id') ?: null,
                'type'                      => $request->purchase_type,
                'weight'                    => $request->weight,
                'dimension'                 => $request->dimension,
                'color'                     => $request->color,
                'tags'                      => $request->tags,
                'hand_painted_details'      => $request->hand_painted_details,
                'care_instructions'         => $request->care_instructions,
                'manufacturing_details'     => $request->manufacturing_details,
                'square_banner'             => $request->square_banner ?: null,
                'square_banner_title'       => $request->square_banner_title ?: null,
                'square_banner_description' => $request->square_banner_description ?: null,
                'artisan_heading'           => $request->artisan_heading ?: null,
                'meta_title'                => $request->meta_title ?: null,
                'meta_keywords'             => $request->meta_keywords ?: null,
                'meta_description'          => $request->meta_description ?: null,
                'status'                    => $request->status ?? 1,
            ]);

            // ── 2. Main image (media library path) ────────────────────────────
            if ($request->filled('image')) {
                $product->update(['image' => $request->image]);
            }

            // ── 3. Gallery images (comma-separated paths) ─────────────────────
            if ($request->filled('gallery')) {
                $product->galleryImages()->delete();

                $paths = array_filter(array_map('trim', explode(',', $request->gallery)));
                foreach ($paths as $path) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image'      => $path,
                        'type'       => 'gallery',
                    ]);
                }
            }

            // ── 4. Artisan gallery — update or create each slot ───────────────
            $artisanGallery = $request->input('artisan_gallery', []);

            foreach ($artisanGallery as $slot) {
                if (!is_array($slot)) $slot = $slot->all();

                $slotId    = !empty($slot['id']) ? (int) $slot['id'] : null;
                $imagePath = trim($slot['image'] ?? '');
                $hasImage  = !empty($imagePath);
                $hasText   = !empty($slot['title']) || !empty($slot['description']);

                if (!$hasImage && !$hasText) {
                    if ($slotId) {
                        ProductImage::where('id', $slotId)
                            ->where('product_id', $product->id)
                            ->delete();
                    }
                    continue;
                }

                $data = [
                    'image'       => $hasImage ? $imagePath : null,
                    'title'       => $slot['title']       ?? null,
                    'description' => $slot['description'] ?? null,
                    'type'        => 'artisan',
                ];

                if ($slotId) {
                    ProductImage::where('id', $slotId)
                        ->where('product_id', $product->id)
                        ->update($data);
                } else {
                    ProductImage::create(array_merge($data, ['product_id' => $product->id]));
                }
            }

            // ── 5. Attributes — wipe then re-insert ───────────────────────────
            $product->productAttributes()->delete();

            $attributes = $request->input('attributes', []);

            if (!empty($attributes['key'])) {
                foreach ($attributes['key'] as $i => $key) {
                    $key = trim($key);
                    if (empty($key)) continue;

                    ProductAttribute::create([
                        'product_id' => $product->id,
                        'key'        => $key,
                        'value'      => trim($attributes['value'][$i] ?? ''),
                    ]);
                }
            }

            // ── 6. Product Icons ───────────────────────────
            $this->syncProductVariants($product, $request->input('variants', []));

            $product->icons()->delete();

            $iconsData = [];

            foreach ($request->product_icons as $position => $icon) {
                $iconsData[] = [
                    'product_id' => $product->id,
                    'image'      => $icon['image'],
                    'text'       => $icon['text'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            ProductIcon::insert($iconsData);

            DB::commit();

            return redirect()->route('admin.products')->with('status', 'Product updated successfully.');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('product_update failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong. Please check the highlighted fields and try again.');
        }
    }

    // -------------------------------------------------------------------------
    // PRODUCT DELETE
    // -------------------------------------------------------------------------
    public function product_delete($id)
    {
        $product = Product::with('images')->findOrFail($id);

        // Main image
        if ($product->image) {
            $path = public_path('uploads/products/' . $product->image);
            if (File::exists($path)) File::delete($path);
        }

        // Gallery + artisan images
        foreach ($product->images as $img) {
            foreach (['uploads/products/gallery/', 'uploads/products/artisan/'] as $folder) {
                $path = public_path($folder . $img->image);
                if (File::exists($path)) File::delete($path);
            }
        }

        $product->images()->delete();
        $product->productAttributes()->delete();
        $product->delete();

        return redirect()->route('admin.products')->with('status', 'Product deleted successfully');
    }

    // -------------------------------------------------------------------------
    // DELETE SINGLE GALLERY IMAGE (AJAX)
    // -------------------------------------------------------------------------
    public function deleteProductImage(Request $request)
    {
        $image = ProductImage::findOrFail($request->image_id);

        foreach (['uploads/products/gallery/', 'uploads/products/artisan/'] as $folder) {
            $path = public_path($folder . $image->image);
            if (File::exists($path)) File::delete($path);
        }

        $image->delete();

        return response()->json(['success' => true]);
    }

    // =========================================================================
    // ORDERS
    // =========================================================================

    public function orders(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $orderStatus = trim((string) $request->get('order_status', 'confirmed'));
        $paymentStatus = trim((string) $request->get('payment_status', ''));
        $paymentMethod = trim((string) $request->get('payment_method', ''));

        $allowedOrderStatuses = ['all', 'pending', 'confirmed', 'packed', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($orderStatus, $allowedOrderStatuses, true)) {
            $orderStatus = 'confirmed';
        }

        $allowedPaymentStatuses = ['', 'paid', 'pending'];
        if (!in_array($paymentStatus, $allowedPaymentStatuses, true)) {
            $paymentStatus = '';
        }

        $allowedPaymentMethods = ['', 'razorpay', 'offline', 'cod', 'bank_transfer'];
        if (!in_array($paymentMethod, $allowedPaymentMethods, true)) {
            $paymentMethod = '';
        }

        $orders = Order::query()
            ->when($orderStatus !== 'all', function ($query) use ($orderStatus) {
                $query->where('order_status', $orderStatus);
            })
            ->when($paymentStatus !== '', function ($query) use ($paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            })
            ->when($paymentMethod !== '', function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('total', 'like', '%' . $search . '%')
                        ->orWhere('order_status', 'like', '%' . $search . '%')
                        ->orWhere('payment_status', 'like', '%' . $search . '%')
                        ->orWhere('payment_method', 'like', '%' . $search . '%')
                        ->orWhere('razorpay_order_id', 'like', '%' . $search . '%')
                        ->orWhere('razorpay_payment_id', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders', compact('orders', 'orderStatus', 'paymentStatus', 'paymentMethod'));
    }

    public function orders_detail($id)
    {
        $orders = Order::with('items')->findOrFail($id);
        return view('admin.order-detail', compact('orders'));
    }

    public function order_invoice($id)
    {
        return $this->generateOrderInvoice($id, 'view');
    }

    public function order_invoice_download($id)
    {
        return $this->generateOrderInvoice($id, 'download');
    }

    private function generateOrderInvoice($id, $type = 'view')
    {
        $order = Order::with('items')->findOrFail($id);

        $pdf = Pdf::loadView('user.invoice', compact('order'))
            ->setPaper('A4', 'portrait');

        if ($type === 'view') {
            return $pdf->stream('invoice-'.$order->id.'.pdf');
        }

        return $pdf->download('invoice-'.$order->id.'.pdf');
    }

    public function updateStatus(Request $request, $id)
    {
        $order  = Order::with('items')->findOrFail($id);
        $status = $request->order_status;

        if ($status === 'packed' && !$order->shiprocket_order_id) {

            $request->validate([
                'length'  => 'required|numeric|min:0.1',
                'breadth' => 'required|numeric|min:0.1',
                'height'  => 'required|numeric|min:0.1',
                'weight'  => 'required|numeric|min:1', // grams, min 1g
            ]);

            // ✅ Convert grams → kg once here, pass to all Shiprocket calls
            $weightInKg = $request->weight / 1000;

            try {

                // 1️⃣ Create Shiprocket Order
                $created = $this->shiprocket->createOrder($order, [
                    'length'  => $request->length,
                    'breadth' => $request->breadth,
                    'height'  => $request->height,
                    'weight'  => $weightInKg, // ✅ kg — createOrder no longer converts
                ]);

                $order->shiprocket_order_id    = $created['order_id'] ?? null;
                $order->shiprocket_shipment_id = $created['shipment_id'] ?? null;

                if (!$order->shiprocket_shipment_id) {
                    throw new \Exception('Shipment ID not received from Shiprocket.');
                }

                // 2️⃣ Check Courier Serviceability
                $pickupPincode = "125001";

                $serviceability = $this->shiprocket->checkServiceability(
                    $pickupPincode,
                    $order->pincode,
                    $weightInKg // ✅ kg
                );

                $couriers = $serviceability['couriers'] ?? [];

                if (empty($couriers)) {
                    throw new \Exception('No courier available for this pincode.');
                }

                // 3️⃣ Filter COD couriers (if COD order)
                if (
                    isset($order->payment_method) &&
                    strtolower($order->payment_method) === 'cod'
                ) {
                    $couriers = collect($couriers)
                        ->filter(fn($courier) => ($courier['cod'] ?? 0) == 1)
                        ->values()
                        ->toArray();

                    if (empty($couriers)) {
                        throw new \Exception('No COD courier available for this pincode.');
                    }
                }

                // 4️⃣ Sort: express = fastest, standard = cheapest
                usort($couriers, function ($a, $b) use ($order) {
                    if ($order->delivery_type === 'express') {
                        return ($a['estimated_delivery_days'] ?? 999)
                            <=> ($b['estimated_delivery_days'] ?? 999);
                    }
                    return ($a['total_charge'] ?? 999999)
                        <=> ($b['total_charge'] ?? 999999);
                });

                // 5️⃣ Try couriers one-by-one until AWB is assigned
                $awbAssigned     = false;
                $awb             = null;
                $selectedCourier = null;
                $assignErrors    = [];

                foreach ($couriers as $courier) {
                    try {

                        $awbResponse = $this->shiprocket->assignCourier(
                            $order->shiprocket_shipment_id,
                            $courier['courier_company_id']
                        );

                        $awb             = $awbResponse;
                        $selectedCourier = $courier;
                        $awbAssigned     = true;
                        break;

                    } catch (\Throwable $e) {
                        $assignErrors[] = ($courier['courier_name'] ?? 'Unknown') . ': ' . $e->getMessage();
                        continue;
                    }
                }

                if (!$awbAssigned) {
                    throw new \Exception(
                        'All couriers failed. ' . implode(' | ', $assignErrors)
                    );
                }

                // 6️⃣ Save AWB + package details
                $order->awb_code     = $awb['awb_code'];
                $order->courier_name = $awb['courier_name'] ?? $selectedCourier['courier_name'] ?? null;
                $order->delivery_eta = $selectedCourier['estimated_delivery_days'] ?? null;

                $order->package_length  = $request->length;
                $order->package_breadth = $request->breadth;
                $order->package_height  = $request->height;
                $order->package_weight  = $request->weight; // ✅ stored as grams

            } catch (\Throwable $e) {
                return back()->with('error', 'Shiprocket Error: ' . $e->getMessage());
            }
        }

        // Stamp delivery time
        if ($status === 'delivered' && !$order->delivered_at) {
            $order->delivered_at = now();
        }

        $order->order_status = $status;
        $order->save();

        return back()->with('status', 'Order processed successfully 🚀');
    }

    // ─────────────────────────────────────────────
    // Reset Shiprocket — clears saved IDs so order
    // can be re-pushed after cancellation
    // ─────────────────────────────────────────────
    public function resetShiprocket($id)
    {
        $order = Order::findOrFail($id);

        $order->shiprocket_order_id    = null;
        $order->shiprocket_shipment_id = null;
        $order->awb_code               = null;
        $order->courier_name           = null;
        $order->delivery_eta           = null;
        $order->package_length         = null;
        $order->package_breadth        = null;
        $order->package_height         = null;
        $order->package_weight         = null;
        $order->save();

        return back()->with('status', 'Shiprocket details cleared. You can now re-assign shipment.');
    }

    // =========================================================================
    // BLOGS  (Intervention kept — blog images are always single uploads)
    // =========================================================================

    public function blogs(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $blogs = Blog::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('content', 'like', '%' . $search . '%')
                        ->orWhere('meta_title', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.blogs', compact('blogs'));
    }

    public function blog_add()
    {
        return view('admin.blog-add');
    }

    public function blog_store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'content'          => 'nullable|string',
            'tags'             => 'nullable|string',
            'image'            => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'meta_title'       => 'nullable|max:255',
            'meta_keywords'    => 'nullable|max:255',
            'meta_description' => 'nullable',
            'author'           => 'required|string|max:255',
            'alt_tag'          => 'nullable|string|max:255',
        ]);

        $image = $request->file('image');
        $file_name = time() . '.' . $image->getClientOriginalExtension();

        // Upload original image as-is
        $image->move(public_path('uploads/blogs'), $file_name);

        $blog = Blog::create([
            'title'            => $request->title,
            'slug'             => $request->slug,
            'content'          => $request->content,
            'image'            => $file_name,
            'meta_title'       => $request->meta_title,
            'meta_keywords'    => $request->meta_keywords,
            'meta_description' => $request->meta_description,
            'author'           => $request->author,
            'alt_tag'          => $request->alt_tag,
        ]);

        $tagIds = collect(explode(',', $request->tags ?? ''))
            ->filter()
            ->map(function ($tagName) {
                return Tag::firstOrCreate(
                    ['slug' => Str::slug(trim($tagName))],
                    ['name' => trim($tagName)]
                )->id;
            });

        $blog->tags()->sync($tagIds);

        return redirect()->route('admin.blogs')->with('status', 'Blog created successfully');
    }

    public function blog_edit($id)
    {
        $blog = Blog::findOrFail($id);
        return view('admin.blog-edit', compact('blog'));
    }

    public function blog_update(Request $request, Blog $blog)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'required|string|max:255',
            'content'          => 'nullable|string',
            'tags'             => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'meta_title'       => 'nullable|max:255',
            'meta_keywords'    => 'nullable|max:255',
            'meta_description' => 'nullable',
            'author'           => 'required|string|max:255',
            'alt_tag'          => 'nullable|string|max:255',
        ]);

        $imageName = $blog->image;

        if ($request->hasFile('image')) {

            $oldImage = public_path('uploads/blogs/' . $blog->image);

            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Upload original image as-is
            $image->move(public_path('uploads/blogs'), $imageName);
        }

        $blog->update([
            'title'            => $request->title,
            'slug'             => $request->slug,
            'content'          => $request->content,
            'image'            => $imageName,
            'meta_title'       => $request->meta_title,
            'meta_keywords'    => $request->meta_keywords,
            'meta_description' => $request->meta_description,
            'author'           => $request->author,
            'alt_tag'          => $request->alt_tag,
        ]);

        $tagIds = collect(explode(',', $request->tags ?? ''))
            ->filter()
            ->map(function ($tagName) {
                return Tag::firstOrCreate(
                    ['slug' => Str::slug(trim($tagName))],
                    ['name' => trim($tagName)]
                )->id;
            });

        $blog->tags()->sync($tagIds);

        return redirect()->route('admin.blogs')->with('success', 'Blog updated successfully');
    }

    public function GenerateBlogThumbnailsImage($image, $imageName)
    {
        $destinationPath       = public_path('uploads/blogs/');
        $destinationPathThumbs = public_path('uploads/blogs/thumbnails/');

        if (!File::exists($destinationPath))       File::makeDirectory($destinationPath, 0755, true);
        if (!File::exists($destinationPathThumbs)) File::makeDirectory($destinationPathThumbs, 0755, true);

        $img = Image::read($image->path());
        $img->scaleDown(850, 478)->save($destinationPath . $imageName);
        unset($img);

        $imgThumb = Image::read($image->path());
        $imgThumb->cover(250, 250, 'top')->save($destinationPathThumbs . $imageName);
        unset($imgThumb);
    }

    public function BlogToggleStatus($id)
    {
        $blog       = Blog::findOrFail($id);
        $blog->status = $blog->status === 1 ? 0 : 1;
        $blog->save();
        $statusText = $blog->status === 1 ? 'activated' : 'deactivated';
        return redirect()->back()->with('status', "Blog has been {$statusText} successfully.");
    }

    public function BlogDelete($id)
    {
        Blog::findOrFail($id)->delete();
        return redirect()->back()->with('status', 'Blog has been deleted successfully.');
    }

    public function importblogscsv()
    {
        return view('admin.blog-import');
    }

    public function importBlogs(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt']);
        Excel::import(new BlogImport, $request->file('file'));
        return back()->with('status', 'Blogs imported successfully.');
    }

    // =========================================================================
    // TESTIMONIALS
    // =========================================================================

    public function testimonials(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $testimonials = Testimonial::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('testimonial', 'like', '%' . $search . '%')
                        ->orWhere('stars', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.testimonials', compact('testimonials'));
    }

    public function testimonial_add()
    {
        return view('admin.testimonial-add');
    }

    public function testimonial_store(Request $request)
    {
        $request->validate([
            'name'        => 'required|min:3',
            'testimonial' => 'required|string',
            'stars'       => 'required|integer|min:1|max:5',
            'image'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|in:0,1',
            'alt_tag'     => 'nullable|string|max:255',
        ]);

        $imageName = null;

        // Upload Image
        if ($request->hasFile('image')) {

            $image      = $request->file('image');
            $imageName  = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            $destinationPath = public_path('uploads/testimonials');

            // Create folder if not exists
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $image->move($destinationPath, $imageName);
        }

        Testimonial::create([
            'name'        => $request->name,
            'testimonial' => $request->testimonial,
            'stars'       => $request->stars,
            'image'       => 'uploads/testimonials/' . $imageName,
            'status'      => $request->status,
            'alt_tag'     => $request->alt_tag,
        ]);

        return redirect()->route('admin.testimonials')->with('status', 'Testimonial added successfully.');
    }

    public function testimonial_edit($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        return view('admin.testimonial-edit', compact('testimonial'));
    }

    public function testimonial_update(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $request->validate([
            'name'        => 'required|min:3',
            'testimonial' => 'required|string',
            'stars'       => 'required|integer|min:1|max:5',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'      => 'required|in:0,1',
            'alt_tag'     => 'nullable|string|max:255',
        ]);

        $imagePath = $testimonial->image;

        // Upload New Image
        if ($request->hasFile('image')) {

            // Delete old image
            if ($testimonial->image && File::exists(public_path($testimonial->image))) {
                File::delete(public_path($testimonial->image));
            }

            $image = $request->file('image');

            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            $destinationPath = public_path('uploads/testimonials');

            // Create folder if not exists
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $image->move($destinationPath, $imageName);

            $imagePath = 'uploads/testimonials/' . $imageName;
        }

        $testimonial->update([
            'name'        => $request->name,
            'testimonial' => $request->testimonial,
            'stars'       => $request->stars,
            'image'       => $imagePath,
            'status'      => $request->status,
            'alt_tag'     => $request->alt_tag
        ]);

        return redirect()
            ->route('admin.testimonials')
            ->with('status', 'Testimonial updated successfully');
    }

    public function testimonial_delete($id)
    {
        Testimonial::findOrFail($id)->delete();
        return redirect()->route('admin.testimonials')->with('status', 'Testimonial deleted successfully');
    }

    public function stories()
    {
        $stories = Story::orderBy('display_order', 'ASC')->get();

        return view('admin.story', compact('stories'));
    }

    public function story_add()
    {
        return view('admin.stories-add');
    }

    public function story_store(Request $request)
    {
        $request->validate([
            'year'          => 'required|digits:4',
            'description'   => 'required',
            'image'         => 'required|image|mimes:jpg,jpeg,png,webp',
            'display_order' => 'required|integer',
            'status'        => 'required',
            'alt_tag'       => 'nullable|string|max:255',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {

            $image = $request->file('image');

            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();

            $destination = public_path('uploads/stories');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $image->move($destination, $imageName);

            $imagePath = 'uploads/stories/'.$imageName;
        }

        Story::create([
            'year'          => $request->year,
            'description'   => $request->description,
            'image'         => $imagePath,
            'display_order' => $request->display_order,
            'status'        => $request->status,
            'alt_tag'       => $request->alt_tag
        ]);

        return redirect()
            ->route('admin.stories')
            ->with('status', 'Story added successfully');
    }

    public function story_edit($id)
    {
        $story = Story::findOrFail($id);

        return view('admin.stories-edit', compact('story'));
    }

    public function story_update(Request $request, $id)
    {
        $story = Story::findOrFail($id);

        $request->validate([
            'year'          => 'required|digits:4',
            'description'   => 'required',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'display_order' => 'required|integer',
            'status'        => 'required',
            'alt_tag'       => 'nullable|string|max:255',
        ]);

        $imagePath = $story->image;

        if ($request->hasFile('image')) {

            if ($story->image && File::exists(public_path($story->image))) {
                File::delete(public_path($story->image));
            }

            $image = $request->file('image');

            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();

            $destination = public_path('uploads/stories');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $image->move($destination, $imageName);

            $imagePath = 'uploads/stories/'.$imageName;
        }

        $story->update([
            'year'          => $request->year,
            'description'   => $request->description,
            'image'         => $imagePath,
            'display_order' => $request->display_order,
            'status'        => $request->status,
            'alt_tag'       => $request->alt_tag
        ]);

        return redirect()
            ->route('admin.stories')
            ->with('status', 'Story updated successfully');
    }

    public function story_delete($id)
    {
        $story = Story::findOrFail($id);

        if ($story->image && File::exists(public_path($story->image))) {
            File::delete(public_path($story->image));
        }

        $story->delete();

        return redirect()
            ->route('admin.stories')
            ->with('status', 'Story deleted successfully');
    }

    public function about_section()
    {
        $section = AboutSection::first();
        $valueItems = $section ? $section->display_value_items : AboutSection::defaultValueItems();

        return view('admin.about-section', compact('section', 'valueItems'));
    }

    public function about_section_update(Request $request)
    {
        $request->validate([
            'heading'                 => 'required',
            'description'             => 'required',
            'signature'               => 'nullable',
            'image'                   => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'alt_tag'                 => 'nullable|string|max:255',
            'value_titles'            => 'required|array|min:1',
            'value_titles.*'          => 'required|string|max:255',
            'value_descriptions'      => 'required|array|min:1',
            'value_descriptions.*'    => 'required|string',
            'value_alts'              => 'nullable|array',
            'value_alts.*'            => 'nullable|string|max:255',
            'existing_value_icons'    => 'nullable|array',
            'existing_value_icons.*'  => 'nullable|string|max:255',
            'value_icons'             => 'nullable|array',
            'value_icons.*'           => 'nullable|file|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ]);

        $section = AboutSection::first();

        // Create if not exists
        if (!$section) {
            $section = new AboutSection();
        }

        $imagePath = $section->image;

        // Upload Image
        if ($request->hasFile('image')) {

            // Delete old image
            if ($section->image && File::exists(public_path($section->image))) {
                File::delete(public_path($section->image));
            }

            $image = $request->file('image');

            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();

            $destination = public_path('uploads/about-section');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $image->move($destination, $imageName);

            $imagePath = 'uploads/about-section/'.$imageName;
        }

        $section->heading = $request->heading;
        $section->description = $request->description;
        $section->signature = $request->signature;
        $section->image = $imagePath;
        $section->alt_tag = $request->alt_tag;
        $section->value_items = $this->buildAboutValueItems($request);

        $section->save();

        return redirect()
            ->back()
            ->with('status', 'About section updated successfully');
    }

    private function buildAboutValueItems(Request $request): array
    {
        $items = [];
        $titles = $request->input('value_titles', []);
        $descriptions = $request->input('value_descriptions', []);
        $alts = $request->input('value_alts', []);
        $existingIcons = $request->input('existing_value_icons', []);

        foreach ($titles as $index => $title) {
            $iconPath = $existingIcons[$index] ?? '';
            $alt = trim((string) ($alts[$index] ?? '')) ?: $title;

            if ($request->hasFile("value_icons.$index")) {
                $oldIcon = $iconPath;
                $icon = $request->file("value_icons.$index");
                $iconName = time().'_'.$index.'_'.uniqid().'.'.$icon->getClientOriginalExtension();
                $destination = public_path('uploads/about-section-values');

                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0755, true);
                }

                $icon->move($destination, $iconName);
                $iconPath = 'uploads/about-section-values/'.$iconName;

                if ($oldIcon && Str::startsWith($oldIcon, 'uploads/about-section-values/') && File::exists(public_path($oldIcon))) {
                    File::delete(public_path($oldIcon));
                }
            }

            $items[] = [
                'icon' => $iconPath,
                'alt' => $alt,
                'title' => $title,
                'description' => $descriptions[$index] ?? '',
            ];
        }

        return $items;
    }

    private function productValidationRules(?int $productId = null): array
    {
        $artisanIdRules = $productId
            ? [
                'nullable',
                'integer',
                Rule::exists('product_images', 'id')->where(function ($query) use ($productId) {
                    $query->where('product_id', $productId)
                        ->where('type', 'artisan');
                }),
            ]
            : ['prohibited'];

        return [
            'name'                          => 'required|string|max:255',
            'slug'                          => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'short_description'             => 'required|string|max:255',
            'description'                   => 'required|string',
            'regular_price'                 => ['required', 'numeric', 'min:0', 'max:99999999.99', 'regex:/^(?:0|[1-9]\d{0,7})(?:\.\d{1,2})?$/'],
            'sale_price'                    => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'lte:regular_price', 'regex:/^(?:0|[1-9]\d{0,7})(?:\.\d{1,2})?$/'],
            'sku'                           => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'quantity'                      => 'required|integer|min:0|max:2147483647',
            'category_id'                   => 'required|integer|exists:categories,id',
            'purchase_type'                 => 'required|in:1,2',
            'image'                         => 'required|string|max:500',
            'gallery'                       => 'required|string',
            'artisan_gallery'               => 'nullable|array',
            'artisan_gallery.*'             => 'nullable|array',
            'artisan_gallery.*.image'       => 'nullable|string|max:500',
            'artisan_gallery.*.title'       => 'nullable|string|max:255',
            'artisan_gallery.*.description' => 'nullable|string|max:1000',
            'artisan_gallery.*.id'          => $artisanIdRules,
            'attributes'                    => 'required|array',
            'attributes.key'                => 'required|array',
            'attributes.key.*'              => 'required|string|max:255',
            'attributes.value.*'            => 'nullable|string|max:255',
            'variants'                      => 'nullable|array',
            'variants.*.size'               => 'nullable|string|max:100',
            'variants.*.fabric_type'        => 'nullable|string|max:150',
            'variants.*.sku'                => 'nullable|string|max:255',
            'variants.*.price'              => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'regex:/^(?:0|[1-9]\d{0,7})(?:\.\d{1,2})?$/'],
            'variants.*.quantity'           => 'nullable|integer|min:0|max:2147483647',
            'variants.*.is_active'          => 'nullable|in:0,1',
            'weight'                        => ['nullable', 'numeric', 'min:0', 'max:99999.999', 'regex:/^(?:0|[1-9]\d{0,4})(?:\.\d{1,3})?$/'],
            'dimension'                     => 'nullable|string|max:255',
            'color'                         => 'nullable|string|max:100',
            'tags'                          => 'nullable|string|max:500',
            'hand_painted_details'          => 'nullable|string',
            'care_instructions'             => 'nullable|string',
            'manufacturing_details'         => 'nullable|string',
            'square_banner'                 => 'nullable|string|max:500',
            'square_banner_title'           => 'nullable|string|max:255',
            'square_banner_description'     => 'nullable|string|max:1000',
            'artisan_heading'               => 'nullable|string|max:255',
            'meta_title'                    => 'nullable|string|max:255',
            'meta_keywords'                 => 'nullable|string',
            'meta_description'              => 'nullable|string',
            'product_icons'                 => 'required|array|min:6|max:6',
            'product_icons.*.image'         => 'required|string|max:500',
            'product_icons.*.text'          => 'required|string|max:255',
            'stock_status'                  => 'nullable|in:0,1',
            'status'                        => 'nullable|in:0,1',
            'featured'                      => 'nullable|in:0,1,2',
        ];
    }

    private function productValidationMessages(): array
    {
        return [
            'name.required'              => 'Product name is required.',
            'slug.required'              => 'Slug is required.',
            'slug.unique'                => 'This slug is already used by another product.',
            'short_description.required' => 'Sub title is required.',
            'description.required'       => 'Description is required.',
            'regular_price.required'     => 'Regular price is required.',
            'regular_price.min'          => 'Regular price cannot be negative.',
            'regular_price.max'          => 'Regular price cannot exceed 99,999,999.99.',
            'regular_price.regex'        => 'Regular price must have no more than 2 decimal places.',
            'sale_price.max'             => 'Sale price cannot exceed 99,999,999.99.',
            'sale_price.regex'           => 'Sale price must have no more than 2 decimal places.',
            'sale_price.lte'             => 'Sale price must be less than or equal to regular price.',
            'sku.unique'                 => 'This SKU is already used by another product.',
            'quantity.integer'           => 'Quantity must be a whole number.',
            'quantity.max'               => 'Quantity is too large.',
            'weight.numeric'             => 'Weight must be a number only. Use kilograms, for example 0.500 instead of 500g.',
            'weight.max'                 => 'Weight cannot exceed 99,999.999 kg.',
            'weight.regex'               => 'Weight must have no more than 3 decimal places.',
            'variants.*.price.max'       => 'Variant price cannot exceed 99,999,999.99.',
            'variants.*.price.regex'     => 'Variant price must have no more than 2 decimal places.',
            'variants.*.quantity.integer'=> 'Variant quantity must be a whole number.',
            'variants.*.quantity.max'    => 'Variant quantity is too large.',
            'category_id.required'       => 'Please select a category.',
            'category_id.exists'         => 'Selected category is invalid.',
            'product_icons.required'     => 'All 6 product icons are required.',
            'product_icons.min'          => 'All 6 product icons are required.',
            'product_icons.*.image.required' => 'Each product icon must have an image.',
            'product_icons.*.text.required'  => 'Each product icon must have text.',
            'artisan_gallery.*.id.prohibited' => 'Artisan gallery IDs are only allowed while editing a product.',
            'artisan_gallery.*.id.exists'     => 'Selected artisan gallery image is invalid for this product.',
        ];
    }

    private function makeProductValidator(Request $request, ?int $productId = null)
    {
        $request->merge($this->normalizeProductInput($request->all()));

        $validator = Validator::make(
            $request->all(),
            $this->productValidationRules($productId),
            $this->productValidationMessages()
        );

        $validator->after(function ($validator) use ($request, $productId) {
            $this->validateArtisanGallerySlots($validator, $request);

            $variants = $request->input('variants', []);
            $seenSkus = [];

            foreach ($variants as $variant) {
                if (!is_array($variant)) {
                    continue;
                }

                $size = trim((string) ($variant['size'] ?? ''));
                $fabricType = trim((string) ($variant['fabric_type'] ?? ''));
                $sku = trim((string) ($variant['sku'] ?? ''));
                $price = $variant['price'] ?? null;

                $hasContent = $size !== ''
                    || $fabricType !== ''
                    || $sku !== ''
                    || ($price !== null && $price !== '');

                if (!$hasContent) {
                    continue;
                }

                if ($sku === '' || $price === null || $price === '') {
                    $validator->errors()->add('variants', 'Every variant row must have a SKU and price.');
                    return;
                }

                $skuKey = strtolower($sku);
                if (isset($seenSkus[$skuKey])) {
                    $validator->errors()->add('variants', "Duplicate variant SKU found: {$sku}.");
                    return;
                }
                $seenSkus[$skuKey] = true;

                $query = ProductVariant::where('sku', $sku);
                if ($productId) {
                    $query->where('product_id', '!=', $productId);
                }

                if ($query->exists()) {
                    $validator->errors()->add('variants', "Variant SKU {$sku} is already used by another product.");
                }
            }
        });

        return $validator;
    }

    private function normalizeProductInput(array $input): array
    {
        foreach ([
            'name', 'short_description', 'sku', 'image', 'gallery', 'weight', 'dimension',
            'color', 'tags', 'square_banner', 'square_banner_title', 'artisan_heading',
            'meta_title', 'meta_keywords', 'regular_price', 'sale_price', 'quantity',
        ] as $field) {
            if (array_key_exists($field, $input)) {
                $input[$field] = $this->emptyStringToNull($input[$field]);
            }
        }

        if (array_key_exists('slug', $input)) {
            $input['slug'] = Str::slug((string) $input['slug']);
        }

        foreach (['attributes', 'variants', 'artisan_gallery', 'product_icons'] as $field) {
            if (isset($input[$field]) && is_array($input[$field])) {
                $input[$field] = $this->trimNestedStrings($input[$field]);
            }
        }

        return $input;
    }

    private function trimNestedStrings(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $values[$key] = $this->trimNestedStrings($value);
                continue;
            }

            if (is_string($value)) {
                $values[$key] = $this->emptyStringToNull($value);
            }
        }

        return $values;
    }

    private function emptyStringToNull($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function validateArtisanGallerySlots($validator, Request $request): void
    {
        $artisanGallery = $request->input('artisan_gallery', []);

        if (!is_array($artisanGallery)) {
            return;
        }

        foreach ($artisanGallery as $index => $slot) {
            if (!is_array($slot)) {
                continue;
            }

            $image = trim((string) ($slot['image'] ?? ''));
            $title = trim((string) ($slot['title'] ?? ''));
            $description = trim((string) ($slot['description'] ?? ''));

            if ($image === '' && $title === '' && $description === '') {
                continue;
            }

            if ($image === '') {
                $validator->errors()->add(
                    "artisan_gallery.{$index}.image",
                    "Artisan gallery slot {$index} image is required when the slot has content."
                );
            }

            if ($title === '') {
                $validator->errors()->add(
                    "artisan_gallery.{$index}.title",
                    "Artisan gallery slot {$index} title is required when the slot has content."
                );
            }

            if ($description === '') {
                $validator->errors()->add(
                    "artisan_gallery.{$index}.description",
                    "Artisan gallery slot {$index} description is required when the slot has content."
                );
            }
        }
    }

    private function syncProductVariants(Product $product, array $variants): void
    {
        $rows = [];
        $seenSkus = [];

        foreach ($variants as $variant) {
            if (!is_array($variant)) {
                continue;
            }

            $size = trim((string) ($variant['size'] ?? ''));
            $fabricType = trim((string) ($variant['fabric_type'] ?? ''));
            $sku = trim((string) ($variant['sku'] ?? ''));
            $price = $variant['price'] ?? null;

            if ($size === '' && $fabricType === '' && $sku === '' && ($price === null || $price === '')) {
                continue;
            }

            if ($sku === '' || $price === null || $price === '') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'variants' => 'Every variant row must have a SKU and price.',
                ]);
            }

            if (isset($seenSkus[strtolower($sku)])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'variants' => "Duplicate variant SKU found: {$sku}.",
                ]);
            }

            $seenSkus[strtolower($sku)] = true;

            $skuExists = ProductVariant::where('sku', $sku)
                ->where('product_id', '!=', $product->id)
                ->exists();

            if ($skuExists) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'variants' => "Variant SKU {$sku} is already used by another product.",
                ]);
            }

            $rows[] = [
                'product_id' => $product->id,
                'size' => $size ?: null,
                'fabric_type' => $fabricType ?: null,
                'sku' => $sku,
                'price' => $price,
                'quantity' => (int) ($variant['quantity'] ?? 0),
                'is_active' => (bool) ($variant['is_active'] ?? true),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $product->variants()->delete();

        if (!empty($rows)) {
            ProductVariant::insert($rows);
        }
    }

    // =========================================================================
    // MISC
    // =========================================================================

    public function users(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $users = User::query()
            ->where('utype', '!=', 'ADM')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('mobile', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function contactquery(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $contactQueries = Contact::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('mobile', 'like', '%' . $search . '%')
                        ->orWhere('subject', 'like', '%' . $search . '%')
                        ->orWhere('message', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.contact-queries', compact('contactQueries'));
    }

    public function contactquerydelete($id)
    {
        Contact::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Contact query deleted successfully');
    }

    public function askquestions(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $askedQuestions = AskQuestion::with(['product', 'user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('mobile', 'like', '%' . $search . '%')
                        ->orWhere('product_name', 'like', '%' . $search . '%')
                        ->orWhere('message', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.asked-questions', compact('askedQuestions'));
    }

    public function askquestion_delete($id)
    {
        AskQuestion::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Ask question deleted successfully');
    }

    public function subscribers(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $subscribers = Subscribe::with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('email', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.subscribers', compact('subscribers'));
    }

    public function sliders()
    {
        $sliders = Sliders::orderBy('id', 'desc')->paginate(10);
        return view('admin.sliders', compact('sliders'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
