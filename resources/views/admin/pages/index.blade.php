<x-admin-layout title="Pages" active="pages">
    <x-admin.head title="CMS Pages">
        <x-slot:actions><a href="{{ route('admin.pages.create') }}" class="btn-primary py-2 text-sm">New page</a></x-slot:actions>
    </x-admin.head>
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                <tr><th class="px-4 py-3">Title</th><th class="px-4 py-3">Slug</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($pages as $page)
                    <tr>
                        <td class="px-4 py-3 font-medium text-brand-800">{{ $page->title }}</td>
                        <td class="px-4 py-3 text-slate-500">/page/{{ $page->slug }}</td>
                        <td class="px-4 py-3"><span class="badge {{ $page->is_published ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-600' }}">{{ $page->is_published ? 'Published' : 'Draft' }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.pages.edit', $page) }}" class="text-brand-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="inline" onsubmit="return confirm('Delete page?')">@csrf @method('DELETE')<button class="ml-2 text-rose-500 hover:underline">Delete</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $pages->links() }}</div>
</x-admin-layout>
