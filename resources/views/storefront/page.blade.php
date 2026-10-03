<x-storefront-layout
    :title="$page->meta_title ?: $page->title"
    :metaDescription="$page->meta_description"
    :breadcrumbs="[['label' => $page->title]]">
    <div class="container-page py-10">
        <article class="mx-auto max-w-3xl">
            <h1 class="font-display text-3xl font-bold text-brand-800">{{ $page->title }}</h1>
            @if($page->featured_image)
                <img src="{{ media($page->featured_image) }}" alt="{{ $page->title }}" class="mt-6 w-full rounded-xl">
            @endif
            <div class="prose-cms mt-6">{!! $page->content !!}</div>
        </article>
    </div>
</x-storefront-layout>
