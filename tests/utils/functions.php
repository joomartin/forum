<?php

function create(string $class, array $attributes = [], int $count = null)
{
    return factory("App\\{$class}", $count)->create($attributes);
}

function make(string $class, array $attributes = [], int $count = null)
{
    return factory("App\\{$class}", $count)->make($attributes);
}