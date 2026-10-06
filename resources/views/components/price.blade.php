@props(['product', 'showDiscount' => true])

@if($product->isOnSale())
    <span class="font-semibold text-brand-800">{{ money($product->currentPrice()) }}</span>
    <span class="text-sm text-slate-400 line-through">{{ money($product->price) }}</span>
    @if($showDiscount)
        <span class="badge bg-rose-100 text-rose-700">-{{ $product->discountPercent() }}%</span>
    @endif
@else
    <span class="font-semibold text-brand-800">{{ money($product->currentPrice()) }}</span>
@endif
