<?php

use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\EnsureUserIsStaff;
use App\Http\Middleware\RedirectWwwToApex;
use App\Http\Middleware\ShareStorefrontData;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(RedirectWwwToApex::class);

        $middleware->alias([
            'staff' => EnsureUserIsStaff::class,
            'permission' => EnsurePermission::class,
            'storefront' => ShareStorefrontData::class,
        ]);

        // Anonymous cart / recently-viewed tokens are opaque UUIDs — safe to leave unencrypted.
        $middleware->encryptCookies(except: ['gc_cart', 'gc_rv']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $isAdmin = fn (Request $request) => $request->routeIs('admin.*') && ! $request->expectsJson();

        // Database-level failures → a plain-English flash instead of a 500 in the admin panel.
        $exceptions->render(function (QueryException $e, Request $request) use ($isAdmin) {
            if (! $isAdmin($request)) {
                return null;
            }

            report($e);

            $message = match ((int) ($e->errorInfo[1] ?? 0)) {
                1451 => 'This item is still linked to other records (products, orders, …). Remove or reassign those first, then try again.',
                1452 => 'A related record is missing or invalid. Please pick a valid option and retry.',
                1062 => 'That value is already in use — it needs to be unique (e.g. SKU, slug or code).',
                1406 => 'One of the fields is longer than allowed. Please shorten it.',
                default => 'The database could not save that change. Please check your input and try again.',
            };

            return back()->withInput()->with('error', $message);
        });

        // Oversized uploads (bigger than the server's post_max_size).
        $exceptions->render(function (PostTooLargeException $e, Request $request) use ($isAdmin) {
            if (! $isAdmin($request)) {
                return null;
            }

            return back()->with('error', 'That upload is larger than the server allows. Use a smaller file, or upload fewer images at once.');
        });

        // Missing record via route-model binding on an admin sub-page → back with a note, not a bare 404.
        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($isAdmin) {
            if (! $isAdmin($request) || $request->routeIs('admin.*.index') || $request->routeIs('admin.*.show')) {
                return null;
            }

            return redirect()->back()->with('error', 'That record no longer exists — it may have been deleted.');
        });

        // Permission / policy failures in the admin panel.
        $exceptions->render(function (AuthorizationException $e, Request $request) use ($isAdmin) {
            if (! $isAdmin($request)) {
                return null;
            }

            return redirect()->route('admin.dashboard')->with('error', $e->getMessage() ?: 'You do not have permission to do that.');
        });

        // Any other unexpected admin error (only when debug is off — otherwise show the real trace).
        $exceptions->render(function (\Throwable $e, Request $request) use ($isAdmin) {
            if (! $isAdmin($request) || config('app.debug')) {
                return null;
            }
            if ($e instanceof ValidationException || $e instanceof HttpExceptionInterface) {
                return null;
            }

            report($e);

            return back()->withInput()->with('error', 'Something went wrong while processing that request. The error has been logged.');
        });

        // Keep unhandled exception reports out of the log for expected domain errors.
        $exceptions->dontReport([
            \App\Exceptions\CouponException::class,
            \App\Exceptions\CheckoutException::class,
        ]);
    })->create();
