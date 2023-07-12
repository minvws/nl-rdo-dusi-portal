<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_redirects_to_login_screen(): void
    {
        $response = $this->get('/');

        $response
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }
}
