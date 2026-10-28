<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Support\SalesReport;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $paidOrders = Order::query()->paid();
        $paidCount = (clone $paidOrders)->count();
        $revenue = (float) (clone $paidOrders)->sum('total');

        return view('admin.dashboard', [
            'metrics' => [
                'revenue' => $revenue,
                'paid_orders' => $paidCount,
                'orders' => Order::query()->count(),
                'customers' => User::query()->where('role', User::ROLE_CUSTOMER)->count(),
                'artworks' => Artwork::query()->count(),
                'pending_orders' => Order::query()
                    ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])
                    ->count(),
                'average_order' => $paidCount > 0 ? $revenue / $paidCount : 0,
            ],
            'recentOrders' => Order::query()
                ->with('user')
                ->withCount('items')
                ->latest()
                ->take(6)
                ->get(),
            'topArtworks' => Artwork::query()
                ->with('category')
                ->withCount(['orderItems as sold_count' => function ($query) {
                    $query->whereHas('order', fn ($order) => $order->paid());
                }])
                ->orderByDesc('sold_count')
                ->orderBy('title')
                ->take(5)
                ->get(),
            'salesByMonth' => array_slice(SalesReport::monthlySeries(now()->subMonths(11)->startOfMonth()), -6),
            'categoryTotals' => Category::query()
                ->withCount('artworks')
                ->orderByDesc('artworks_count')
                ->take(6)
                ->get(),
            'inactiveArtworks' => Artwork::query()
                ->where('is_active', false)
                ->with('category')
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }
}
