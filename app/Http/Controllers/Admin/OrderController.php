<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InventoryService;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->when($request->string('q')->toString(), fn ($q, $t) => $q->where('number', 'like', "%{$t}%")
                ->orWhere('email', 'like', "%{$t}%"))
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('payment_status'), fn ($q, $s) => $q->where('payment_status', $s))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'payment', 'payments', 'user', 'shippingMethod', 'statusHistory.user']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function updateStatus(Request $request, Order $order, InventoryService $inventory)
    {
        $data = $request->validate([
            'status' => ['required', new Enum(OrderStatus::class)],
            'note' => ['nullable', 'string', 'max:250'],
        ]);

        $new = OrderStatus::from($data['status']);
        $previous = $order->status;

        // Restock when an order is cancelled/returned that had stock deducted.
        if (in_array($new, [OrderStatus::Cancelled, OrderStatus::Returned], true)
            && ! in_array($previous, [OrderStatus::Cancelled, OrderStatus::Returned, OrderStatus::Refunded], true)) {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $inventory->returnToStock($item->product, $item->variant, $item->quantity, $order);
                }
            }
        }

        $order->status = $new->value;
        $order->fill(match ($new) {
            OrderStatus::Confirmed => ['confirmed_at' => now()],
            OrderStatus::Shipped => ['shipped_at' => now()],
            OrderStatus::Delivered => ['delivered_at' => now()],
            OrderStatus::Cancelled => ['cancelled_at' => now()],
            default => [],
        });
        $order->save();

        if ($data['note'] ?? null) {
            $order->statusHistory()->latest()->first()?->update(['note' => $data['note']]);
        }

        ActivityLogger::log('order.status', "Order {$order->number} → {$new->label()}", $order);

        return back()->with('success', "Order marked as {$new->label()}.");
    }

    public function updateDetails(Request $request, Order $order)
    {
        $data = $request->validate([
            'tracking_number' => ['nullable', 'string', 'max:120'],
            'staff_note' => ['nullable', 'string', 'max:1000'],
            'payment_status' => ['required', Rule::in(['pending', 'awaiting_confirmation', 'paid', 'failed', 'refunded', 'partially_refunded'])],
        ]);

        $order->update($data);
        if ($data['payment_status'] === 'paid' && $order->payment) {
            $order->payment->update(['status' => 'paid', 'paid_at' => $order->payment->paid_at ?? now()]);
        }

        return back()->with('success', 'Order updated.');
    }

    public function invoice(Order $order)
    {
        $order->load(['items', 'user']);

        return view('admin.orders.invoice', compact('order'));
    }

    public function destroy(Request $request, Order $order, InventoryService $inventory)
    {
        // Return stock for orders that had it deducted and were not already reversed.
        if (! in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Returned, OrderStatus::Refunded], true)) {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $inventory->returnToStock($item->product, $item->variant, $item->quantity, $order);
                }
            }
        }

        $number = $order->number;
        $order->delete(); // cascades order_items, payments, status history

        ActivityLogger::log('order.deleted', "Deleted order {$number}");

        return redirect()->route('admin.orders.index')->with('success', "Order {$number} deleted.");
    }
}
