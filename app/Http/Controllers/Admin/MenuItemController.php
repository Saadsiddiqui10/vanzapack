<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuItemController extends Controller
{
    public function index()
    {
        return view('admin.menu.index', [
            'items' => MenuItem::orderBy('position')->orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.menu.form', ['item' => new MenuItem(['type' => 'link', 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        MenuItem::create($this->data($request));

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item added.');
    }

    public function edit(MenuItem $menuItem)
    {
        return view('admin.menu.form', ['item' => $menuItem]);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $menuItem->update($this->data($request));

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return back()->with('success', 'Menu item removed.');
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:60'],
            'url' => ['nullable', 'string', 'max:250'],
            'type' => ['required', Rule::in(['link', 'mega'])],
            'is_active' => ['boolean'],
            'open_in_new_tab' => ['boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['open_in_new_tab'] = $request->boolean('open_in_new_tab');
        $data['position'] = $data['position'] ?? 0;
        if ($data['type'] === 'mega') {
            $data['url'] = null;
        }

        return $data;
    }
}
