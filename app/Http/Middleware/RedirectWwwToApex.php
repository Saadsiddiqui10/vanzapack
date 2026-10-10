<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * www.vanzapack.com → vanzapack.com (permanent), so search engines see one site
 * instead of two copies, and rankings / favicon are not split between them.
 */
class RedirectWwwToApex
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if (str_starts_with($host, 'www.')) {
            // Behind Cloudflare the app may see plain http, so prefer the scheme of APP_URL (https).
            $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: $request->getScheme();
            $target = $scheme.'://'.substr($host, 4).$request->getRequestUri();

            // 301 for normal page loads; 308 keeps the method + body for form posts.
            return redirect()->away($target, $request->isMethod('GET') || $request->isMethod('HEAD') ? 301 : 308);
        }

        return $next($request);
    }
}
