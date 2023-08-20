<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use MinVWS\DUSi\User\Admin\API\Tests\TestCase;

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
