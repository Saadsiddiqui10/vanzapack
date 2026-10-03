<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        return view('account.addresses', [
            'addresses' => $request->user()->addresses()->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $address = $request->user()->addresses()->create($this->validated($request));
        $this->syncDefaults($request, $address);

        return back()->with('success', 'Address added.');
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeAddress($request, $address);
        $address->update($this->validated($request));
        $this->syncDefaults($request, $address);

        return back()->with('success', 'Address updated.');
    }

    public function destroy(Request $request, Address $address)
    {
        $this->authorizeAddress($request, $address);
        $address->delete();

        return back()->with('success', 'Address removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:40'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'phone' => ['required', 'string', 'max:40'],
            'line1' => ['required', 'string', 'max:150'],
            'line2' => ['nullable', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:80'],
            'state' => ['nullable', 'string', 'max:80'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'size:2'],
            'is_default_shipping' => ['boolean'],
            'is_default_billing' => ['boolean'],
        ]);
    }

    private function syncDefaults(Request $request, Address $address): void
    {
        if ($request->boolean('is_default_shipping')) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default_shipping' => false]);
        }
        if ($request->boolean('is_default_billing')) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default_billing' => false]);
        }
    }

    private function authorizeAddress(Request $request, Address $address): void
    {
        abort_unless($address->user_id === $request->user()->id, 403);
    }
}
