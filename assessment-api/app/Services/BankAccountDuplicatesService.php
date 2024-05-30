<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Illuminate\Support\Collection;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\BankAccountSubsidyStageHashNotFoundException;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationHash as ApplicationHashDTO;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationHashService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;

class BankAccountDuplicatesService
{
    public const BANK_ACCOUNT_SUBSIDY_STAGE_HASH_NAME = 'Bank account';

    public function __construct(private readonly ApplicationHashService $applicationHashService)
    {
    }

    /**
     * @return Collection<int, ApplicationHashDTO>
     * @throws BankAccountSubsidyStageHashNotFoundException
     */
    public function getDuplicatesForSubsidy(Subsidy $subsidy): Collection
    {
        $subsidyStageHashes = $this->getBankAccountSubsidyStageHash($subsidy);

        return  $subsidyStageHashes->flatMap(function (SubsidyStageHash $subsidyStageHash) {
            $query = $this->applicationHashService->getApplicationHashDuplicatesQuery($subsidyStageHash);
            /**
             * @psalm-suppress InvalidTemplateParam
             */
            return $query
                ->join('applications', 'applications.id', 'application_hashes.application_id')
                ->whereNotIn('applications.status', [ApplicationStatus::Draft, ApplicationStatus::Rejected])
                ->get()
                ->map(function ($result) {
                    /** @phpstan-ignore-next-line */
                    $applicationIds = $result->application_ids;

                    /** @var Collection<array-key, Application> $applications */
                    $applications = Application::query()
                        ->whereIn('id', explode(',', $applicationIds))
                        ->get();

                    /** @phpstan-ignore-next-line */
                    return new ApplicationHashDTO($result->hash, $result->count, $applications);
                })
                ->groupBy('hash')
                ->map(function ($group) {
                    /** @phpstan-ignore-next-line */
                    $hash = $group->first()->hash;
                    $count = $group->sum('count');
                    $applications = $group->flatMap->applications;

                    return new ApplicationHashDTO($hash, $count, $applications);
                });
        })->values()->toBase();
    }

    /**
     * @return Collection<int, SubsidyStageHash>
     */
    public function getBankAccountSubsidyStageHash(Subsidy $subsidy): Collection
    {
        $subsidyStageHashQuery = $this->applicationHashService->getSubsidyStageHashJoinedQuery();

        $subsidyStageHashQuery
            ->where('subsidies.id', $subsidy->id)
            ->where('subsidy_stage_hashes.name', self::BANK_ACCOUNT_SUBSIDY_STAGE_HASH_NAME);

        if ($subsidyStageHashQuery->count() === 0) {
            throw new BankAccountSubsidyStageHashNotFoundException(
                sprintf(
                    'No \'%s\' SubsidyStageHash found for subsidy (%s)',
                    self::BANK_ACCOUNT_SUBSIDY_STAGE_HASH_NAME,
                    $subsidy->id
                )
            );
        }

        /** @phpstan-ignore-next-line */
        return $subsidyStageHashQuery
            ->get()
            ->toBase();
    }
}
