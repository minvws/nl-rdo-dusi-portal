<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Auth;

use Illuminate\Contracts\Auth\StatefulGuard;
use MinVWS\DUSi\Application\API\Models\PortalUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Session\Session;

/**
 * @SuppressWarnings(PHPMD)
 */
class PortalUserGuard implements StatefulGuard
{
    protected const SESSION_KEY = 'portal_user';

    public function __construct(
        protected Session $session
    ) {
    }

    public function check(): bool
    {
        return $this->session->has(self::SESSION_KEY);
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function user(): PortalUser | null
    {
        if (!$this->check()) {
            return null;
        }

        return $this->session->get(self::SESSION_KEY);
    }

    /**
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function id(): int|string|null
    {
        return $this->user()?->bsn ?? null;
    }

    /**
     * @param array $credentials
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validate(array $credentials = [])
    {
        throw new \RuntimeException('Not implemented UziAuthGuard::validate() method');
    }

    public function hasUser()
    {
        throw new \RuntimeException('Not implemented UziAuthGuard::hasUser() method');
    }

    public function setUser(Authenticatable $user): static
    {
        $this->session->put(self::SESSION_KEY, $user);
        $this->session->migrate(true);
        return $this;
    }

    /**
     * Logs out the current user.
     *
     * @return void
     */
    public function logout(): void
    {
        $this->session->remove(self::SESSION_KEY);
        $this->session->migrate(true);
    }

    public function attempt(array $credentials = [], $remember = false): bool
    {
        return true;
    }

    public function once(array $credentials = []): bool
    {
            return true;
    }

    public function login(Authenticatable $user, $remember = false): bool
    {
            return true;
    }

    public function loginUsingId($id, $remember = false): bool
    {
            return true;
    }

    public function onceUsingId($id): bool
    {
            return true;
    }

    public function viaRemember(): bool
    {
            return true;
    }

}
