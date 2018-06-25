<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Inspections\Spam;

class SpamTest extends TestCase
{
    /** @test */
    function it_checks_for_invalid_keywords()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here'));

        $this->expectException(\Exception::class);

        $spam->detect('yahoo customer support');
    }

    /** @test */
    function it_checks_for_any_key_hold_down()
    {
        $spam = new Spam();

        $this->expectException(\Exception::class);

        $spam->detect('Hello world aaaaaaaaaaaaaa');
    }
}