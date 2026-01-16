<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Enums\TenantRole;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $freePlan = Plan::where('slug', 'free')->first();

        $tenant = Tenant::create([
            'name' => $user->name . "'s Workspace",
            'slug' => Str::slug($user->name) . '-' . Str::random(5),
            'settings' => [],
            'plan_id' => $freePlan->id,
            'trial_ends_at' => now()->addDays(14),
        ]);


        $user->tenants()->attach($tenant->id, [
            'role' => TenantRole::OWNER->value,
        ]);

        event(new Registered($user));

        Auth::login($user);
        session(['tenant_id' => $tenant->id]);

        return redirect(route('dashboard', absolute: false));
    }
}
