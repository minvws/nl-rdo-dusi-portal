<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodableSupport;
use MinVWS\Codable\Reflection\Attributes\CodableName;
use MinVWS\DUSi\Application\API\Services\Oidc\OidcUserLoa;
use RuntimeException;

class PortalUser implements Authenticatable, Decodable
{
    use DecodableSupport;

    /**
     * @param string $bsn
     * @param string $id
     * @param OidcUserLoa $loaAuthn
     */
    public function __construct(
        public string $bsn,
        #[CodableName('bsn')] public string $id,
        #[CodableName('loa_authn')] public OidcUserLoa $loaAuthn,
    ) {
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
        throw new RuntimeException("Portal user can't have a password");
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
