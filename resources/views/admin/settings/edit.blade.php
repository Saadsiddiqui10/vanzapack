<x-admin-layout title="Settings" active="settings">
    <x-admin.head title="Store settings" />

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        @foreach($groups as $g)
            <a href="{{ route('admin.settings.edit', $g) }}"
               class="rounded-lg px-3 py-1.5 capitalize {{ $group === $g ? 'bg-brand-700 text-white' : 'bg-white border border-slate-200' }}">{{ $g }}</a>
        @endforeach
    </div>

    <form method="POST" action="{{ route('admin.settings.update', $group) }}" class="max-w-2xl space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf @method('PUT')
        @foreach($settings as $setting)
            @php $label = \Illuminate\Support\Str::headline($setting->key); @endphp
            @if($setting->type === 'bool')
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="bools[]" value="{{ $setting->key }}" @checked($setting->castedValue()) class="rounded border-slate-300 text-brand-500">
                    {{ $label }}
                </label>
            @else
                <div>
                    <label class="label">{{ $label }}</label>
                    @if(\Illuminate\Support\Str::contains($setting->key, ['description', 'message', 'address', 'bank_details', 'announcement']))
                        <textarea name="settings[{{ $setting->key }}]" rows="3" class="field">{{ $setting->value }}</textarea>
                    @else
                        <input name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="field">
                    @endif
                </div>
            @endif
        @endforeach
        <button class="btn-primary">Save {{ $group }} settings</button>
    </form>
</x-admin-layout>
