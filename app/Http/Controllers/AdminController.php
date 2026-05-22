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
use App\Services\ShiprocketService;
use App\Models\AboutSection;

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
        $totalOrders     = Order::count();
        $recentOrders    = Order::where('payment_status', 'paid')->with('items')->orderBy('created_at', 'desc')->take(5)->get();
        $deliveredOrders = Order::where('order_status', 'delivered')->count();
        $pendingOrders   = Order::where('order_status', 'pending')->count();
        $cancelledOrders = Order::where('order_status', 'cancelled')->count();
        $totalAmount     = Order::sum('total');
        $deliveredAmount = Order::where('order_status', 'delivered')->sum('total');
        $pendingAmount   = Order::where('order_status', 'pending')->sum('total');
        $cancelledAmount = Order::where('order_status', 'cancelled')->sum('total');

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
            'pendingAmount',
            'cancelledAmount',
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

    // =========================================================================
    // BRANDS  (Intervention is fine here — single image, small size)
    // =========================================================================

    public function brands()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate();
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
            ->paginate(10)
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
        ]);

        $category                    = new Category();
        $category->parent_id         = $request->parent_id;
        $category->name              = $request->name;
        $category->slug              = Str::slug($request->name);
        $category->meta_title        = $request->meta_title;
        $category->meta_keywords     = $request->meta_keywords;
        $category->meta_description  = $request->meta_description;
        $category->show_on_home      = $request->show_on_home;
        $category->image             = '';

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
        ]);

        $category = Category::findOrFail($request->id);

        $category->name = $request->name;
        $category->slug = Str::slug($request->slug ?? $request->name);
        $category->meta_title = $request->meta_title;
        $category->meta_keywords = $request->meta_keywords;
        $category->meta_description = $request->meta_description;
        $category->show_on_home = $request->show_on_home ?? 1;

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

        $product = Product::with('category')
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
        $validate = Validator::make($request->all(), [
            'name'                          => 'required|string|max:255',
            'slug'                          => 'required|string|max:255',
            'short_description'             => 'nullable|string',
            'description'                   => 'required|string',
            'regular_price'                 => 'required|numeric',
            'sale_price'                    => 'nullable|numeric',
            'sku'                           => 'nullable|string|max:255',
            'quantity'                      => 'nullable|integer|min:0',
            'category_id'                   => 'nullable|integer',
            'image'                         => 'nullable|string',
            'gallery'                       => 'nullable|string',
            'artisan_gallery.*.image'       => 'nullable|string',
            'artisan_gallery.*.title'       => 'nullable|string|max:255',
            'artisan_gallery.*.description' => 'nullable|string',
            'attributes.key.*'              => 'nullable|string|max:255',
            'attributes.value.*'            => 'nullable|string|max:255',
            'weight'                        => 'nullable|string|max:100',
            'dimension'                     => 'nullable|string|max:100',
            'color'                         => 'nullable|string|max:100',
            'tags'                          => 'nullable|string|max:500',
            'hand_painted_details'          => 'nullable|string',
            'care_instructions'             => 'nullable|string',
            'manufacturing_details'         => 'nullable|string',
            'artisan_heading'               => 'nullable|string|max:255',
            'meta_title'                    => 'nullable|string|max:255',
            'meta_keywords'                 => 'nullable|string|max:255',
            'meta_description'              => 'nullable|string',
            'product_icons'                 => 'required|array|size:6',
            'product_icons.*.image'         => 'required|string',
            'product_icons.*.text'          => 'required|string|max:255',
        ]);

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
                'sale_price'                => $request->sale_price ?: null,
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
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('product_store failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // PRODUCT EDIT
    // -------------------------------------------------------------------------
    public function product_edit($id)
    {
        $product = Product::with(['galleryImages', 'artisanImages', 'productAttributes','icons'])->findOrFail($id);

        $categories = Category::where('status', 1)->select('id', 'name')->orderBy('name')->limit(20)->get();

        return view('admin.product-edit', compact('product', 'categories'));
    }

    // -------------------------------------------------------------------------
    // PRODUCT UPDATE
    // -------------------------------------------------------------------------
    public function product_update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'                          => 'required|string|max:255',
            'slug'                          => 'required|string|max:255',
            'short_description'             => 'nullable|string',
            'description'                   => 'required|string',
            'regular_price'                 => 'required|numeric',
            'sale_price'                    => 'nullable|numeric',
            'sku'                           => 'nullable|string|max:255',
            'quantity'                      => 'nullable|integer|min:0',
            'category_id'                   => 'nullable|integer',
            'image'                         => 'nullable|string',
            'gallery'                       => 'nullable|string',
            'artisan_gallery.*.image'       => 'nullable|string',
            'artisan_gallery.*.title'       => 'nullable|string|max:255',
            'artisan_gallery.*.description' => 'nullable|string',
            'artisan_gallery.*.id'          => 'nullable|integer|exists:product_images,id',
            'attributes.key.*'              => 'nullable|string|max:255',
            'attributes.value.*'            => 'nullable|string|max:255',
            'square_banner'                 => 'nullable|string',
            'square_banner_title'           => 'nullable|string|max:255',
            'square_banner_description'     => 'nullable|string',
            'artisan_heading'               => 'nullable|string|max:255',
            'meta_title'                    => 'nullable|string|max:255',
            'meta_keywords'                 => 'nullable|string|max:255',
            'meta_description'              => 'nullable|string',
            'product_icons'                 => 'required|array|size:6',
            'product_icons.*.image'         => 'required|string',
            'product_icons.*.text'          => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {

            // ── 1. Core fields ────────────────────────────────────────────────
            $product->update([
                'name'                      => $request->name,
                'slug'                      => Str::slug($request->slug),
                'short_description'         => $request->short_description,
                'description'               => $request->description,
                'regular_price'             => $request->regular_price,
                'sale_price'                => $request->sale_price ?: null,
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
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('product_update failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong: ' . $e->getMessage());
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

    public function orders()
    {
        $orders = Order::orderBy('id', 'desc')->where('payment_status', 'paid')->paginate(20);
        return view('admin.orders', compact('orders'));
    }

    public function orders_detail($id)
    {
        $orders = Order::with('items')->findOrFail($id);
        return view('admin.order-detail', compact('orders'));
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
        ]);

        $image     = $request->file('image');
        $file_name = Carbon::now()->timestamp . '.' . $image->getClientOriginalExtension();
        $this->GenerateBlogThumbnailsImage($image, $file_name);

        $blog = Blog::create([
            'title'            => $request->title,
            'slug'             => $request->slug,
            'content'          => $request->content,
            'image'            => $file_name,
            'meta_title'       => $request->meta_title,
            'meta_keywords'    => $request->meta_keywords,
            'meta_description' => $request->meta_description,
        ]);

        $tagIds = collect(explode(',', $request->tags ?? ''))->filter()->map(function ($tagName) {
            return Tag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
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
        ]);

        $imageName = $blog->image;

        if ($request->hasFile('image')) {
            $fullPath  = public_path('uploads/blogs/' . $blog->image);
            $thumbPath = public_path('uploads/blogs/thumbnails/' . $blog->image);
            if (File::exists($fullPath))  File::delete($fullPath);
            if (File::exists($thumbPath)) File::delete($thumbPath);

            $image     = $request->file('image');
            $imageName = Carbon::now()->timestamp . '.' . $image->getClientOriginalExtension();
            $this->GenerateBlogThumbnailsImage($image, $imageName);
        }

        $blog->update([
            'title'            => $request->title,
            'slug'             => $request->slug,
            'content'          => $request->content,
            'image'            => $imageName,
            'meta_title'       => $request->meta_title,
            'meta_keywords'    => $request->meta_keywords,
            'meta_description' => $request->meta_description,
        ]);

        $tagIds = collect(explode(',', $request->tags ?? ''))->filter()->map(function ($tagName) {
            return Tag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
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

    public function testimonials()
    {
        $testimonials = Testimonial::orderBy('id', 'desc')->paginate(10);
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
            'status'        => 'required'
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
            'status'        => 'required'
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

        return view('admin.about-section', compact('section'));
    }

    public function about_section_update(Request $request)
    {
        $request->validate([
            'heading'     => 'required',
            'description' => 'required',
            'signature'   => 'nullable',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp',
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

        $section->save();

        return redirect()
            ->back()
            ->with('status', 'About section updated successfully');
    }

    // =========================================================================
    // MISC
    // =========================================================================

    public function users()
    {
        $users = User::where('utype', '!=', 'ADM')->orderBy('id', 'desc')->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function contactquery()
    {
        $contactQueries = Contact::orderBy('id', 'desc')->paginate(10);
        return view('admin.contact-queries', compact('contactQueries'));
    }

    public function contactquerydelete($id)
    {
        Contact::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Contact query deleted successfully');
    }

    public function askquestions()
    {
        $askedQuestions = AskQuestion::with(['product', 'user'])->orderBy('id', 'desc')->paginate(10);
        return view('admin.asked-questions', compact('askedQuestions'));
    }

    public function askquestion_delete($id)
    {
        AskQuestion::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Ask question deleted successfully');
    }

    public function subscribers()
    {
        $subscribers = Subscribe::with('user')->orderBy('id', 'desc')->paginate(10);
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
