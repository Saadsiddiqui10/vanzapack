<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    public function index()
    {
        return view('admin.shipping.index', [
            'methods' => ShippingMethod::orderBy('position')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.shipping.form', ['method' => new ShippingMethod(['is_active' => true, 'zone' => 'Domestic'])]);
    }

    public function store(Request $request)
    {
        ShippingMethod::create($this->data($request));

        return redirect()->route('admin.shipping-methods.index')->with('success', 'Shipping method created.');
    }

    public function edit(ShippingMethod $shipping_method)
    {
        return view('admin.shipping.form', ['method' => $shipping_method]);
    }

    public function update(Request $request, ShippingMethod $shipping_method)
    {
        $shipping_method->update($this->data($request));

        return redirect()->route('admin.shipping-methods.index')->with('success', 'Shipping method updated.');
    }

    public function destroy(ShippingMethod $shipping_method)
    {
        $shipping_method->delete();

        return back()->with('success', 'Shipping method deleted.');
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:250'],
            'zone' => ['required', 'string', 'max:60'],
            'country' => ['nullable', 'string', 'size:2'],
            'city' => ['nullable', 'string', 'max:80'],
            'price' => ['required', 'numeric', 'min:0'],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0'],
            'estimated_delivery' => ['nullable', 'string', 'max:80'],
            'is_active' => ['boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['position'] = $data['position'] ?? 0;
        $data['country'] = $data['country'] ? strtoupper($data['country']) : null;

        return $data;
    }
}
