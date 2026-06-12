<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasTenant
{
    /**
     * Boot the trait to apply a global scope based on the authenticated user's tenant_id.
     */
    public static function bootHasTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where(
                    (new static)->getTable() . '.tenant_id',
                    auth()->user()->tenant_id
                );
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && !$model->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}
