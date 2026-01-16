<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantSwitchController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TenantInvitationController;
use App\Http\Controllers\TenantInvitationAcceptController;
use App\Http\Controllers\TenantMemberController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified', 'tenant'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('projects', ProjectController::class);

    Route::post('/tenant/switch', TenantSwitchController::class)
        ->name('tenant.switch');

    Route::post('/tenant/invitations', [TenantInvitationController::class, 'store'])
        ->name('tenant.invitations.store');

    Route::get('/members', [TenantMemberController::class, 'index'])
        ->name('tenant.members.index');

    Route::patch('/members/{user}', [TenantMemberController::class, 'update'])
        ->name('tenant.members.update');

    Route::delete('/members/{user}', [TenantMemberController::class, 'destroy'])
        ->name('tenant.members.destroy');

    Route::delete('/tenant/invitations/{invitation}', [TenantInvitationController::class, 'destroy'])
        ->name('tenant.invitations.destroy');

    Route::post('/tenant/invitations/{invitation}/resend', [TenantInvitationController::class, 'resend'])
        ->name('tenant.invitations.resend');

    Route::post('/tenant/members/{user}/transfer-ownership', [TenantMemberController::class, 'transferOwnership'])
        ->name('tenant.members.transferOwnership');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Página pública: mostrar convite
Route::get('/invitations/{token}', [TenantInvitationAcceptController::class, 'show'])
    ->name('tenant.invitations.show');

// Ação: aceitar convite
Route::post('/invitations/{token}/accept', [TenantInvitationAcceptController::class, 'accept'])
    ->name('tenant.invitations.accept');

require __DIR__.'/auth.php';
