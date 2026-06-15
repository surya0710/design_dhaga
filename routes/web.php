<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\CkeditorController;
use App\Http\Controllers\ShiprocketController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ShiprocketWebhookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\HomePageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\HomepageHighlightController;
use App\Http\Controllers\Admin\FooterWidgetController;
use App\Http\Controllers\Admin\FaqController;

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/email/verify', [UserController::class, 'verificationNotice'])->name('verification.notice');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('home')
        ->with('success', 'Email verified successfully.');
})->middleware(['auth', 'signed'])->name('verification.verify');


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/register', [UserController::class, 'register'])->name('register');
Route::post('/register', [UserController::class, 'registerPost'])->name('register.post');
Auth::routes([
    'login' => false,
    'reset' => false,
    'register' => false,
]);

Route::post('/login', [UserController::class, 'loginPost'])->name('login.post');

Route::match(['GET', 'POST'], '/forgot-password', [UserController::class, 'forgotPassword'])->name('password.forgot');
Route::match(['GET', 'POST'], '/reset-password/{token}', [UserController::class, 'resetPassword'])->name('password.reset');
Route::get('login', function(){ return redirect()->route('home');})->name('login');

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [HomeController::class, 'about'])->name('about-us');
Route::get('/contact-us', [HomeController::class, 'contact'])->name('contact-us');
Route::post('/contact-us', [HomeController::class, 'sendmail'])->name('sendmail');
Route::get('/portfolio/{slug?}', [HomeController::class, 'portfolio'])->name('portfolio');
Route::get('/terms-and-condition', [HomeController::class, 'terms'])->name('terms-and-condition');
Route::get('/return-policy', [HomeController::class, 'returnPolicy'])->name('return-policy');
Route::get('/order-shipping-policy', [HomeController::class, 'orderShipping'])->name('shipping-policy');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/store', [HomeController::class, 'store'])->name('store');
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs');
Route::get('/blogs/{slug}', [BlogController::class, 'blogdetail'])->name('blog.show');
Route::get('/collaborations', [HomeController::class, 'collaborations'])->name('collaborations');
Route::post('/pincode/serviceable', [ShiprocketController::class, 'checkPincode'])->name('pincode.serviceable');
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);


/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'utype:USR', 'verified'])->group(function () {

    Route::get('/account', [AccountController::class,'index'])->name('account.index');
    Route::post('/account/addresses', [AccountController::class, 'storeAddress'])->name('account.addresses.store');
    Route::put('/account/addresses/{address}', [AccountController::class, 'updateAddress'])->name('account.addresses.update');
    Route::patch('/account/addresses/{address}/default', [AccountController::class, 'setDefaultAddress'])->name('account.addresses.default');
    Route::delete('/account/addresses/{address}', [AccountController::class, 'deleteAddress'])->name('account.addresses.delete');
    
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add',    [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

    Route::get('/wishlist', [ShopController::class, 'wishlist'])->name('wishlist.index');

    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');

    Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/delivery-options', [CheckoutController::class, 'getDeliveryOptions'])->name('checkout.delivery.options');

    Route::post('/place-order', [CheckoutController::class, 'placeOrder'])->name('place.order');

    Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');

    Route::post('/razorpay/order', [CheckoutController::class, 'createRazorpayOrder'])->name('razorpay.order');

    Route::post('/razorpay/verify', [CheckoutController::class, 'verifyRazorpayPayment'])->name('razorpay.verify');

    Route::get('/order/{id}/invoice', [CheckoutController::class, 'invoice'])->name('order.invoice');
    Route::get('order/track/{awb}', [AccountController::class, 'trackOrder'])->name('order.track');

    Route::post('/checkout/calculate-gst', [CheckoutController::class, 'calculateGst'])->name('checkout.calculate.gst');

    Route::get('/order/{id}/invoice/download', function ($id) { return app(\App\Http\Controllers\CheckoutController::class)->invoice($id, 'download');})->name('order.invoice.download');

    Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');
    Route::post('/coupon/remove', [CouponController::class, 'remove'])->name('coupon.remove');

    Route::post('/review/store', [ReviewController::class, 'store'])->name('review.store');
    Route::put('/review/{id}', [ReviewController::class, 'update'])->name('review.update');
    Route::delete('/review/{id}', [ReviewController::class, 'destroy'])->name('review.destroy');

    // Changed to POST for security (same route name)
    Route::post('/logout', [AccountController::class,'logout'])->name('account.logout');
});

Route::post('/webhook/shipment', [ShiprocketWebhookController::class, 'handle']);

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminController::class,'login'])->name('admin.login');
Route::post('/admin/login', [AdminController::class,'loginAttempt'])->name('admin.loginAttempt');

