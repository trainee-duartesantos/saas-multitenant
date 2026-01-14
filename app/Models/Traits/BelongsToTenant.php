<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenantId = session('tenant_id')) {
                $builder->where(
                    $builder->getModel()->getTable() . '.tenant_id',
                    $tenantId
                );
            }
        });

        static::creating(function (Model $model) {
            if (!$model->tenant_id && session('tenant_id')) {
                $model->tenant_id = session('tenant_id');
            }
        });
    }
}
