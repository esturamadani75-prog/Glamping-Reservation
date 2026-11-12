<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_landing_page_bisa_dibuka(): void
    {
        $this->get('/')->assertOk()->assertSee('Merapi Glamping');
    }
}
