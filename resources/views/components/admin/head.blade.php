@props(['title', 'back' => null])

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        @if($back)<a href="{{ $back }}" class="text-sm text-slate-400 hover:text-brand-600">← Back</a>@endif
        <h2 class="font-display text-xl font-bold text-brand-800">{{ $title }}</h2>
    </div>
    @if(isset($actions))<div class="flex flex-wrap gap-2 [&>*]:whitespace-nowrap">{{ $actions }}</div>@endif
</div>
