<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves files from storage/app/public without relying on the
 * `php artisan storage:link` symlink (which is unreliable on shared hosting).
 */
class MediaController extends Controller
{
    public function __invoke(string $path): Response
    {
        abort_if(str_contains($path, '..') || str_contains($path, "\0"), 404);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($path), 404);

        $headers = [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ];

        // SVGs can carry scripts — neutralise them (matters for admin-uploaded files).
        if (Str::endsWith(Str::lower($path), '.svg')) {
            $headers['Content-Type'] = 'image/svg+xml';
            $headers['Content-Security-Policy'] = "default-src 'none'; style-src 'unsafe-inline'; sandbox";
        }

        return $disk->response($path, null, $headers);
    }
}
