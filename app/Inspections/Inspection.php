<?php

namespace App\Inspections;

interface Inspection
{
    /**
     * @throws \Exception
     * @param string $body
     * @return bool
     */
    public function detect(string $body): bool;
}