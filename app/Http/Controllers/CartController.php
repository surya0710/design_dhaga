<?php

namespace App\Http\Controllers;

use App\Mail\OrderInvoiceMail;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Api;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index(){
        $items = Cart::instance('cart')->content();
        $total = Cart::instance('cart')->total();

        //related products
        $related_products = Product::where('status', '1')->orderBy('id', 'desc')->take(4)->get();
        $related_products->each(function ($related_product) {
            if ($related_product->sale_price > 0) {
                $related_product->price = $related_product->sale_price;
            } else {
                $related_product->price = $related_product->regular_price;
            }
        });

        $coupon = Coupon::where('status', '1')
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::now())
            ->get();

        return view('cart', compact('items', 'total','related_products','coupon'));
    }
    public function cartModal(){
        $items = Cart::instance('cart')->content();
        $total = Cart::instance('cart')->total();
        return view('layouts.cart-modal', compact('items', 'total'));
    }


    public function add_to_cart(Request $request){
        $product = Product::findOrFail($request->id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        if ($product->quantity < $request->qty) {
            return response()->json(['error' => 'Insufficient stock'], 400);
        }
        if ($request->qty <= 0) {
            return response()->json(['error' => 'Invalid quantity'], 400);
        }
        if ($product->stock_status != 'instock') {
            return response()->json(['error' => 'Product is out of stock'], 400);
        }
        if ($product->status != '1') {
            return response()->json(['error' => 'Product is not available'], 400);
        }
        if ($product->sale_price > 0) {
            $price = $product->sale_price;
        } else {
            $price = $product->regular_price;
        }
        $allimages = $product->images ? explode(',', $product->images) : [];
        if (count($allimages) > 0) {
            $firstimage = $allimages[0];
        } else {
            $firstimage = 'default.png'; // Fallback image
        }
        // Cart::add([
        //     'id' => $request->id,
        //     'name' => $request->name,
        //     'qty' => $request->qty,
        //     'price' => $request->price,
        // ]);
        if ($request->cert_id == 0) {
            $certPrice = 0;
            $certName = 'No Certification';
        }if ($request->cert_id == 1) {
            $certPrice = 0;
            $certName = 'Free Lab Certificate';
        }if ($request->cert_id == 2) {
            $certPrice = 1050;
            $certName = 'Lab Certificate - IIGJ';
        }if ($request->cert_id == 3) {
            $certPrice = 3600;
            $certName = 'Lab Certificate - IGI';
        }

        // return response()->json(['success' => true]);
        Cart::instance('cart')->add($product->id, $product->name, $request->qty, $price + $certPrice, ['certificate_name' => $certName, 'certificate_price' => $certPrice] )->associate('App\Models\Product');
        // return redirect()->back()->with('success', 'Product added to cart successfully');
         return response()->json([
            'success' => true,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'product_image' => asset('uploads/products/thumbnails/' . $firstimage),
            'product_price' => $price + $certPrice,
            'product_price_formatted' => format_currency(($price + $certPrice) * $request->qty, session('currency', 'INR')),
            'product_qty' => $request->qty,
            'cart_count' => Cart::instance('cart')->count()
        ]);
    }
    // public function add(Request $request){
    //     $product = Product::find($request->id);
    //     Cart::instance('cart')->add($product->id, $product->name, 1, $product->price)->associate('App\Models\Product');
    //     return redirect()->route('cart.index')->with('success', 'Product added to cart successfully');
    // }
    public function remove($rowId){
        Cart::instance('cart')->remove($rowId);
        return redirect()->back()->with('success', 'Product removed from cart successfully');
    }
    public function update(Request $request, $rowId){
        $qty = $request->input('qty');
        // dd($qty);
        Cart::instance('cart')->update($request->rowId, $request->qty);
        return redirect()->back()->with('success', 'Product updated successfully');
    }
    public function clear(){
        Cart::instance('cart')->destroy();
        return redirect()->back()->with('success', 'Cart cleared successfully');
    }

    
    
    
    public function checkout(){
        $items = Cart::instance('cart')->content();
        $total = Cart::instance('cart')->total();
        $subtotal = Cart::instance('cart')->subtotal();
        // $vat = Cart::instance('cart')->subtotal();
        $coupon = Coupon::where('status', '1')
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::now())
            ->get();
        
        if (Cart::instance('cart')->count() <= 0) {
            return redirect()->route('home.index')->with('error', 'Your cart is empty!');
        }
        return view('checkout', compact('items', 'subtotal', 'total', 'coupon'));
    }
    public function storeBuynow(Request $request)
    {
        $product = Product::findOrFail($request->id);
        if (!$product) {
            return redirect()->back()->with('error' , 'Product not found');
        }
        if ($product->quantity < $request->qty) {
            return redirect()->back()->with('error', 'Insufficient stock');
        }
        if ($request->qty <= 0) {
            return redirect()->back()->with('error', 'Invalid quantity');
        }
        if ($product->stock_status != 'instock') {
            return redirect()->back()->with('error', 'Product is out of stock');
        }
        if ($product->status != '1') {
            return redirect()->back()->with('error', 'Product is not available');
        }
        if ($product->sale_price > 0) {
            $price = $product->sale_price;
        } else {
            $price = $product->regular_price;
        }

        if ($request->cert_id == 0) {
            $certPrice = 0;
            $certName = 'No Certification';
        }if ($request->cert_id == 1) {
            $certPrice = 0;
            $certName = 'Free Lab Certificate';
        }if ($request->cert_id == 2) {
            $certPrice = 1050;
            $certName = 'Lab Certificate - IIGJ';
        }if ($request->cert_id == 3) {
            $certPrice = 3600;
            $certName = 'Lab Certificate - IGI';
        }
        
        Cart::instance('cart')->add($product->id, $product->name, $request->qty, $price + $certPrice, ['certificate_name' => $certName, 'certificate_price' => $certPrice])->associate('App\Models\Product');
        
        return redirect()->route('checkout.index')->with('success', 'Product added to cart successfully');
    }

    public function calculateShipping(Request $request)
    {
        $country = $request->input('country');
        $charges = config('shipping.charges');
        if (session()->has('coupon')) {
            $couponDiscount = session('coupon')['discount'];
        }
        else {
            $couponDiscount = 0;
        }

        $shippingCharge = $country === 'India'
            ? $charges['national']
            : $charges['international'];

        $subtotal = Cart::instance('cart')->subtotal(); // without tax, formatted (string)

        $subtotalNumeric = floatval(str_replace(',', '', $subtotal));
        $total = $subtotalNumeric + $shippingCharge - $couponDiscount;
        
        return response()->json([
            'shipping_charge' => format_currency($shippingCharge, session('currency', 'INR')),
            'subtotal' => format_currency($subtotalNumeric, session('currency', 'INR')),
            'total' => format_currency($total, session('currency', 'INR')),
            'totalforRazorpay' => $total,
            'shippingChargeforRazorpay' => $shippingCharge

        ]);
    }

    public function applyCoupon(Request $request)
    {
        $code = $request->coupon_code;
        $coupon = Coupon::where('code', $code)
            ->where('status', '1')
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::now())
            ->first();

        if (!$coupon) {
            return redirect()->back()->with('coupon_error', 'Invalid or expired coupon.');
        }

        $cartTotal = Cart::instance('cart')->subtotal(); // subtotal returns string like "2,500.00"
        $cartTotal = floatval(str_replace(',', '', $cartTotal));

        if ($cartTotal < $coupon->min_cart_value) {
            return redirect()->back()->with('coupon_error', 'Cart value must be at least ₹' . $coupon->min_cart_value . ' to apply this coupon.');
        }

        $discount = $coupon->type === 'fixed'
            ? $coupon->value
            : ($cartTotal * $coupon->value / 100);

        if ($coupon->max_discount > 0) {
            $discount = min($discount, $coupon->max_discount);
        }

        session()->put('coupon', [
            'cpid' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount,
        ]);

        return redirect()->back()->with('success', 'Coupon applied successfully!');
    }
    public function removeCoupon()
    {
        session()->forget('coupon');
        return redirect()->back()->with('success', 'Coupon removed successfully.');
    }

    public function createRazorpayOrder(Request $request)
    {
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        $order = $api->order->create([
            'receipt' => 'order_rcpt_' . uniqid(),
            'amount' => $request->amount * 100,
            'currency' => 'INR'
        ]);

        return response()->json(['order_id' => $order->id]);
    }
    public function processPayment(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'mobile' => 'required|string',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'street' => 'required',
            'pincode' => 'required',
            'payment_method' => 'required|in:cod,razorpay',
        ]);

        $user = auth()->user();
        $cart = Cart::instance('cart');
        $totalrs = $request->amount;
        $total = str_replace(['₹',','],'',$totalrs); 

        if ($request->payment_method === 'razorpay') {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            try {
                $attributes = [
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature,
                ];
                $api->utility->verifyPaymentSignature($attributes);

                $order = Order::create([
                    'user_id' => $user->id ?? null,
                    'name' => $request->name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'country' => $request->country,
                    'state' => $request->state,
                    'city' => $request->city,
                    'street' => $request->street,
                    'pincode' => $request->pincode,
                    'landmark' => $request->landmark,
                    'notes' => $request->notes,
                    'coupon_code' => $request->coupon_code ?? null,
                    'coupon_discount' => $request->coupon_discount ?? 0,
                    'delivery_charge' => $request->delivery_charge ?? 0,
                    'coupon_id' => $request->coupon_id ?? null,
                    'payment_method' => 'online',
                    'payment_id' => $request->razorpay_payment_id, // store Razorpay payment ID
                    'total' => $total,
                    'status' => 'paid',
                ]);

                foreach ($cart->content() as $item) {
                    $order->items()->create([
                        'product_id' => $item->id,
                        'quantity' => $item->qty,
                        'price' => $item->price,
                        'total' => $item->price * $item->qty,
                        'product_sku' => $item->sku ?? null,
                        'product_category' => $item->category ?? null,
                        'certificate_name' => $item->options->certificate_name ?? null,
                        'certificate_price' => $item->options->certificate_price ?? 0,
                    ]);
                }

                $cart->destroy();
                session(['order_id' => $order->id]);

                return redirect()->route('order.success')->with('success', 'Payment successful!');
            } catch (\Exception $e) {
                return redirect()->route('checkout.index')->with('error', 'Payment failed: ' . $e->getMessage());
            }
        }

        // COD
        if ($request->payment_method === 'cod') {
            $order = Order::create([
                'user_id' => $user->id ?? null,
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'street' => $request->street,
                'pincode' => $request->pincode,
                'landmark' => $request->landmark,
                'notes' => $request->notes,
                'coupon_code' => $request->coupon_code ?? null,
                'coupon_discount' => $request->coupon_discount ?? 0,
                'delivery_charge' => $request->delivery_charge ?? 0,
                'coupon_id' => $request->coupon_id ?? null,
                'payment_method' => 'cod',
                'payment_id' => null, // No payment ID for COD
                'total' => $total,
                'status' => 'pending',
            ]);

            foreach ($cart->content() as $item) {
                $order->items()->create([
                    'product_id' => $item->id,
                    'quantity' => $item->qty,
                    'price' => $item->price,
                    'total' => $item->price * $item->qty,
                    'product_sku' => $item->options->sku ?? null,
                    'product_category' => $item->options->category ?? null,
                    'certificate_name' => $item->options->certificate_name ?? null,
                    'certificate_price' => $item->options->certificate_price ?? 0,
                ]);
            }

            $cart->destroy();
            session(['order_id' => $order->id]);
            return redirect()->route('order.success')->with('success', 'Order placed via COD.');
        }
    }

    public function orderSuccess() {
        $orderId = session('order_id');
        if (!$orderId) {
            return redirect()->route('home.index'); // or fallback to cart/checkout
        }
        $order = Order::with('items')->findOrFail($orderId);
        Mail::to($order->email)->bcc('info@ratnabhagya.com')->send(new OrderInvoiceMail($order));
        // $order = Order::with('items')->find(session('order_id'));
        // $order = Order::findOrFail($orderId);
        // Forget the session after fetching
        session()->forget('order_id');
        return view('order-success', compact('order'));
    }

}
