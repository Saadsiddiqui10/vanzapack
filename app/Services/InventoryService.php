<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Notifications\LowStockNotification;
use App\Support\Notify;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function for(Product $product, ?ProductVariant $variant = null): Inventory
    {
        return Inventory::firstOrCreate(
            [
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
            ],
            [
                'quantity' => 0,
                'reserved' => 0,
                'low_stock_threshold' => (int) settings('low_stock_threshold', 5),
            ],
        );
    }

    public function available(Product $product, ?ProductVariant $variant = null): int
    {
        if (! $product->track_inventory) {
            return PHP_INT_MAX;
        }

        return $this->for($product, $variant)->available();
    }

    public function canFulfill(Product $product, ?ProductVariant $variant, int $qty): bool
    {
        if (! $product->track_inventory || $product->allow_backorder) {
            return true;
        }

        return $this->available($product, $variant) >= $qty;
    }

    /** Permanently remove stock (order fulfilled / shipped). */
    public function deduct(Product $product, ?ProductVariant $variant, int $qty, ?Model $reference = null, ?string $note = null): void
    {
        if (! $product->track_inventory) {
            return;
        }

        DB::transaction(function () use ($product, $variant, $qty, $reference, $note) {
            $inventory = $this->for($product, $variant);
            $inventory->lockForUpdate()->first();
            $inventory->decrement('quantity', $qty);
            $inventory->reserved = max(0, $inventory->reserved - $qty);
            $inventory->save();

            $this->record($inventory, StockMovementType::Sale, -$qty, $reference, $note);
            $this->checkLowStock($inventory);
        });
    }

    public function restock(Product $product, ?ProductVariant $variant, int $qty, ?string $note = null): void
    {
        DB::transaction(function () use ($product, $variant, $qty, $note) {
            $inventory = $this->for($product, $variant);
            $inventory->increment('quantity', $qty);
            $this->record($inventory, StockMovementType::Restock, $qty, null, $note);
        });
    }

    public function adjust(Inventory $inventory, int $newQuantity, ?string $note = null): void
    {
        DB::transaction(function () use ($inventory, $newQuantity, $note) {
            $delta = $newQuantity - $inventory->quantity;
            $inventory->quantity = $newQuantity;
            $inventory->save();
            $this->record($inventory, StockMovementType::Adjustment, $delta, null, $note);
            $this->checkLowStock($inventory);
        });
    }

    public function returnToStock(Product $product, ?ProductVariant $variant, int $qty, ?Model $reference = null): void
    {
        DB::transaction(function () use ($product, $variant, $qty, $reference) {
            $inventory = $this->for($product, $variant);
            $inventory->increment('quantity', $qty);
            $this->record($inventory, StockMovementType::ReturnToStock, $qty, $reference, 'Order return');
        });
    }

    private function record(Inventory $inventory, StockMovementType $type, int $delta, ?Model $reference, ?string $note): void
    {
        $inventory->movements()->create([
            'type' => $type->value,
            'quantity' => $delta,
            'balance_after' => $inventory->quantity,
            'reference_type' => $reference?->getMorphClass(),
            'reference_id' => $reference?->getKey(),
            'user_id' => auth()->id(),
            'note' => $note,
        ]);
    }

    private function checkLowStock(Inventory $inventory): void
    {
        if ($inventory->isLow()) {
            Notify::staff(new LowStockNotification($inventory));
        }
    }
}
