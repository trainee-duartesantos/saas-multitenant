<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run()
    {
        Plan::create([
            'name' => 'Free',
            'slug' => 'free',
            'price' => 0,
            'max_members' => 1,
            'max_projects' => 1,
        ]);

        Plan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'price' => 1500,
            'max_members' => 5,
            'max_projects' => 10,
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'price' => 5000,
            'max_members' => null,
            'max_projects' => null,
            'has_priority_support' => true,
        ]);
    }
}
