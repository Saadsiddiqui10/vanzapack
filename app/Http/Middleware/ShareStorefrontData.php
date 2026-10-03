<?php

namespace App\Http\Middleware;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\MenuItem;
use App\Services\CartService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareStorefrontData
{
    public function __construct(private readonly CartService $cart) {}

    public function handle(Request $request, Closure $next): Response
    {
        View::share('navCategories', Category::query()
            ->active()
            ->roots()
            ->with(['children' => fn ($q) => $q->active()->withCount('products')->orderBy('position')
                ->with(['children' => fn ($c) => $c->active()->withCount('products')->orderBy('position')])])
            ->orderBy('position')
            ->get());

        // menu_items / announcements tables ship in a later migration — guard so the
        // storefront still renders if the migration has not run yet.
        View::share('navMenuItems', Schema::hasTable('menu_items')
            ? MenuItem::active()->get()
            : collect());

        View::share('announcements', Schema::hasTable('announcements')
            ? Announcement::active()->get()
            : collect());

        View::share('cartCount', $this->cart->itemCount());

        return $next($request);
    }
}
