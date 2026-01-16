<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'is_active',
        'max_members',
        'max_projects',
        'has_priority_support',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}