Route::middleware(['auth.admin', 'utype:ADM'])->group(function(){

    Route::get('/admin', [AdminController::class,'index'])->name('admin.index');
    Route::get('/admin/total-pages', [AdminController::class, 'totalPages'])->name('admin.total-pages');

    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/categories/add', [AdminController::class, 'category_add'])->name('admin.category.add');
    Route::post('/admin/categories/store', [AdminController::class, 'category_store'])->name('admin.category.store');
    Route::get('/admin/categories/edit/{id}', [AdminController::class, 'category_edit'])->name('admin.category.edit');
    Route::put('/admin/categories/update', [AdminController::class, 'category_update'])->name('admin.category.update');
    Route::delete('/admin/categories/delete/{id}', [AdminController::class, 'category_delete'])->name('admin.category.delete');

    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/products/add', [AdminController::class, 'product_add'])->name('admin.products.add');
    Route::post('/admin/products/store', [AdminController::class, 'product_store'])->name('admin.product.store');
    Route::get('/admin/products/edit/{id}', [AdminController::class, 'product_edit'])->name('admin.product.edit');
    Route::put('/admin/products/update/{id}', [AdminController::class, 'product_update'])->name('admin.product.update');
    Route::delete('/admin/products/delete/{id}', [AdminController::class, 'product_delete'])->name('admin.product.delete');
    Route::post('admin/products/{id}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.product.toggleStatus');
    Route::get('admin/products/import-products', [AdminController::class, 'importproductcsv'])->name('import.products');
    Route::post('admin/products/import-products/add', [AdminController::class, 'importProducts'])->name('import.products.add');
    Route::post('/admin/products/delete-product-image', [AdminController::class, 'deleteProductImage'])->name('product.image.delete');

    Route::get('/admin/coupons', [CouponController::class,'index'])->name('admin.coupons');
    Route::get('/admin/coupon/add', [CouponController::class,'add_coupon'])->name('admin.coupon.add');
    Route::post('/admin/coupon/store', [CouponController::class,'coupon_store'])->name('admin.coupon.store');
    Route::get('/admin/coupon/edit/{id}', [CouponController::class,'coupon_edit'])->name('admin.coupon.edit');
    Route::post('/admin/coupon/update', [CouponController::class,'coupon_update'])->name('admin.coupon.update');
    Route::get('/admin/coupon/delete/{id}', [CouponController::class,'coupon_delete'])->name('admin.coupon.delete');

    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/admin/order/detail/{id}', [AdminController::class, 'orders_detail'])->name('admin.order.detail');
    Route::get('/admin/order/track', [AdminController::class, 'orders_track'])->name('admin.order.track');
    Route::post('/admin/order/{id}/status', [AdminController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/admin/orders/{id}/reset-shiprocket', [AdminController::class, 'resetShiprocket'])->name('orders.resetShiprocket');

    Route::get('admin/blogs', [AdminController::class, 'blogs'])->name('admin.blogs');
    Route::get('admin/blogs/add', [AdminController::class, 'blog_add'])->name('admin.blog.add');
    Route::post('admin/blogs/store', [AdminController::class, 'blog_store'])->name('admin.blog.store');
    Route::get('admin/blogs/edit/{id}', [AdminController::class, 'blog_edit'])->name('admin.blog.edit');
    Route::put('admin/blogs/update/{blog}', [AdminController::class, 'blog_update'])->name('admin.blog.update');
    // Route::get('admin/blogs/delete/{id}', [AdminController::class, 'blog_delete'])->name('admin.blog.delete');
    Route::put('admin/blogs/{blog}/toggle-status', [AdminController::class, 'BlogToggleStatus'])->name('admin.blog.toggleStatus');
    Route::delete('admin/blogs/{id}/delete', [AdminController::class, 'BlogDelete'])->name('admin.blog.delete');
    Route::get('admin/blogs/import-blogs', [AdminController::class, 'importblogscsv'])->name('import.blogs');
    Route::post('admin/blogs/import-blogs/add', [AdminController::class, 'importBlogs'])->name('import.blogs.add');
    Route::post('/ckeditor/upload', [CkeditorController::class, 'upload'])->name('ckeditor.upload');

    Route::get('/admin/testimonials', [AdminController::class, 'testimonials'])->name('admin.testimonials');
    Route::get('/admin/testimonials/add', [AdminController::class, 'testimonial_add'])->name('admin.testimonial.add');
    Route::post('/admin/testimonials/store', [AdminController::class, 'testimonial_store'])->name('admin.testimonial.store');
    Route::get('/admin/testimonials/edit/{id}', [AdminController::class, 'testimonial_edit'])->name('admin.testimonial.edit');
    Route::put('/admin/testimonials/{id}/update', [AdminController::class, 'testimonial_update'])->name('admin.testimonial.update');
    Route::delete('/admin/testimonials/delete/{id}', [AdminController::class, 'testimonial_delete'])->name('admin.testimonial.delete');

    Route::get('/admin/reviews', [ReviewController::class, 'adminIndex'])->name('admin.reviews');
    Route::delete('/admin/reviews/delete/{id}', [ReviewController::class, 'adminDestroy'])->name('admin.review.delete');

    Route::get('/admin/stories', [AdminController::class, 'stories'])->name('admin.stories');
    Route::get('/admin/story/add', [AdminController::class, 'story_add'])->name('admin.story.add');
    Route::post('/admin/story/store', [AdminController::class, 'story_store'])->name('admin.story.store');
    Route::get('/admin/story/edit/{id}', [AdminController::class, 'story_edit'])->name('admin.story.edit');
    Route::put('/admin/story/update/{id}', [AdminController::class, 'story_update'])->name('admin.story.update');
    Route::delete('/admin/story/delete/{id}', [AdminController::class, 'story_delete'])->name('admin.story.delete');

    Route::prefix('/admin/portfolio')->name('admin.portfolio.')->group(function () {
        Route::get('/categories', [PortfolioController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [PortfolioController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}', [PortfolioController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [PortfolioController::class, 'deleteCategory'])->name('categories.destroy');

        Route::get('/subcategories', [PortfolioController::class, 'subcategories'])->name('subcategories.index');
        Route::post('/subcategories', [PortfolioController::class, 'storeSubcategory'])->name('subcategories.store');
        Route::put('/subcategories/{subcategory}', [PortfolioController::class, 'updateSubcategory'])->name('subcategories.update');
        Route::delete('/subcategories/{subcategory}', [PortfolioController::class, 'deleteSubcategory'])->name('subcategories.destroy');
        Route::get('/subcategories/by-category/{category}', [PortfolioController::class, 'subcategoriesForCategory'])->name('subcategories.by-category');

        Route::get('/gallery', [PortfolioController::class, 'gallery'])->name('gallery.index');
        Route::get('/gallery/create', [PortfolioController::class, 'createGallery'])->name('gallery.create');
        Route::post('/gallery', [PortfolioController::class, 'storeGallery'])->name('gallery.store');
        Route::get('/gallery/{gallery}/edit', [PortfolioController::class, 'editGallery'])->name('gallery.edit');
        Route::put('/gallery/{gallery}', [PortfolioController::class, 'updateGallery'])->name('gallery.update');
        Route::delete('/gallery/{gallery}', [PortfolioController::class, 'deleteGallery'])->name('gallery.destroy');
    });

    Route::get('/admin/about-section', [AdminController::class, 'about_section'])->name('admin.about.section');

    Route::post('/admin/about-section/update', [AdminController::class, 'about_section_update'])->name('admin.about.section.update');

    Route::get('/admin/home-page', [HomePageController::class, 'index'])->name('admin.home-page.index');
    Route::put('/admin/home-page/sections/{section}', [HomePageController::class, 'updateSection'])->name('admin.home-page.sections.update');
    Route::post('/admin/home-page/sections/{section}/items', [HomePageController::class, 'storeItem'])->name('admin.home-page.items.store');
    Route::put('/admin/home-page/items/{item}', [HomePageController::class, 'updateItem'])->name('admin.home-page.items.update');
    Route::delete('/admin/home-page/items/{item}', [HomePageController::class, 'deleteItem'])->name('admin.home-page.items.destroy');

    Route::get('/admin/sliders/list', [SliderController::class, 'sliders'])->name('admin.sliders');
    Route::get('/admin/sliders/create/', [SliderController::class, 'sliders_create'])->name('admin.sliders.create');
    Route::post('/admin/sliders/create/', [SliderController::class, 'sliders_add'])->name('admin.sliders.store');
    Route::get('/admin/sliders/edit/{id}', [SliderController::class, 'sliders_edit'])->name('admin.sliders.edit');
    Route::put('/admin/sliders/edit/{id}', [SliderController::class, 'update'])->name('admin.sliders.update');
    Route::delete('/admin/sliders/delete/{id}', [SliderController::class, 'sliders_delete'])->name('admin.sliders.destroy');

    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');

    Route::get('/admin/contactquery', [AdminController::class, 'contactquery'])->name('admin.contact.view');
    Route::delete('/admin/contactquery/{id}', [AdminController::class, 'contactquerydelete'])->name('admin.contact.delete');

    Route::get('/admin/askquestions', [AdminController::class, 'askquestions'])->name('admin.questions.view');
    Route::delete('/admin/askquestions/{id}', [AdminController::class, 'askquestion_delete'])->name('admin.question.delete');

    Route::get('/admin/media', [MediaController::class,'index']);
    Route::post('/admin/media/upload', [MediaController::class,'upload']);

    Route::resource('/admin/menus', MenuController::class)->names([
        'index'   => 'admin.menus.index',
        'create'  => 'admin.menus.create',
        'store'   => 'admin.menus.store',
        'show'    => 'admin.menus.show',
        'edit'    => 'admin.menus.edit',
        'update'  => 'admin.menus.update',
        'destroy' => 'admin.menus.destroy',
    ]);

    Route::prefix('/admin/menus/{menu}/items')->name('admin.menu-items.')->group(function () {
        Route::get('create',          [MenuItemController::class, 'create'])->name('create');
        Route::post('/',              [MenuItemController::class, 'store'])->name('store');
        Route::get('{menuItem}/edit', [MenuItemController::class, 'edit'])->name('edit');
        Route::put('{menuItem}',      [MenuItemController::class, 'update'])->name('update');
        Route::delete('{menuItem}',   [MenuItemController::class, 'destroy'])->name('destroy');
    });

    Route::post('/admin/menu-items/reorder', [MenuItemController::class, 'reorder'])->name('admin.menu-items.reorder');

    Route::get('/admin/pages', [PageController::class, 'index'])->name('admin.pages');
    Route::get('/admin/pages/create', [PageController::class, 'create'])->name('admin.pages.create');
    Route::post('/admin/pages/store', [PageController::class, 'store'])->name('admin.pages.store');

    Route::get('/admin/pages/edit/{id}', [PageController::class, 'edit'])->name('admin.pages.edit');
    Route::post('/admin/pages/update/{id}', [PageController::class, 'update'])->name('admin.pages.update');

    Route::delete('/admin/pages/delete/{id}', [PageController::class, 'destroy'])->name('admin.pages.delete');

    Route::get('/admin/homepage-highlights', [HomepageHighlightController::class, 'index'])->name('admin.homepage-highlights.index');
    Route::post('/admin/homepage-highlights/store', [HomepageHighlightController::class, 'store'])->name('admin.highlights.store');
    Route::post('/admin/homepage-highlights/update/{id}', [HomepageHighlightController::class, 'update'])->name('admin.highlights.edit');
    Route::delete('/admin/homepage-highlights/delete/{id}', [HomepageHighlightController::class, 'destroy'])->name('admin.highlights.destroy');

    Route::get('/admin/footer-widgets', [FooterWidgetController::class, 'index'])->name('footer.widgets');
    Route::post('/admin/footer-widgets/store', [FooterWidgetController::class, 'store'])->name('footer.widgets.store');
    Route::post('/admin/footer-widgets/update/{id}', [FooterWidgetController::class, 'update'])->name('footer.widgets.update');
    Route::delete('/admin/footer-widgets/delete/{id}',[FooterWidgetController::class, 'delete']) ->name('footer.widgets.delete');

    Route::get('/admin/subscribers', [AdminController::class, 'subscribers'])->name('admin.subscribers.view');
    Route::get('/admin/settings', [SettingController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings/update', [SettingController::class, 'settings_update'])->name('admin.settings.update');
    
    Route::get('/admin/faqs', [FaqController::class, 'index'])->name('admin.faqs');
    Route::get('/admin/faqs/create', [FaqController::class, 'create'])->name('admin.faqs.create');
    Route::post('/admin/faqs/store', [FaqController::class, 'store'])->name('admin.faqs.store');
    Route::get('/admin/faqs/edit/{id}', [FaqController::class, 'edit'])->name('admin.faqs.edit');
    Route::post('/admin/faqs/update/{id}', [FaqController::class, 'update'])->name('admin.faqs.update');
    Route::delete('/admin/faqs/delete/{id}', [FaqController::class, 'delete'])->name('admin.faqs.delete');
    
    Route::post('/admin/logout', [AdminController::class,'logout'])->name('logout');
});


Route::get('/shop/load', [ShopController::class, 'loadProducts'])->name('shop.load');
Route::get('/shop', [ShopController::class, 'category_products'])->name('shop.all');
Route::get('/shop/{category}', [ShopController::class, 'category_products'])->name('shop.index');
Route::get('/shop/{category}/{subcategory}', [ShopController::class, 'category_products'])->name('shop.subcategory');
Route::get('/shop/{category}/{subcategory}/{product}', [ShopController::class, 'product_details'])->name('shop.product');
Route::get('/search/suggestions', [ShopController::class, 'searchSuggestions'])->name('shop.search.suggestions');
Route::get('/search', [ShopController::class, 'search'])->name('shop.search');
