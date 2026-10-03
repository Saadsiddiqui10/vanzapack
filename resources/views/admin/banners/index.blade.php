<x-admin-layout title="Banners" active="banners">
    <x-admin.head title="Banners">
        <x-slot:actions><a href="{{ route('admin.banners.create') }}" class="btn-primary py-2 text-sm">New banner</a></x-slot:actions>
    </x-admin.head>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($banners as $banner)
            <div class="rounded-xl border border-slate-200 bg-white p-3">
                <img src="{{ $banner->imageUrl() }}" alt="" class="aspect-video w-full rounded object-cover">
                <div class="mt-2 flex items-center justify-between text-sm">
                    <span class="badge bg-slate-100 capitalize text-slate-600">{{ $banner->placement }}</span>
                    <span class="badge {{ $banner->is_active ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-600' }}">{{ $banner->is_active ? 'Active' : 'Off' }}</span>
                </div>
                <p class="mt-1 line-clamp-1 text-sm font-medium text-brand-800">{{ $banner->title }}</p>
                <div class="mt-2 flex gap-3 text-sm">
                    <a href="{{ route('admin.banners.edit', $banner) }}" class="text-brand-600 hover:underline">Edit</a>
                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('Delete banner?')">@csrf @method('DELETE')<button class="text-rose-500 hover:underline">Delete</button></form>
                </div>
            </div>
        @endforeach
    </div>
</x-admin-layout>
