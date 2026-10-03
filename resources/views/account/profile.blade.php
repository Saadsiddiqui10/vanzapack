<x-account-layout title="Profile & Password">
    <div class="space-y-6">
        <form method="POST" action="{{ route('account.profile.update') }}" class="card p-5">
            @csrf @method('PUT')
            <h2 class="mb-4 font-semibold text-brand-800">Profile details</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="label">Name</label><input name="name" value="{{ old('name', auth()->user()->name) }}" class="field"><x-input-error :messages="$errors->get('name')" class="mt-1" /></div>
                <div><label class="label">Email</label><input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" class="field"><x-input-error :messages="$errors->get('email')" class="mt-1" /></div>
                <div><label class="label">Phone</label><input name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="field"></div>
            </div>
            <button class="btn-primary mt-4">Save changes</button>
        </form>

        <form method="POST" action="{{ route('account.password.update') }}" class="card p-5">
            @csrf @method('PUT')
            <h2 class="mb-4 font-semibold text-brand-800">Change password</h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <div><label class="label">Current password</label><input name="current_password" type="password" class="field"><x-input-error :messages="$errors->get('current_password')" class="mt-1" /></div>
                <div><label class="label">New password</label><input name="password" type="password" class="field"><x-input-error :messages="$errors->get('password')" class="mt-1" /></div>
                <div><label class="label">Confirm password</label><input name="password_confirmation" type="password" class="field"></div>
            </div>
            <button class="btn-primary mt-4">Update password</button>
        </form>
    </div>
</x-account-layout>
