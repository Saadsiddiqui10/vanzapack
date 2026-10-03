<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function index()
    {
        return view('admin.attributes.index', [
            'attributes' => Attribute::withCount('values')->orderBy('position')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.attributes.form', ['attribute' => new Attribute(['type' => 'select', 'is_variation' => true])]);
    }

    public function store(Request $request)
    {
        $attribute = Attribute::create($this->data($request));
        $this->syncValues($attribute, $request);

        return redirect()->route('admin.attributes.edit', $attribute)->with('success', 'Attribute created.');
    }

    public function edit(Attribute $attribute)
    {
        $attribute->load('values');

        return view('admin.attributes.form', compact('attribute'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $attribute->update($this->data($request));
        $this->syncValues($attribute, $request);

        return back()->with('success', 'Attribute updated.');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute deleted.');
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:select,color,text'],
            'is_variation' => ['boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_variation'] = $request->boolean('is_variation');
        $data['slug'] = Str::slug($data['name']);
        $data['position'] = $data['position'] ?? 0;

        return $data;
    }

    private function syncValues(Attribute $attribute, Request $request): void
    {
        $lines = collect(preg_split('/\r?\n/', (string) $request->input('values_text')))
            ->map(fn ($l) => trim($l))
            ->filter();

        $keep = [];
        foreach ($lines as $i => $line) {
            [$value, $hex] = array_pad(array_map('trim', explode('|', $line, 2)), 2, null);
            $model = $attribute->values()->updateOrCreate(
                ['slug' => Str::slug($value)],
                ['value' => $value, 'color_hex' => $hex, 'position' => $i],
            );
            $keep[] = $model->id;
        }

        $attribute->values()->whereNotIn('id', $keep)->delete();
    }
}
