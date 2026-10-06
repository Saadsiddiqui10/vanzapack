<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use ZipArchive;

/**
 * Attaches photos to products during CSV import.
 *
 * Sources are image URLs or file names inside an uploaded ZIP. ZIP files can
 * also be matched to products automatically when they are named after the
 * product's SKU or name, e.g. "GC-10001.jpg", "GC-10001-2.jpg", "Kraft Paper Cups.png".
 */
class ProductImageImporter
{
    /** Generated demo images that real photos should replace. */
    public const PLACEHOLDER_PATTERN = 'catalog/products/%.svg';

    private const MAX_BYTES = 15 * 1024 * 1024;

    private const MAX_DIMENSION = 1600;

    private ?ZipArchive $zip = null;

    /** @var array<string,int> lower-case basename => zip index */
    private array $byBasename = [];

    /** @var array<string,array<int,array{0:int,1:int}>> match key => [[order, zip index], ...] */
    private array $byKey = [];

    public static function zipSupported(): bool
    {
        return class_exists(ZipArchive::class);
    }

    public function openZip(string $path): bool
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            return false;
        }

        $this->zip = $zip;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = (string) $zip->getNameIndex($i);
            $base = basename(str_replace('\\', '/', $name));

            // Skip folders and macOS metadata (__MACOSX/, ._file.jpg).
            if ($base === '' || str_ends_with($name, '/') || str_starts_with($base, '.') || str_contains($name, '__MACOSX')) {
                continue;
            }
            if (! preg_match('/\.(jpe?g|png|webp|gif)$/i', $base)) {
                continue;
            }

            $this->byBasename[mb_strtolower($base)] = $i;

            $stem = mb_strtolower(pathinfo($base, PATHINFO_FILENAME));
            $this->addKey($stem, 0, $i);
            $this->addKey(Str::slug($stem), 0, $i);

            // "GC-10001-2", "GC-10001_3", "Kraft Cups (2)" => extra photos of the same product.
            if (preg_match('/^(.*?)(?:[-_ ]+|\s*\()(\d{1,2})\)?$/', $stem, $m) && $m[1] !== '') {
                $this->addKey($m[1], (int) $m[2], $i);
                $this->addKey(Str::slug($m[1]), (int) $m[2], $i);
            }
        }

        return true;
    }

    public function hasZip(): bool
    {
        return $this->zip !== null;
    }

    public function close(): void
    {
        $this->zip?->close();
        $this->zip = null;
    }

    /**
     * ZIP entries that belong to a product, matched by SKU first, then by product name.
     *
     * @return list<string> source references for attach()
     */
    public function zipSourcesFor(Product $product): array
    {
        foreach ([mb_strtolower($product->sku), Str::slug($product->sku), Str::slug($product->name)] as $key) {
            if ($key !== '' && isset($this->byKey[$key])) {
                $entries = $this->byKey[$key];
                usort($entries, fn ($a, $b) => $a[0] <=> $b[0]);

                return array_map(fn ($e) => 'zip:'.$e[1], array_values(array_unique($entries, SORT_REGULAR)));
            }
        }

        return [];
    }

    /**
     * @return list<string> source references parsed from a CSV "images" cell
     */
    public function parseCell(?string $cell): array
    {
        $parts = preg_split('/\s*[|;\n]\s*|\s*,\s*(?=https?:\/\/)|\s*,\s*(?=[^,]+\.(?:jpe?g|png|webp|gif)\b)/i', trim((string) $cell)) ?: [];

        return array_values(array_filter(array_map('trim', $parts), fn ($p) => $p !== ''));
    }

    /**
     * Store the given images and attach them to the product.
     *
     * Real photos replace generated placeholders; with $replaceAll every existing image is replaced.
     * Existing images are only removed once at least one new image was stored successfully.
     *
     * @param  list<string>  $sources  URLs, ZIP file names, or "zip:<index>" references
     * @param  list<string>  $errors
     * @return int number of images attached
     */
    public function attach(Product $product, array $sources, bool $replaceAll, array &$errors, string $context): int
    {
        $stored = [];
        foreach ($sources as $source) {
            try {
                $binary = $this->read($source);
                if ($binary === null) {
                    $errors[] = "{$context}: image \"".Str::limit($source, 80).'" not found'.($this->hasZip() ? ' in the ZIP or at that URL.' : ' (upload a ZIP or use a full https:// URL).');

                    continue;
                }

                $path = $this->store($binary);
                if ($path === null) {
                    $errors[] = "{$context}: \"".Str::limit($source, 80).'" is not a valid JPG, PNG, WEBP or GIF image (max 15 MB).';

                    continue;
                }

                $stored[] = $path;
            } catch (Throwable $e) {
                report($e);
                $errors[] = "{$context}: could not load \"".Str::limit($source, 80).'": '.Str::limit($e->getMessage(), 100);
            }
        }

        if ($stored === []) {
            return 0;
        }

        $old = $product->images()->get()->filter(fn (ProductImage $img) => $replaceAll || Str::is('catalog/products/*.svg', $img->path));
        foreach ($old as $img) {
            if (! Str::startsWith($img->path, ['http://', 'https://'])) {
                Storage::disk('public')->delete($img->path);
            }
            $img->delete();
        }

        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        $position = (int) $product->images()->max('position');

        foreach ($stored as $i => $path) {
            $product->images()->create([
                'path' => $path,
                'alt' => $product->name,
                'is_primary' => ! $hasPrimary && $i === 0,
                'position' => $position + $i + 1,
            ]);
        }

        return count($stored);
    }

    private function read(string $source): ?string
    {
        if (str_starts_with($source, 'zip:')) {
            return $this->zip?->getFromIndex((int) substr($source, 4)) ?: null;
        }

        if (preg_match('#^https?://#i', $source)) {
            $response = Http::timeout(25)->withHeaders(['User-Agent' => 'Mozilla/5.0 (VanzaPack product import)'])->get($source);

            return $response->successful() ? $response->body() : null;
        }

        // A file name (optionally with folders) inside the uploaded ZIP.
        $key = mb_strtolower(basename(str_replace('\\', '/', $source)));
        if ($this->zip && isset($this->byBasename[$key])) {
            return $this->zip->getFromIndex($this->byBasename[$key]) ?: null;
        }

        return null;
    }

    /**
     * Validate, downscale to a sensible size and save as WebP when possible.
     */
    private function store(string $binary): ?string
    {
        if (strlen($binary) > self::MAX_BYTES) {
            return null;
        }

        $info = @getimagesizefromstring($binary);
        $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
        if (! $info || ! isset($types[$info[2]])) {
            return null;
        }

        $name = 'products/'.Str::random(40);

        if (function_exists('imagewebp') && $info[2] !== IMAGETYPE_GIF && ($image = @imagecreatefromstring($binary))) {
            [$w, $h] = [imagesx($image), imagesy($image)];
            $scale = min(1, self::MAX_DIMENSION / max($w, $h));
            if ($scale < 1) {
                $resized = imagecreatetruecolor((int) round($w * $scale), (int) round($h * $scale));
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, imagesx($resized), imagesy($resized), $w, $h);
                imagedestroy($image);
                $image = $resized;
            } else {
                imagepalettetotruecolor($image);
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }

            ob_start();
            $ok = imagewebp($image, null, 82);
            $webp = ob_get_clean();
            imagedestroy($image);

            if ($ok && $webp) {
                Storage::disk('public')->put($name.'.webp', $webp);

                return $name.'.webp';
            }
        }

        Storage::disk('public')->put($name.'.'.$types[$info[2]], $binary);

        return $name.'.'.$types[$info[2]];
    }

    private function addKey(string $key, int $order, int $index): void
    {
        if ($key !== '') {
            $this->byKey[$key][] = [$order, $index];
        }
    }
}
