<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_guests_are_redirected_from_home(): void
    {
        $response = $this->get('/');

        // Guest: '/' redirige verso setup (se non seedato) o verso il login
        $response->assertRedirect();
    }
}