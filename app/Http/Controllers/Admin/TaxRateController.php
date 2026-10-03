<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    public function index()
    {
        return view('admin.tax.index', ['rates' => TaxRate::orderBy('priority')->get()]);
    }

    public function create()
    {
        return view('admin.tax.form', ['rate' => new TaxRate(['is_active' => true, 'tax_class' => 'standard'])]);
    }

    public function store(Request $request)
    {
        TaxRate::create($this->data($request));

        return redirect()->route('admin.tax-rates.index')->with('success', 'Tax rate created.');
    }

    public function edit(TaxRate $tax_rate)
    {
        return view('admin.tax.form', ['rate' => $tax_rate]);
    }

    public function update(Request $request, TaxRate $tax_rate)
    {
        $tax_rate->update($this->data($request));

        return redirect()->route('admin.tax-rates.index')->with('success', 'Tax rate updated.');
    }

    public function destroy(TaxRate $tax_rate)
    {
        $tax_rate->delete();

        return back()->with('success', 'Tax rate deleted.');
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'country' => ['nullable', 'string', 'size:2'],
            'state' => ['nullable', 'string', 'max:80'],
            'tax_class' => ['required', 'string', 'max:40'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
            'priority' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['priority'] = $data['priority'] ?? 0;
        $data['country'] = $data['country'] ? strtoupper($data['country']) : null;

        return $data;
    }
}
