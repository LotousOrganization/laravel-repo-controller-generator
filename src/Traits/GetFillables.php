<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait GetFillables
{
    public function scopeGetFillables(Builder $query)
    {
        return $this->fillable;
    }
}