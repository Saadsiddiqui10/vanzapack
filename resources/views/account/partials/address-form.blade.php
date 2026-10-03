<form method="POST" action="{{ $action }}" class="grid gap-3 text-sm sm:grid-cols-2">
    @csrf
    @if($method !== 'POST')@method($method)@endif
    <div><label class="label">Label</label><input name="label" value="{{ old('label', $address?->label ?? 'Home') }}" class="field"></div>
    <div></div>
    <div><label class="label">First name</label><input name="first_name" value="{{ old('first_name', $address?->first_name) }}" required class="field"></div>
    <div><label class="label">Last name</label><input name="last_name" value="{{ old('last_name', $address?->last_name) }}" required class="field"></div>
    <div><label class="label">Phone</label><input name="phone" value="{{ old('phone', $address?->phone) }}" required class="field"></div>
    <div><label class="label">Country</label>
        <select name="country" class="field">
            @foreach(['AE' => 'United Arab Emirates', 'SA' => 'Saudi Arabia', 'QA' => 'Qatar', 'OM' => 'Oman', 'KW' => 'Kuwait', 'BH' => 'Bahrain'] as $code => $name)
                <option value="{{ $code }}" @selected(old('country', $address?->country ?? 'AE') === $code)>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2"><label class="label">Address line 1</label><input name="line1" value="{{ old('line1', $address?->line1) }}" required class="field"></div>
    <div class="sm:col-span-2"><label class="label">Address line 2</label><input name="line2" value="{{ old('line2', $address?->line2) }}" class="field"></div>
    <div><label class="label">City</label><input name="city" value="{{ old('city', $address?->city) }}" required class="field"></div>
    <div><label class="label">State / Emirate</label><input name="state" value="{{ old('state', $address?->state) }}" class="field"></div>
    <div><label class="label">Postal code</label><input name="postal_code" value="{{ old('postal_code', $address?->postal_code) }}" class="field"></div>
    <div class="flex items-end gap-4">
        <label class="flex items-center gap-2"><input type="checkbox" name="is_default_shipping" value="1" @checked($address?->is_default_shipping) class="rounded border-slate-300 text-brand-500"> Default shipping</label>
    </div>
    <div class="sm:col-span-2"><button class="btn-primary">{{ $address ? 'Update address' : 'Add address' }}</button></div>
</form>
