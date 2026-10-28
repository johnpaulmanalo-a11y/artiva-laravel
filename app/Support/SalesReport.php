<?php

namespace App\Support;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesReport
{
    /**
     * Paid-order revenue grouped by month.
     *
     * Aggregation happens in PHP so the report works on MySQL, MariaDB and
     * SQLite without database-specific date functions.
     *
     * @return array<int, array{period: string, label: string, revenue: float, orders: int}>
     */
    public static function monthlySeries(?Carbon $from = null, ?Carbon $to = null): array
    {
        /** @var Collection<string, Collection<int, Order>> $grouped */
        $grouped = Order::query()
            ->paid()
            ->when($from, fn ($query) => $query->where('created_at', '>=', $from->copy()->startOfDay()))
            ->when($to, fn ($query) => $query->where('created_at', '<=', $to->copy()->endOfDay()))
            ->get(['created_at', 'total'])
            ->groupBy(fn (Order $order) => $order->created_at->format('Y-m'));

        return $grouped
            ->map(function (Collection $orders, string $period) {
                $date = Carbon::createFromFormat('Y-m', $period);

                return [
                    'period' => $period,
                    'label' => $date->format('M Y'),
                    'revenue' => (float) $orders->sum(fn (Order $order) => (float) $order->total),
                    'orders' => $orders->count(),
                ];
            })
            ->sortKeys()
            ->values()
            ->all();
    }

    /**
     * Revenue per category for paid orders inside an optional period.
     *
     * @return Collection<int, object>
     */
    public static function categoryRevenue(?Carbon $from = null, ?Carbon $to = null): Collection
    {
        return \App\Models\OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('artworks', 'artworks.id', '=', 'order_items.artwork_id')
            ->join('categories', 'categories.id', '=', 'artworks.category_id')
            ->where('orders.payment_status', Order::PAYMENT_PAID)
            ->when($from, fn ($query) => $query->where('orders.created_at', '>=', $from->copy()->startOfDay()))
            ->when($to, fn ($query) => $query->where('orders.created_at', '<=', $to->copy()->endOfDay()))
            ->groupBy('categories.id', 'categories.name')
            ->selectRaw('categories.name as category_name, SUM(order_items.subtotal) as revenue, SUM(order_items.quantity) as units')
            ->orderByDesc('revenue')
            ->get();
    }

    /**
     * Best selling artworks for paid orders inside an optional period.
     *
     * @return Collection<int, object>
     */
    public static function topArtworks(?Carbon $from = null, ?Carbon $to = null, int $limit = 5): Collection
    {
        return \App\Models\OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', Order::PAYMENT_PAID)
            ->when($from, fn ($query) => $query->where('orders.created_at', '>=', $from->copy()->startOfDay()))
            ->when($to, fn ($query) => $query->where('orders.created_at', '<=', $to->copy()->endOfDay()))
            ->groupBy('order_items.artwork_id', 'order_items.title')
            ->selectRaw('order_items.artwork_id, order_items.title, SUM(order_items.quantity) as units, SUM(order_items.subtotal) as revenue')
            ->orderByDesc('units')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();
    }
}
