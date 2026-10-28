<?php

namespace App\Providers;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Artiva uses Bootstrap 5 for its layouts, so paginate with Bootstrap markup.
        Paginator::useBootstrapFive();

        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        // Shared storefront navigation data: categories plus live cart and
        // wishlist counters for the navbar. No database work happens in views.
        View::composer('layouts.app', function ($view) {
            $user = auth()->user();

            $view->with([
                'navCategories' => Category::query()->active()->orderBy('name')->get(),
                'navCartCount' => $user
                    ? CartItem::query()->whereHas('cart', fn ($query) => $query->where('user_id', $user->id))->count()
                    : 0,
                'navWishlistCount' => $user
                    ? Wishlist::query()->where('user_id', $user->id)->count()
                    : 0,
            ]);
        });

        View::composer(['layouts.admin', 'components.admin-sidebar'], function ($view) {
            $view->with([
                'navPendingOrders' => Order::query()
                    ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])
                    ->count(),
            ]);
        });
    }
}
