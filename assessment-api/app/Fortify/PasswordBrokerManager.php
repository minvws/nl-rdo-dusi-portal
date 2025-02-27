<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Fortify;

use Illuminate\Auth\Passwords\PasswordBrokerManager as LaravelPasswordBrokerManager;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Hashing\Hasher;
use MinVWS\DUSi\Assessment\API\Fortify\Providers\PasswordResetTokenRepository;

class PasswordBrokerManager extends LaravelPasswordBrokerManager
{
    /**
     * Create a token repository instance based on the given configuration.
     * Overrides function to use our PasswordResetTokenRepository
     *
     * @param array{expire: int, throttle?: int} $config
     * @return TokenRepositoryInterface
     * @throws BindingResolutionException
     */
    protected function createTokenRepository(array $config): TokenRepositoryInterface
    {
        $key = $this->app->make(Repository::class)->get('app.key');

        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        // Use our repository
        return new PasswordResetTokenRepository(
            hasher: $this->app->make(Hasher::class),
            hashKey: $key,
            expires: $config['expire'],
            throttle: $config['throttle'] ?? 0
        );
    }
}
