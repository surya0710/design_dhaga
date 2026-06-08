<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItemReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\Address;
use App\Services\ShiprocketService;
use App\Models\Menu;
use App\Models\HomepageHighlight;

class AccountController extends Controller
{
    protected $categories;
    protected $shiprocket;
    protected $menu;
    protected $highlights;

    public function __construct(ShiprocketService $shiprocket)
    {
        $this->categories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        $this->shiprocket = $shiprocket;
        $this->menu = Menu::where('is_active', 1)->orderBy('created_at', 'asc')->get();
        $this->highlights = HomepageHighlight::where('status', 1)->get();
    }

    public function index()
    {
        $categories = $this->categories;

        $addresses = Address::where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        // Fetch only paid orders with items
        $orders = auth()->user()->orders()->with(['items' => function ($q) {   // ✅ $q (NOT q)
            $q->select('id','order_id','product_name','product_image','price','quantity','total');}])
        ->where('payment_status', 'paid')->latest()->get();

        // Total Spend Calculation
        $totalSpend = $orders->sum('total');
        $menu       = $this->menu;
        $highlights     = HomepageHighlight::where('status', 1)->get();

        return view('user.my-account', compact('categories','addresses','orders', 'totalSpend', 'menu', 'highlights'));
    }

    public function trackOrder(string $awbCode)
    {
        try {
            $data = $this->shiprocket->trackOrder($awbCode);

            $order = auth()->user()
                ->orders()
                ->where('awb_code', $awbCode)
                ->first();

            if ($order) {
                $order->tracking_status = $data['current_status'] ?? $order->tracking_status;
                $order->tracking_last_update = now();
                $order->tracking_raw_json = json_encode($data);

                $currentStatus = strtolower((string) ($data['current_status'] ?? ''));

                if (str_contains($currentStatus, 'deliver') || !empty($data['delivered_date'])) {
                    $order->order_status = 'delivered';
                    $order->delivered_at = $order->delivered_at ?? now();
                } elseif (in_array($order->order_status, ['pending', 'confirmed', 'packed'], true)) {
                    $order->order_status = 'shipped';
                }

                $order->save();
            }

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('home');
    }

    public function storeAddress(Request $request)
    {
        $validated = $this->validateAddress($request);

        DB::transaction(function () use ($validated) {
            $makeDefault = (bool) ($validated['is_default'] ?? false)
                || ! Address::where('user_id', auth()->id())->exists();

            if ($makeDefault) {
                Address::where('user_id', auth()->id())->update(['is_default' => false]);
            }

            Address::create(array_merge($validated, [
                'user_id' => auth()->id(),
                'country' => $validated['country'] ?? 'India',
                'is_default' => $makeDefault,
            ]));
        });

        return back()->with('success', 'Address added successfully.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        $this->authorizeAddress($address);

        $validated = $this->validateAddress($request);

        DB::transaction(function () use ($address, $validated) {
            $wasDefault = (bool) $address->is_default;
            $makeDefault = (bool) ($validated['is_default'] ?? false);

            if ($makeDefault) {
                Address::where('user_id', auth()->id())
                    ->whereKeyNot($address->id)
                    ->update(['is_default' => false]);
            }

            $address->update(array_merge($validated, [
                'country' => $validated['country'] ?? 'India',
                'is_default' => $makeDefault,
            ]));

            // Keep exactly one default address for the user.
            // If user unsets the current default and doesn't choose another here,
            // promote the most recently updated remaining address.
            if ($wasDefault && ! $makeDefault) {
                $fallbackDefault = Address::where('user_id', auth()->id())
                    ->whereKeyNot($address->id)
                    ->latest()
                    ->first();

                if ($fallbackDefault) {
                    $fallbackDefault->update(['is_default' => true]);
                } else {
                    // If this is the only address, keep it as default.
                    $address->update(['is_default' => true]);
                }
            }
        });

        return back()->with('success', 'Address updated successfully.');
    }

    public function setDefaultAddress(Address $address)
    {
        $this->authorizeAddress($address);

        DB::transaction(function () use ($address) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return back()->with('success', 'Default address updated.');
    }

    public function deleteAddress(Address $address)
    {
        $this->authorizeAddress($address);

        DB::transaction(function () use ($address) {
            $wasDefault = $address->is_default;
            $address->delete();

            if ($wasDefault) {
                $nextAddress = Address::where('user_id', auth()->id())->latest()->first();
                if ($nextAddress) {
                    $nextAddress->update(['is_default' => true]);
                }
            }
        });

        return back()->with('success', 'Address deleted successfully.');
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

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'country' => 'nullable|string|max:100',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'landmark' => 'nullable|string|max:255',
            'address_type' => 'required|in:home,work,other',
            'is_default' => 'nullable|boolean',
        ]);
    }

    private function authorizeAddress(Address $address): void
    {
        abort_unless((int) $address->user_id === (int) auth()->id(), 403);
    }
}
