@props(['product'])

@if($product->isOnSale())
    <span class="font-semibold text-brand-800">{{ money($product->currentPrice()) }}</span>
    <span class="text-sm text-slate-400 line-through">{{ money($product->price) }}</span>
    <span class="badge bg-rose-100 text-rose-700">-{{ $product->discountPercent() }}%</span>
@else
    <span class="font-semibold text-brand-800">{{ money($product->currentPrice()) }}</span>
@endif
