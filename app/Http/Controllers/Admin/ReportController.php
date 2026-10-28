<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Support\SalesReport;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = isset($data['from']) ? Carbon::parse($data['from']) : now()->subMonths(5)->startOfMonth();
        $to = isset($data['to']) ? Carbon::parse($data['to']) : now()->endOfMonth();

        $orders = Order::query()
            ->paid()
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        $revenue = (float) (clone $orders)->sum('total');
        $orderCount = (clone $orders)->count();

        $monthly = SalesReport::monthlySeries($from, $to);
        $peak = collect($monthly)->max('revenue') ?: 1;

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'totals' => [
                'revenue' => $revenue,
                'orders' => $orderCount,
                'average' => $orderCount > 0 ? $revenue / $orderCount : 0,
                'units' => (int) OrderItem::query()
                    ->whereHas('order', fn ($query) => $query->paid()->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]))
                    ->sum('quantity'),
                'buyers' => (clone $orders)->distinct()->count('user_id'),
                'catalogue' => Artwork::query()->count(),
            ],
            'monthly' => $monthly,
            'peak' => $peak,
            'topArtworks' => SalesReport::topArtworks($from, $to, 5),
            'categoryRevenue' => SalesReport::categoryRevenue($from, $to),
            'statusBreakdown' => Order::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->map(fn ($total) => (int) $total),
            'topCustomers' => User::query()
                ->where('role', User::ROLE_CUSTOMER)
                ->withSum(['orders as spent' => fn ($query) => $query->paid()->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])], 'total')
                ->withCount(['orders as paid_orders' => fn ($query) => $query->paid()])
                ->orderByDesc('spent')
                ->take(5)
                ->get(),
        ]);
    }
}
