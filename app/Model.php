<?php

namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
    protected $guarded = [];

    public function path($uri = ''): string
    {
        $url = "/{$this->getTable()}/{$this->{$this->getRouteKeyName()}}";
        return $uri ? "{$url}/{$uri}" : $url;
    }
}
