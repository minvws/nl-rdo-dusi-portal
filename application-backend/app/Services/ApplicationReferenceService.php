<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;

readonly class ApplicationReferenceService
{
    private const MAX_TRIES = 3;

    public function __construct(
        private ApplicationRepository $applicationRepository,
    )
    {
    }

    public function generateUniqueReference(Subsidy $subsidy): string
    {
        $referencePrefix = $subsidy->reference_prefix;

        return $this->createApplicationReferenceString($referencePrefix, $this->makeUniqueNumberByElevenRule($referencePrefix));
    }

    private function makeUniqueNumberByElevenRule(string $referencePrefix): int
    {
        for ($i = 0; $i < self::MAX_TRIES; $i++) {
            $randomNumber = ApplicationReferenceGenerator::generateRandomNumberByElevenRule();

            if ($this->applicationRepository->isReferenceUnique($this->createApplicationReferenceString($referencePrefix, $randomNumber))) {
                return $randomNumber;
            }
        }

        throw new ApplicationReferenceException(sprintf('Could not make unique reference after %d tries.', $i));
    }

    private function createApplicationReferenceString(string $referencePrefix, int $randomNumber): string
    {
        return sprintf('%s-%s', $referencePrefix, $randomNumber);
    }
}
