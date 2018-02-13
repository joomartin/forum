<?php

namespace App\Inspections;

use Exception;

class InvalidKeywords implements Inspection
{
    protected $keywords = [
        'yahoo customer support'
    ];

    public function detect(string $body): bool
    {
        $containsInvalid = collect($this->keywords)
            ->contains(function ($keyword) use ($body) {
                return stripos($body, $keyword) !== false;
            });

        if ($containsInvalid) {
            throw new Exception('Your reply contains spam');
        }

        return false;
    }
}