<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Notifications\OrderStatusUpdatedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        $from = $order->getOriginal('status');
        $to = $order->status instanceof OrderStatus ? $order->status->value : $order->status;

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $from,
            'to_status' => $to,
            'user_id' => Auth::id(),
        ]);

        Log::info('Order status changed', [
            'order' => $order->number,
            'from' => $from,
            'to' => $to,
        ]);

        try {
            $order->user?->notify(new OrderStatusUpdatedNotification($order));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
