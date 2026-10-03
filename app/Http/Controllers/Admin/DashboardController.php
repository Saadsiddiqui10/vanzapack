<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $paidStatuses = ['confirmed', 'processing', 'packed', 'shipped', 'delivered'];

        $revenueByMonth = Order::query()
            ->whereIn('status', $paidStatuses)
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->get(['created_at', 'grand_total'])
            ->groupBy(fn ($o) => $o->created_at->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('grand_total'))
            ->sortKeys();

        return view('admin.dashboard', [
            'totalRevenue' => Order::whereIn('status', $paidStatuses)->sum('grand_total'),
            'todayRevenue' => Order::whereIn('status', $paidStatuses)->whereDate('created_at', today())->sum('grand_total'),
            'monthRevenue' => Order::whereIn('status', $paidStatuses)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('grand_total'),
            'ordersCount' => Order::count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'customersCount' => User::whereHas('roles', fn ($q) => $q->where('name', 'customer'))->count(),
            'productsCount' => Product::count(),
            'lowStockCount' => Inventory::whereColumn('quantity', '<=', 'low_stock_threshold')->where('quantity', '>', 0)->count(),
            'outOfStockCount' => Inventory::where('quantity', '<=', 0)->count(),
            'recentOrders' => Order::with('user')->latest()->take(8)->get(),
            'topProducts' => Product::orderByDesc('sales_count')->take(6)->get(),
            'revenueByMonth' => $revenueByMonth,
            'ordersByStatus' => Order::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }
}
