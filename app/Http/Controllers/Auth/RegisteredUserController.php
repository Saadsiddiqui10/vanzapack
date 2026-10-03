<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wishlist;
use App\Notifications\WelcomeNotification;
use App\Services\CartService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request, CartService $cart): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole(UserRole::Customer);
        Wishlist::firstOrCreate(['user_id' => $user->id]);

        event(new Registered($user));
        rescue(fn () => $user->notify(new WelcomeNotification), report: false);

        Auth::login($user);
        $cart->mergeGuestCartInto($user);

        return redirect(route('account.dashboard', absolute: false));
    }
}
