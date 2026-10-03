<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventories = Inventory::query()
            ->with(['product', 'variant.attributeValues'])
            ->when($request->input('filter') === 'low', fn ($q) => $q->whereColumn('quantity', '<=', 'low_stock_threshold')->where('quantity', '>', 0))
            ->when($request->input('filter') === 'out', fn ($q) => $q->where('quantity', '<=', 0))
            ->when($request->string('q')->toString(), fn ($q, $t) => $q->whereHas('product', fn ($p) => $p->where('name', 'like', "%{$t}%")->orWhere('sku', 'like', "%{$t}%")))
            ->orderBy('quantity')
            ->paginate(30)
            ->withQueryString();

        return view('admin.inventory.index', compact('inventories'));
    }

    public function update(Request $request, Inventory $inventory, InventoryService $service)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:200'],
        ]);

        $inventory->update(['low_stock_threshold' => $data['low_stock_threshold']]);
        if ($data['quantity'] !== $inventory->quantity) {
            $service->adjust($inventory, $data['quantity'], $data['note'] ?? 'Manual adjustment');
        }

        return back()->with('success', 'Stock updated.');
    }

    public function history(Inventory $inventory)
    {
        $inventory->load(['product', 'variant', 'movements.user']);

        return view('admin.inventory.history', compact('inventory'));
    }
}
