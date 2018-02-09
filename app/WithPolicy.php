<?php

namespace App;

use Illuminate\Support\Facades\Gate;

trait WithPolicy
{
    public function getCanAttribute()
    {
        $methods = collect(get_class_methods(Gate::getPolicyFor($this)));

        return $methods->mapWithKeys(function ($m) {
            return [$m => Gate::allows($m, $this)];
        });
    }
}