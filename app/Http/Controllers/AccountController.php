<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItemReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\Address;
use App\Models\Wishlist;

class AccountController extends Controller
{
    protected $categories;

    public function __construct()
    {
        $this->categories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->with('children')
            ->get();
    }

    public function index()
    {
        $categories = $this->categories;

        $addresses = Address::where('user_id', auth()->id())->get();

        // Fetch only paid orders with items
        $orders = auth()->user()->orders()->with(['items' => function ($q) {   // ✅ $q (NOT q)
            $q->select('id','order_id','product_name','product_image','price','quantity','total');}])
        ->where('payment_status', 'paid')->latest()->get();

        // Total Spend Calculation
        $totalSpend = $orders->sum('total');

        return view('user.my-account', compact(
            'categories',
            'addresses',
            'orders',
            'totalSpend'
        ));
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }
    
    public function updateInfo(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'mobile' => 'nullable|string|max:15|unique:users,mobile,' . auth()->id(),
        ]);
        $user = auth()->user();
        $user->name = $request->input('name');
        if ($user->email !== $request->input('email')) {
            $user->email = $request->input('email');
            $user->email_verified_at = null; // Reset email verification if email is changed
            $user->sendEmailVerificationNotification();
        }
        $user->mobile = $request->input('mobile');
        $user->save();
        return redirect()->back()->with('success', 'Account information updated successfully.');
    }
    // This method handles the password update functionality with confirm password validation

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();
        $user->delete();
        Auth::logout();
        return redirect('/')->with('success', 'Account deleted successfully.');
    }
    public function myOrders()
    {
        $orders = auth()->user()->orders()->with('items')->latest()->get();
        return view('my-account-orders', ['orders' => $orders, 'user' => auth()->user()]);
    }
    public function orderDetail($id)
    {
        $order = auth()->user()->orders()->with('items')->findOrFail($id);
        // dd($order->items);
        return view('my-account-orders-details', ['order' => $order, 'user' => auth()->user()]);
        
    }

    // public function orderReturn(Request $request, $id)
    // {
    //     $order = auth()->user()->orders()->with('items')->findOrFail($id);
    //     $item = $order->items()->findOrFail($request->input('item_id'));

    //     // Check if the item is eligible for return
    //     if (!$item->isEligibleForReturn()) {
    //         return back()->with('error', 'This item is not eligible for return.');
    //     }

    //     // Process the return request
    //     $item->requestReturn($request->input('reason'));

    //     return back()->with('success', 'Return request submitted successfully.');
    // }
    public function returnRequest(Request $request){
        // $orders = auth()->user()->orders()->with(['items', 'items.returnRequest'])->latest()->get();
        $item_id = $request->input('item_id');
        $order_id = $request->input('order_id');
        return view('order-return', compact('item_id', 'order_id'));
    }

    public function returnItem(Request $request)
    {
        
        $order = Order::findOrFail($request->order_id);
        $item = $order->items()->findOrFail($request->order_item_id);
        // Check delivered_at exists
        if (!$order->delivered_at) {
            return back()->with('error', 'Order not marked as delivered.');
        }

        // Check 14-day return window
        $daysSinceDelivery = Carbon::parse($order->delivered_at)->diffInDays(Carbon::now());
        if ($daysSinceDelivery > 14) {
            return back()->with('error', 'Return window expired for this item.');
        }

        // Check if return already requested
        if ($item->returnRequest) {
            return back()->with('error', 'Return already requested for this item.');
        }

        OrderItemReturn::create([
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'reason_title' => $request->reason_title,
            'reason' => $request->reason,
            'status' => 'requested',
        ]);

        return redirect()->route('user.orders')->with('success', 'Return requested for item.');
    }

    public function invoice(Order $order) {
        return view('invoice', compact('order'));
    }
}
