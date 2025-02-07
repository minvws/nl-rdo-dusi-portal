<?php // @phpcs:disable SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\SurePay;

use Carbon\CarbonImmutable;

readonly class SurePayAccessToken
{
    protected string $accessToken;
    protected CarbonImmutable $issuedAt;
    protected CarbonImmutable $expiresAt;

    public function __construct(string $accessToken, CarbonImmutable $issuedAt, CarbonImmutable $expiresAt)
    {
        $this->accessToken = $accessToken;
        $this->issuedAt = $issuedAt;
        $this->expiresAt = $expiresAt;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getIssuedAt(): CarbonImmutable
    {
        return $this->issuedAt;
    }

    public function getExpiresAt(): CarbonImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->isPast();
    }
}
