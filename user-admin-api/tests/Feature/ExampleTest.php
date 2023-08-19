<?php

declare(strict_types=1);

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testThatTheApplicationRedirectsToLoginScreen(): void
    {
        $response = $this->get('/');

        $response
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }
}
