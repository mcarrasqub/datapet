<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Desabilitar middleware CSRF para evitar errores 419 en tests
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }
}
