<x-account-layout title="Addresses">
    <div x-data="{ editing: null }">
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach($addresses as $address)
                <div class="card p-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-brand-800">{{ $address->label }}</span>
                        <div class="flex gap-1">
                            @if($address->is_default_shipping)<span class="badge bg-brand-50 text-brand-700">Default</span>@endif
                        </div>
                    </div>
                    <p class="mt-1 text-slate-500">
                        {{ $address->fullName() }}<br>
                        {{ $address->line1 }}{{ $address->line2 ? ', '.$address->line2 : '' }}<br>
                        {{ $address->city }}{{ $address->state ? ', '.$address->state : '' }}, {{ $address->country }}<br>
                        {{ $address->phone }}
                    </p>
                    <div class="mt-3 flex gap-3">
                        <button class="text-xs text-brand-800 hover:underline"
                                @click="editing = editing === {{ $address->id }} ? null : {{ $address->id }}">Edit</button>
                        <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" onsubmit="return confirm('Delete this address?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-rose-500 hover:underline">Delete</button>
                        </form>
                    </div>

                    <div x-show="editing === {{ $address->id }}" x-cloak class="mt-3 border-t border-slate-100 pt-3">
                        @include('account.partials.address-form', ['address' => $address, 'action' => route('account.addresses.update', $address), 'method' => 'PUT'])
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card mt-6 p-5">
            <h2 class="mb-4 font-semibold text-brand-800">Add a new address</h2>
            @include('account.partials.address-form', ['address' => null, 'action' => route('account.addresses.store'), 'method' => 'POST'])
        </div>
    </div>
</x-account-layout>
