<?php

namespace App\Inspections;

class Spam
{
    protected $inspections = [
        InvalidKeywords::class,
        KeyHeldDown::class
    ];

    public function detect(string $body)
    {
        foreach ($this->inspections as $inspection) {
            app($inspection)->detect($body);
        }

        return false;
    }

    protected function detectKeyHeldDown($body)
    {
        if (preg_match('/(.)\\1{4,}/', $body, $matches)) {
            throw new \Exception('Your reply contains spam');
        }
    }
}