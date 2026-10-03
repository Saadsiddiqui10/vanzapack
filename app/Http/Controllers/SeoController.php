<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect();

        $urls->push(['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily']);
        $urls->push(['loc' => route('shop.index'), 'priority' => '0.9', 'changefreq' => 'daily']);
        $urls->push(['loc' => route('brands.index'), 'priority' => '0.5', 'changefreq' => 'weekly']);

        Category::query()->where('is_active', true)->get()->each(fn (Category $c) => $urls->push([
            'loc' => route('category.show', $c->slug),
            'lastmod' => $c->updated_at->toAtomString(),
            'priority' => '0.7', 'changefreq' => 'weekly',
        ]));

        Product::query()->where('status', 'active')->get()->each(fn (Product $p) => $urls->push([
            'loc' => route('product.show', $p->slug),
            'lastmod' => $p->updated_at->toAtomString(),
            'priority' => '0.8', 'changefreq' => 'weekly',
        ]));

        Page::query()->where('is_published', true)->get()->each(fn (Page $page) => $urls->push([
            'loc' => route('page.show', $page->slug),
            'lastmod' => $page->updated_at->toAtomString(),
            'priority' => '0.4', 'changefreq' => 'monthly',
        ]));

        return response()
            ->view('seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /account',
            'Disallow: /checkout',
            'Disallow: /cart',
            'Allow: /',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }
}
