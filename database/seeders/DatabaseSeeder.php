<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use App\Enums\TenantRole;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@tenant.com',
            'password' => Hash::make('password'),
        ]);

        $tenantA = Tenant::create([
            'uuid' => Str::uuid(),
            'name' => 'Tenant Alpha',
            'slug' => 'tenant-alpha',
            'settings' => [],
        ]);

        $tenantB = Tenant::create([
            'uuid' => Str::uuid(),
            'name' => 'Tenant Beta',
            'slug' => 'tenant-beta',
            'settings' => [],
        ]);

        // 👇 LIGAÇÃO USER ↔ TENANT COM ROLE (FORMA CORRETA)
        $tenantA->users()->attach($user->id, [
            'role' => TenantRole::OWNER->value,
        ]);

        $tenantB->users()->attach($user->id, [
            'role' => TenantRole::OWNER->value,
        ]);
    }
}
