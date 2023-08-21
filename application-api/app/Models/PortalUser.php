<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Models;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\Authenticatable;
use RuntimeException;

class PortalUser implements Authenticatable
{
    /**
     * @param string      $bsn
     * @param string      $id
     * @param string|null $loaAuthn
     */
    public function __construct(
        public string $bsn,
        public string $id,
        public string|null $loaAuthn,
    ) {
    }

    /**
     * @param object $oidcResponse
     * @return PortalUser|null
     */
    public static function deserializeFromObject(object $oidcResponse): ?PortalUser
    {
        $requiredKeys = ["bsn"];
        $missingKeys = [];
        foreach ($requiredKeys as $key) {
            if (!property_exists($oidcResponse, $key)) {
                $missingKeys[] = $key;
            }
        }
        if (count($missingKeys) > 0) {
            return null;
        }

        try {
            return new PortalUser(
                $oidcResponse->bsn, // @phpstan-ignore-line
                $oidcResponse->bsn, // @phpstan-ignore-line
                $oidcResponse->loa_authn ?? null
            );
        } catch (Exception $e) {
            report($e);
            Log::error("Trying to build an PortalUser from userinfo failed", [$e]);
            return null;
        }
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return $this->bsn;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifier(): string
    {
        return $this->bsn;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        throw new RuntimeException("Portal uses can't have a password");
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken(): string
    {
        throw new RuntimeException("Do not remember cookie's");
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setRememberToken($value): void
    {
        throw new RuntimeException("Do not remember cookie's");
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(): string
    {
        throw new RuntimeException("Do not remember cookie's");
    }
}
