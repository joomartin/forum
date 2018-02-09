<?php

namespace App;

class Favorite extends Model
{
    use RecordsActivity;

    public function favorited()
    {
        return $this->morphTo('favorited');
    }
}
