<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantSwitchController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TenantInvitationController;
use App\Http\Controllers\TenantInvitationAcceptController;
use App\Http\Controllers\TenantMemberController;
use App\Http\Controllers\TenantOnboardingController;
use App\Http\Controllers\InvitationsController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\StripeWebhookController;
use Laravel\Cashier\Http\Controllers\WebhookController;
use App\Http\Controllers\BillingHistoryController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

Route::post('/stripe/webhook', function (\Illuminate\Http\Request $request) {
    // 1️⃣ Cashier trata subscriptions, invoices, status
    app(WebhookController::class)->handleWebhook($request);

    // 2️⃣ O teu controller trata plano, logs, downgrade, etc
    return app(StripeWebhookController::class)
        ->handle($request);
})->withoutMiddleware(['web']);


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/invitations', [InvitationsController::class, 'index'])
        ->name('invitations.index');
});

// Página pública: mostrar convite
Route::get('/invitations/{token}', [TenantInvitationAcceptController::class, 'show'])
    ->name('tenant.invitations.show');

// Ação: aceitar convite
Route::post('/invitations/{token}/accept', [TenantInvitationAcceptController::class, 'accept'])
    ->name('tenant.invitations.accept');

Route::middleware(['auth', 'verified', 'tenant'])->group(function () {

    Route::get('/onboarding', [TenantOnboardingController::class, 'index'])
        ->name('onboarding.index');

    Route::post('/onboarding/tenant', [TenantOnboardingController::class, 'storeTenant'])
        ->name('onboarding.tenant.store');

    Route::post('/onboarding/complete', [TenantOnboardingController::class, 'complete'])
        ->name('onboarding.complete');

    Route::get('/pricing', [PricingController::class, 'index'])
        ->name('pricing.index');

    Route::post('/pricing/upgrade/{plan:slug}', [PricingController::class, 'upgrade'])
        ->name('pricing.upgrade');
});

Route::middleware(['auth', 'verified', 'tenant', 'tenant.onboarded'])->group(function () {

    Route::get('/dashboard', function (Request $request) {
        $tenant = $request->attributes->get('tenant');

        $latestProjects = $tenant->projects()
        ->latest()
        ->limit(5)
        ->get(['id', 'name', 'created_at']);
        
        return Inertia::render('Dashboard', [
            'plan' => [
                'name' => $tenant->plan?->name,
                'slug' => $tenant->plan?->slug,
                'price' => $tenant->plan?->price,
            ],
            'trial' => [
                'active' => $tenant->onTrial(),
                'ends_at' => $tenant->trial_ends_at,
            ],
            'latestProjects' => $latestProjects,
            'membersPreview' => $tenant->users()
                ->select('users.id', 'users.name')
                ->limit(4)
                ->get(),
            'membersTotal' => $tenant->users()->count(),
            'onboardingChecklist' => $tenant->onboardingChecklist(),
        ]);
    })->name('dashboard');

    Route::resource('projects', ProjectController::class);

    Route::get('/members', [TenantMemberController::class, 'index'])
        ->name('tenant.members.index');

    Route::patch('/members/{user}', [TenantMemberController::class, 'update'])
        ->name('tenant.members.update');

    Route::delete('/members/{user}', [TenantMemberController::class, 'destroy'])
        ->name('tenant.members.destroy');

    Route::post('/tenant/members/{user}/transfer-ownership', [TenantMemberController::class, 'transferOwnership'])
        ->name('tenant.members.transferOwnership');

    Route::post('/tenant/invitations', [TenantInvitationController::class, 'store'])
        ->name('tenant.invitations.store');

    Route::delete('/tenant/invitations/{invitation}', [TenantInvitationController::class, 'destroy'])
        ->name('tenant.invitations.destroy');

    Route::post('/tenant/invitations/{invitation}/resend', [TenantInvitationController::class, 'resend'])
        ->name('tenant.invitations.resend');

    Route::post('/tenant/switch', TenantSwitchController::class)
        ->name('tenant.switch');

    /*
    | 💳 BILLING (feature-based)
    */
    Route::get('/billing', function () {
        return redirect()->route('pricing.index');
    })->name('billing');

    Route::post('/billing/checkout/{plan}', [BillingController::class, 'checkout'])
        ->name('billing.checkout');

    Route::get('/billing/success', [BillingController::class, 'success'])
        ->name('billing.success');

    Route::get('/billing/history', [BillingHistoryController::class, 'index'])
        ->name('billing.history');

    Route::post('/billing/downgrade/{plan}', [BillingController::class, 'downgrade'])
        ->name('billing.downgrade');

    Route::post('/billing/cancel', [BillingController::class, 'cancel'])
        ->name('billing.cancel');

    Route::post('/billing/downgrade/cancel', [BillingController::class, 'cancelDowngrade'])
        ->name('billing.downgrade.cancel');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
