<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'customer'))
            ->withCount('orders')
            ->withSum('orders as orders_total', 'grand_total')
            ->when($request->string('q')->toString(), fn ($q, $t) => $q->where('name', 'like', "%{$t}%")->orWhere('email', 'like', "%{$t}%"))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $user)
    {
        $user->load(['orders' => fn ($q) => $q->latest(), 'addresses', 'reviews.product']);

        return view('admin.customers.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'is_active' => ['boolean'],
            'customer_group' => ['required', 'string', 'max:40'],
        ]);

        $user->update([
            'is_active' => $request->boolean('is_active'),
            'customer_group' => $data['customer_group'],
        ]);

        return back()->with('success', 'Customer updated.');
    }
}
