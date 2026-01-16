<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Enums\TenantRole;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PlanSeeder::class);

        $user = User::firstOrCreate(
            ['email' => 'admin@tenant.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
            ]
        );

        $freePlan = Plan::where('slug', 'free')->first();

        // 4️⃣ Criar tenant já com plano
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'tenant-alpha'],
            [
                'name' => 'Tenant Alpha',
                'plan_id' => $freePlan->id,
                'trial_ends_at' => now()->addDays(14),
            ]
        );

        // Attach apenas se ainda não estiver ligado
        if (! $user->tenants()->where('tenants.id', $tenant->id)->exists()) {
            $user->tenants()->attach($tenant->id, ['role' => 'owner']);
        }
        
    }
}
