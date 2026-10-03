<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.coupons.index', [
            'coupons' => Coupon::withCount('usages')->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.coupons.form', [
            'coupon' => new Coupon(['type' => 'percentage', 'scope' => 'global', 'is_active' => true]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Coupon::create($this->data($request));

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.form', [
            'coupon' => $coupon,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->data($request, $coupon));

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }

    private function data(Request $request, ?Coupon $coupon = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:60', Rule::unique('coupons', 'code')->ignore($coupon)],
            'description' => ['nullable', 'string', 'max:200'],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0'],
            'scope' => ['required', 'in:global,product,category'],
            'min_order_total' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'product_ids_text' => ['nullable', 'string'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['code'] = strtoupper($data['code']);
        $data['product_ids'] = $data['scope'] === 'product'
            ? Product::whereIn('sku', array_filter(array_map('trim', explode(',', (string) $request->input('product_ids_text')))))->pluck('id')->all()
            : null;
        $data['category_ids'] = $data['scope'] === 'category' ? ($data['category_ids'] ?? []) : null;
        unset($data['product_ids_text']);

        return $data;
    }
}
