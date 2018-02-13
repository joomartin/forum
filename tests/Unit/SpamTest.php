<?php

namespace Tests\Feature;

use App\Inspections\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
    /** @test */
    public function it_detects_invalid_keywords()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here'));

        $this->expectException(\Exception::class);
        $spam->detect('yahoo customer support');
    }

    /** @test */
    public function it_detects_any_key_held_down()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Voodoo people'));

        $this->expectException(\Exception::class);
        $spam->detect('Hello world aaaaaaa');
    }
}