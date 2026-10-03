<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to, $preset] = $this->range($request);

        $paid = ['confirmed', 'processing', 'packed', 'shipped', 'delivered'];

        $orders = Order::whereBetween('created_at', [$from, $to]);
        $revenue = (clone $orders)->whereIn('status', $paid)->sum('grand_total');
        $orderCount = (clone $orders)->count();

        $topProducts = OrderItem::query()
            ->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$from, $to])->whereIn('status', $paid))
            ->selectRaw('name, SUM(quantity) as qty, SUM(line_total) as total')
            ->groupBy('name')->orderByDesc('qty')->take(10)->get();

        $dailyRevenue = Order::query()
            ->whereBetween('created_at', [$from, $to])->whereIn('status', $paid)
            ->get(['created_at', 'grand_total'])
            ->groupBy(fn ($o) => $o->created_at->format('Y-m-d'))
            ->map(fn ($group) => (float) $group->sum('grand_total'))
            ->sortKeys();

        return view('admin.reports.index', [
            'from' => $from, 'to' => $to, 'preset' => $preset,
            'revenue' => $revenue,
            'orderCount' => $orderCount,
            'avgOrder' => $orderCount ? $revenue / $orderCount : 0,
            'newCustomers' => User::whereHas('roles', fn ($q) => $q->where('name', 'customer'))
                ->whereBetween('created_at', [$from, $to])->count(),
            'topProducts' => $topProducts,
            'dailyRevenue' => $dailyRevenue,
            'statusBreakdown' => (clone $orders)->selectRaw('status, COUNT(*) c, SUM(grand_total) total')->groupBy('status')->get(),
        ]);
    }

    private function range(Request $request): array
    {
        $preset = $request->input('preset', 'last_30');

        return match ($preset) {
            'today' => [today(), now(), $preset],
            'yesterday' => [today()->subDay(), today()->subSecond(), $preset],
            'last_7' => [today()->subDays(6), now(), $preset],
            'this_month' => [now()->startOfMonth(), now(), $preset],
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth(), $preset],
            'custom' => [
                Carbon::parse($request->input('from', today()->subDays(30))),
                Carbon::parse($request->input('to', now()))->endOfDay(),
                $preset,
            ],
            default => [today()->subDays(29), now(), 'last_30'],
        };
    }
}
