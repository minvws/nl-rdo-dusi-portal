<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Application\Backend\Services\Exceptions\ApplicationReferenceException;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationReferenceRepository;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;

class ApplicationReferenceService
{
    private const MAX_TRIES = 3;

    public function __construct(
        private readonly ApplicationReferenceRepository $applicationReferenceRepository,
        private readonly ApplicationReferenceGenerator $applicationReferenceGenerator,
    ) {
    }

    public function generateUniqueReferenceByElevenRule(Subsidy $subsidy): string
    {
        for ($i = 0; $i < self::MAX_TRIES; $i++) {
            $randomNumber = $this->applicationReferenceGenerator->generateRandomNumberByElevenRule();
            $applicationReference = $this->createApplicationReferenceString($subsidy->reference_prefix, $randomNumber);

            if ($this->applicationReferenceRepository->isReferenceUnique($applicationReference)) {
                return $applicationReference;
            }
        }

        throw new ApplicationReferenceException(sprintf('Could not make unique reference after %d tries.', $i));
    }

    private function createApplicationReferenceString(string $referencePrefix, int $randomNumber): string
    {
        return sprintf('%s-%08d', $referencePrefix, $randomNumber);
    }
}
