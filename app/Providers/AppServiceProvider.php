<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Services\SettingsRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsRepository::class);
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        // Super-admins bypass every ability check.
        Gate::before(function ($user, string $ability) {
            return $user->hasRole(UserRole::SuperAdmin) ? true : null;
        });

        // Generic permission gate: Gate::allows('products.edit')
        Gate::define('permission', fn ($user, string $permission) => $user->hasPermission($permission));

        Order::observe(OrderObserver::class);
    }
}
