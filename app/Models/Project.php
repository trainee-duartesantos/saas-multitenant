<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    protected $fillable = [
        'name',
    ];

    protected static function booted()
    {
        // 🔒 ISOLAMENTO POR TENANT (GLOBAL SCOPE)
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (session()->has('tenant_id')) {
                $builder->where('tenant_id', session('tenant_id'));
            }
        });

        // ✍️ AUTO-ASSIGN DO TENANT AO CRIAR
        static::creating(function ($model) {
            if (session()->has('tenant_id')) {
                $model->tenant_id = session('tenant_id');
            }
        });
    }
}
