<?php


namespace App\Helpers;

use Illuminate\Support\Str;

trait Uuid
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::uuid();
        });
    }
}
