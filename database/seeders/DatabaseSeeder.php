<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
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

        $tenant = Tenant::create([
            'uuid' => Str::uuid(),
            'name' => 'Tenant Demo',
            'slug' => 'tenant-demo',
            'settings' => [],
        ]);

        $tenant->users()->attach($user->id, [
            'role' => 'owner',
        ]);
    }
}
