<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Illuminate\Database\Eloquent\Collection;
use League\CommonMark\Exception\LogicException;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\BankAccountSubsidyStageHashNotFoundException;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationHashService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use Webmozart\Assert\Assert;

class BankAccountDuplicatesService
{
    public const BANK_ACCOUNT_SUBSIDY_STAGE_HASH_NAME = 'Bank account';

    public function __construct(private readonly ApplicationHashService $applicationHashService)
    {
    }

    /**
     * @return array|Collection
     */
    public function getDuplicatesForSubsidy(Subsidy $subsidy): Collection
    {
        $subsidyStageHash = $this->getBankAccountSubsidyStageHash($subsidy);

        $query = $this->applicationHashService->getApplicationHashDuplicatesQuery($subsidyStageHash);

        return $query->join('applications', 'applications.id', 'application_hashes.application_id')
                ->whereNotIn('applications.status', [ApplicationStatus::Draft, ApplicationStatus::Rejected])
                ->get();
    }

    public function getBankAccountSubsidyStageHash(Subsidy $subsidy): SubsidyStageHash
    {
        $subsidyStageHashQuery = $this->applicationHashService->getSubsidyStageHashJoinedQuery();

        $subsidyStageHashQuery
            ->where('subsidy_stage_hashes.name', self::BANK_ACCOUNT_SUBSIDY_STAGE_HASH_NAME);

        $count = $subsidyStageHashQuery->count();
        $subsidyStageHash = match (true) {
            $count === 0 => throw new BankAccountSubsidyStageHashNotFoundException(
                sprintf(
                    'No \'%s\' SubsidyStageHash found for subsidy (%s)',
                    self::BANK_ACCOUNT_SUBSIDY_STAGE_HASH_NAME,
                    $subsidy->id
                )
            ),
            $count === 1 => $subsidyStageHashQuery->firstOrFail(),
            $count > 1 => throw new LogicException(
                sprintf(
                    'Multiple SubsidyStageHashes with name \'%s\' found for subsidy (%s)',
                    self::BANK_ACCOUNT_SUBSIDY_STAGE_HASH_NAME,
                    $subsidy->id
                )
            ),
        };
        Assert::isInstanceOf($subsidyStageHash, SubsidyStageHash::class);

        return $subsidyStageHash;
    }
}
