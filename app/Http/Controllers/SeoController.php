<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Throwable;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('shop.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => route('brands.index'), 'priority' => '0.5', 'changefreq' => 'weekly'],
            ['loc' => route('contact.show'), 'priority' => '0.5', 'changefreq' => 'monthly'],
        ]);

        $this->collect($urls, Category::query()->where('is_active', true), 'category.show', '0.7', 'weekly');
        $this->collect($urls, Product::query()->where('status', 'active'), 'product.show', '0.8', 'weekly');
        $this->collect($urls, Page::query()->where('is_published', true), 'page.show', '0.4', 'monthly');

        return response()
            ->view('seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Add one URL per record. A single bad record (missing slug, odd timestamp)
     * is logged and skipped so it can never take the whole sitemap down.
     */
    private function collect(Collection $urls, Builder $query, string $route, string $priority, string $changefreq): void
    {
        try {
            $query->whereNotNull('slug')->where('slug', '!=', '')
                ->select(['id', 'slug', 'updated_at', 'created_at'])
                ->orderBy('id')
                ->chunk(500, function ($records) use ($urls, $route, $priority, $changefreq) {
                    foreach ($records as $record) {
                        try {
                            $date = $record->updated_at ?? $record->created_at;
                            $urls->push(array_filter([
                                'loc' => route($route, $record->slug),
                                'lastmod' => $date?->toAtomString(),
                                'priority' => $priority,
                                'changefreq' => $changefreq,
                            ]));
                        } catch (Throwable $e) {
                            report($e);
                        }
                    }
                });
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /account',
            'Disallow: /checkout',
            'Disallow: /cart',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /password',
            'Disallow: /search',
            'Disallow: /*?*sort=',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
