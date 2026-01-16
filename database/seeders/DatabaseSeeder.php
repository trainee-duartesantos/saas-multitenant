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
        $user = User::firstOrCreate(
            ['email' => 'admin@tenant.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
            ]
        );

        $tenant = Tenant::firstOrCreate(
            ['slug' => 'tenant-alpha'],
            ['name' => 'Tenant Alpha']
        );

        // Attach apenas se ainda não estiver ligado
        if (! $user->tenants()->where('tenants.id', $tenant->id)->exists()) {
            $user->tenants()->attach($tenant->id, ['role' => 'owner']);
        }
    }
}
