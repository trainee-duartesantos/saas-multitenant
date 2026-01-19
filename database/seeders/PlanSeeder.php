<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run()
    {
        Plan::updateOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'price' => 0,
                'max_members' => 1,
                'max_projects' => 1,
                'billing_access' => false,
                'advanced_permissions' => false,
                'has_priority_support' => false,
                'stripe_price_id' => null,
            ]
        );

        Plan::updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'price' => 1500,
                'max_members' => 5,
                'max_projects' => 10,
                'billing_access' => true,
                'advanced_permissions' => true,
                'has_priority_support' => false,
                'stripe_price_id' => 'price_1SrKeP6sG72TAVnWpsgiTcCx',
            ]
        );


        Plan::updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 5000,
                'max_members' => null,
                'max_projects' => null,
                'billing_access' => true,
                'advanced_permissions' => true,
                'has_priority_support' => true,
                'stripe_price_id' => 'price_1SrKgu6sG72TAVnW28JBmIDI',
            ]
        );
    }
}
