<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Auth;

use Illuminate\Contracts\Auth\StatefulGuard;
use MinVWS\DUSi\Application\API\Models\PortalUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Session\Session;

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

    public function attempt(array $credentials = [], $remember = false): void
    {
        // TODO: Implement attempt() method.
    }

    public function once(array $credentials = []): void
    {
        // TODO: Implement once() method.
    }

    public function login(Authenticatable $user, $remember = false): void
    {
        // TODO: Implement login() method.
    }

    public function loginUsingId($id, $remember = false): void
    {
        // TODO: Implement loginUsingId() method.
    }

    public function onceUsingId($id): void
    {
        // TODO: Implement onceUsingId() method.
    }

    public function viaRemember(): void
    {
        // TODO: Implement viaRemember() method.
    }
}
