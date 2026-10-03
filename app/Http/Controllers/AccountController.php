<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        return view('account.dashboard', [
            'ordersCount' => $user->orders()->count(),
            'openOrders' => $user->orders()->whereNotIn('status', ['delivered', 'cancelled', 'refunded', 'returned'])->count(),
            'wishlistCount' => $user->wishlist?->items()->count() ?? 0,
            'addressCount' => $user->addresses()->count(),
            'recentOrders' => $user->orders()->latest()->take(5)->get(),
        ]);
    }

    public function orders(Request $request)
    {
        $orders = $request->user()->orders()
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('account.orders', compact('orders'));
    }

    public function showOrder(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $order->load(['items.product.images', 'payment', 'shippingMethod', 'statusHistory.user']);

        return view('account.order-show', compact('order'));
    }

    public function cancelOrder(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($order->isCancellable(), 422, 'This order can no longer be cancelled.');

        $order->update([
            'status' => OrderStatus::Cancelled->value,
            'cancelled_at' => now(),
        ]);

        return back()->with('success', "Order {$order->number} has been cancelled.");
    }

    public function reorder(Request $request, Order $order, CartService $cart)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $added = 0;
        foreach ($order->items as $item) {
            $product = $item->product;
            if (! $product || $product->status->value !== 'active') {
                continue;
            }
            try {
                $cart->add($product, $item->variant, $item->quantity);
                $added++;
            } catch (\Throwable) {
                // skip unavailable lines
            }
        }

        return redirect()->route('cart.index')
            ->with($added ? 'success' : 'error', $added
                ? "{$added} item(s) added back to your cart."
                : 'None of those items are available right now.');
    }

    public function editProfile(Request $request)
    {
        return view('account.profile');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($request->user()->id)],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $user = $request->user();
        if ($data['email'] !== $user->email) {
            $user->email_verified_at = null;
        }
        $user->fill($data)->save();

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($data['password']),
            'must_change_password' => false,
        ]);

        return back()->with('success', 'Password changed.');
    }

    public function notifications(Request $request)
    {
        return view('account.notifications', [
            'notifications' => $request->user()->notifications()->paginate(20),
        ]);
    }

    public function readNotifications(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
