<?php

namespace App\Inspections;

use Exception;

class KeyHeldDown implements Inspection
{
    public function detect(string $body): bool
    {
        if (preg_match('/(.)\\1{4,}/', $body, $matches)) {
            throw new Exception('Your reply contains spam');
        }

        return false;
    }
}