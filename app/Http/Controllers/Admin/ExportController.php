<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function download(string $resource): StreamedResponse
    {
        [$headers, $rows] = match ($resource) {
            'orders' => $this->orders(),
            'customers' => $this->customers(),
            'products' => $this->products(),
            'inventory' => $this->inventory(),
            default => abort(404),
        };

        $filename = "vanzapack-{$resource}-".now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function orders(): array
    {
        $rows = Order::with('user')->latest()->cursor()->map(fn (Order $o) => [
            $o->number, $o->created_at->toDateTimeString(), $o->user?->name ?? 'Guest', $o->email,
            $o->status->value, $o->payment_status->value, $o->payment_method->value,
            $o->subtotal, $o->discount_total, $o->shipping_total, $o->tax_total, $o->grand_total,
        ]);

        return [['Number', 'Date', 'Customer', 'Email', 'Status', 'Payment', 'Method', 'Subtotal', 'Discount', 'Shipping', 'Tax', 'Total'], $rows];
    }

    private function customers(): array
    {
        $rows = User::whereHas('roles', fn ($q) => $q->where('name', 'customer'))
            ->withCount('orders')->withSum('orders as total', 'grand_total')->cursor()
            ->map(fn (User $u) => [$u->name, $u->email, $u->phone, $u->orders_count, $u->total ?? 0, $u->created_at->toDateString()]);

        return [['Name', 'Email', 'Phone', 'Orders', 'Lifetime value', 'Joined'], $rows];
    }

    private function products(): array
    {
        $rows = Product::with('category', 'brand', 'inventory')->cursor()->map(fn (Product $p) => [
            $p->sku, $p->name, $p->category?->name, $p->brand?->name, $p->price, $p->sale_price,
            $p->status->value, $p->inventory?->quantity ?? '—', $p->sales_count,
        ]);

        return [['SKU', 'Name', 'Category', 'Brand', 'Price', 'Sale price', 'Status', 'Stock', 'Sold'], $rows];
    }

    private function inventory(): array
    {
        $rows = Inventory::with('product', 'variant')->cursor()->map(fn (Inventory $i) => [
            $i->product?->sku, $i->product?->name, $i->variant?->sku, $i->quantity, $i->reserved, $i->available(), $i->low_stock_threshold,
        ]);

        return [['Product SKU', 'Product', 'Variant SKU', 'Quantity', 'Reserved', 'Available', 'Low threshold'], $rows];
    }
}
