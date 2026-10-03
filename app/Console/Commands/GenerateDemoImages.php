<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateDemoImages extends Command
{
    protected $signature = 'vanzapack:demo-images
        {--force : Replace existing images (including uploaded ones)}
        {--only= : Limit to products|categories|brands}
        {--count=2 : Images to generate per product}';

    protected $description = 'Generate local placeholder images (SVG) for products, categories and brands for demo/local use';

    /** @var array<int,array{0:string,1:string}> bg / accent pairs */
    private array $palettes = [
        ['#eef4e2', '#7aa82f'],
        ['#e6f2cf', '#5e8226'],
        ['#f3f9e8', '#95c93f'],
        ['#e8eef7', '#000066'],
        ['#f1f5f9', '#4b6622'],
        ['#f4f7ee', '#3f5620'],
    ];

    public function handle(): int
    {
        $only = $this->option('only');
        $disk = Storage::disk('public');
        $disk->makeDirectory('demo/products');
        $disk->makeDirectory('demo/categories');
        $disk->makeDirectory('demo/brands');

        if (! $only || $only === 'products') {
            $this->products($disk);
        }
        if (! $only || $only === 'categories') {
            $this->categories($disk);
        }
        if (! $only || $only === 'brands') {
            $this->brands($disk);
        }

        $this->newLine();
        $this->info('Done. Images served via /media/… (no storage:link needed).');

        return self::SUCCESS;
    }

    private function products($disk): void
    {
        $force = (bool) $this->option('force');
        $count = max(1, (int) $this->option('count'));
        $products = Product::with(['images', 'category', 'brand'])->get();

        $bar = $this->output->createProgressBar($products->count());
        $bar->setFormat(" Products %current%/%max% [%bar%] %message%");

        foreach ($products as $product) {
            $bar->setMessage(Str::limit($product->name, 30));

            $hasLocal = $product->images->contains(fn ($i) => ! Str::startsWith($i->path, 'http'));
            if ($hasLocal && ! $force) {
                $bar->advance();

                continue;
            }

            if ($force) {
                foreach ($product->images as $img) {
                    if (! Str::startsWith($img->path, 'http')) {
                        $disk->delete($img->path);
                    }
                    $img->delete();
                }
            } else {
                // drop the remote placeholders, keep any real uploads
                $product->images()->where('path', 'like', 'http%')->get()
                    ->each(fn ($img) => $img->delete());
            }

            [$bg, $accent] = $this->paletteFor($product->category?->name ?? $product->name);

            for ($n = 0; $n < $count; $n++) {
                $path = "demo/products/{$product->id}-{$n}.svg";
                $disk->put($path, $this->svg(
                    title: $product->name,
                    subtitle: $product->brand?->name ?? $product->category?->name ?? 'VanzaPack',
                    bg: $bg,
                    accent: $accent,
                    variant: $n,
                ));

                $product->images()->create([
                    'path' => $path,
                    'alt' => $product->name,
                    'is_primary' => $n === 0,
                    'position' => $n,
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function categories($disk): void
    {
        $force = (bool) $this->option('force');

        foreach (Category::all() as $category) {
            if ($category->image && ! Str::startsWith($category->image, 'http') && ! $force) {
                continue;
            }
            [$bg, $accent] = $this->paletteFor($category->name);
            $path = "demo/categories/{$category->id}.svg";
            $disk->put($path, $this->svg($category->name, 'Category', $bg, $accent, 0, wide: true));
            $category->forceFill(['image' => $path])->saveQuietly();
        }
        $this->line(' Categories updated.');
    }

    private function brands($disk): void
    {
        $force = (bool) $this->option('force');

        foreach (Brand::all() as $brand) {
            if ($brand->logo && ! Str::startsWith($brand->logo, 'http') && ! $force) {
                continue;
            }
            [$bg, $accent] = $this->paletteFor($brand->name);
            $path = "demo/brands/{$brand->id}.svg";
            $disk->put($path, $this->logoSvg($brand->name, $accent));
            $brand->forceFill(['logo' => $path])->saveQuietly();
        }
        $this->line(' Brands updated.');
    }

    private function paletteFor(string $seed): array
    {
        return $this->palettes[hexdec(substr(md5($seed), 0, 2)) % count($this->palettes)];
    }

    private function wrap(string $text, int $perLine = 20, int $maxLines = 3): array
    {
        $words = preg_split('/\s+/', trim($text));
        $lines = [];
        $line = '';
        foreach ($words as $word) {
            if (mb_strlen($line.' '.$word) > $perLine && $line !== '') {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $line === '' ? $word : $line.' '.$word;
            }
        }
        if ($line !== '') {
            $lines[] = $line;
        }
        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            $lines[$maxLines - 1] = Str::limit($lines[$maxLines - 1], $perLine - 1, '…');
        }

        return $lines;
    }

    private function svg(string $title, string $subtitle, string $bg, string $accent, int $variant, bool $wide = false): string
    {
        $w = 800;
        $h = $wide ? 450 : 800;
        $cx = $w / 2;

        $lines = $this->wrap($title, $wide ? 24 : 18, 3);
        $blockH = count($lines) * 52;
        $textTop = ($h - $blockH) / 2 + ($wide ? 20 : 90);

        $iconSize = 92;
        $iconX = $cx - $iconSize / 2;
        $iconY = $textTop - 150;

        $text = '';
        foreach ($lines as $i => $l) {
            $y = $textTop + $i * 52;
            $text .= '<text x="'.$cx.'" y="'.$y.'" text-anchor="middle" font-family="Poppins, Segoe UI, Arial, sans-serif" font-size="40" font-weight="700" fill="#2c3d13">'.htmlspecialchars($l, ENT_QUOTES).'</text>';
        }

        $dots = '';
        if (! $wide) {
            for ($i = 0; $i < 6; $i++) {
                $dcx = 90 + $i * 130 + $variant * 18;
                $dots .= '<circle cx="'.$dcx.'" cy="130" r="'.(6 + $i).'" fill="'.$accent.'" opacity="0.15"/>';
            }
        }

        $subY = $h - 60;
        $tagY = $h - 26;
        $sub = $this->esc($subtitle);

        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$w} {$h}" width="{$w}" height="{$h}">
          <defs>
            <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0" stop-color="{$bg}"/>
              <stop offset="1" stop-color="#ffffff"/>
            </linearGradient>
          </defs>
          <rect width="{$w}" height="{$h}" fill="url(#g)"/>
          {$dots}
          <rect x="600" y="-40" width="230" height="230" rx="40" fill="{$accent}" opacity="0.08" transform="rotate(18 700 80)"/>
          <g transform="translate({$iconX}, {$iconY})">
            <rect width="{$iconSize}" height="{$iconSize}" rx="20" fill="{$accent}"/>
            <path d="M18 30 L46 16 L74 30 L74 62 L46 78 L18 62 Z" fill="none" stroke="#ffffff" stroke-width="5" stroke-linejoin="round"/>
            <path d="M18 30 L46 44 L74 30 M46 44 L46 78" stroke="#ffffff" stroke-width="5" fill="none"/>
          </g>
          {$text}
          <text x="{$cx}" y="{$subY}" text-anchor="middle" font-family="Figtree, Segoe UI, Arial, sans-serif" font-size="24" fill="#5e8226">{$sub}</text>
          <text x="{$cx}" y="{$tagY}" text-anchor="middle" font-family="Figtree, Segoe UI, Arial, sans-serif" font-size="15" letter-spacing="2" fill="#94a3b8">VANZAPACK &#183; DEMO</text>
        </svg>
        SVG;
    }

    private function logoSvg(string $name, string $accent): string
    {
        $initial = mb_strtoupper(mb_substr($name, 0, 1));

        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 120" width="320" height="120">
          <rect width="320" height="120" fill="#ffffff"/>
          <circle cx="60" cy="60" r="34" fill="{$accent}"/>
          <text x="60" y="74" text-anchor="middle" font-family="Poppins, Arial, sans-serif" font-size="34" font-weight="800" fill="#ffffff">{$initial}</text>
          <text x="108" y="70" font-family="Poppins, Arial, sans-serif" font-size="26" font-weight="700" fill="#2c3d13">{$this->esc($name)}</text>
        </svg>
        SVG;
    }

    private function esc(string $s): string
    {
        return htmlspecialchars(Str::limit($s, 22, ''), ENT_QUOTES);
    }
}
