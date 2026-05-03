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

Auth::routes(['login' => false, 'reset' => false]);

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
Route::get('/portfolio', [HomeController::class, 'portfolio'])->name('portfolio');
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

    Route::post('/checkout/calculate-gst', [CheckoutController::class, 'calculateGst'])->name('checkout.calculate.gst');

    Route::get('/order/{id}/invoice/download', function ($id) { return app(\App\Http\Controllers\CheckoutController::class)->invoice($id, 'download');})->name('order.invoice.download');

    Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');
    Route::post('/coupon/remove', [CouponController::class, 'remove'])->name('coupon.remove');

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

    Route::get('/admin/brands', [AdminController::class,'brands'])->name('admin.brands');
    Route::get('/admin/brand/add', [AdminController::class,'add_brand'])->name('admin.brand.add');
    Route::post('/admin/brand/store', [AdminController::class,'brand_store'])->name('admin.brand.store');
    Route::get('/admin/brand/edit/{id}', [AdminController::class,'brand_edit'])->name('admin.brand.edit');
    Route::put('/admin/brand/update', [AdminController::class,'brand_update'])->name('admin.brand.update');
    Route::delete('/admin/brand/{id}/delete', [AdminController::class,'brand_delete'])->name('admin.brand.delete');

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
    Route::put('/admin/testimonials/update', [AdminController::class, 'testimonial_update'])->name('admin.testimonial.update');
    Route::delete('/admin/testimonials/delete/{id}', [AdminController::class, 'testimonial_delete'])->name('admin.testimonial.delete');

    Route::get('/admin/sliders/list', [SliderController::class, 'sliders'])->name('admin.sliders');
    Route::match(['get', 'post'], '/admin/sliders/create/{id?}', [SliderController::class, 'sliders_create'])->name('admin.sliders.create');

    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');

    Route::get('/admin/contactquery', [AdminController::class, 'contactquery'])->name('admin.contact.view');
    Route::delete('/admin/contactquery/{id}', [AdminController::class, 'contactquerydelete'])->name('admin.contact.delete');

    Route::get('/admin/askquestions', [AdminController::class, 'askquestions'])->name('admin.questions.view');
    Route::delete('/admin/askquestions/{id}', [AdminController::class, 'askquestion_delete'])->name('admin.question.delete');

    Route::get('/admin/media', [MediaController::class,'index']);
    Route::post('/admin/media/upload', [MediaController::class,'upload']);

    Route::get('/admin/subscribers', [AdminController::class, 'subscribers'])->name('admin.subscribers.view');
    Route::post('/admin/logout', [AdminController::class,'logout'])->name('logout');
    
});


Route::get('/shop/{category}', [ShopController::class, 'category_products'])->name('shop.index');

Route::get('/shop/{category}/{subcategory}', [ShopController::class, 'category_products'])->name('shop.subcategory');

Route::get('/shop/{category}/{subcategory}/{product}', [ShopController::class, 'product_details'])->name('shop.product');