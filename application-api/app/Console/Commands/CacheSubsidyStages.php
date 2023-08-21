<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Console\Commands;

use MinVWS\DUSi\Application\API\Services\CacheService;
use Illuminate\Console\Command;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;

class CacheSubsidyStages extends Command
{
    protected $signature = 'cache:subsidy-stages';
    protected $description = 'Caches the open forms for the active subsidies';

    public function handle(SubsidyRepository $subsidyRepository, CacheService $cacheService): int
    {
        $this->info('Retrieving subsidies...');
        $activeSubsidies = $subsidyRepository->getSubsidiesWithSubsidyStagesForSubjectRole(SubjectRole::Applicant);
        if (count($activeSubsidies) === 0) {
            $this->error('No active subsidies found!');
            return self::FAILURE;
        }

        $this->newLine();

        foreach ($activeSubsidies as $subsidy) {
            $this->info('Retrieving forms for subsidy "' . $subsidy->title . '"...');
            $subsidyStages = $subsidy->publishedVersion->subsidyStages->filter(
                function ($subsidyStage) {
                    return $subsidyStage->subject_role === SubjectRole::Applicant;
                }
            );

            if (count($subsidyStages) === 0) {
                $this->warn('No active forms found for subsidy!');
                $this->newLine();
                continue;
            }

            if (!empty($subsidyStages)) {
                $this->info('Caching forms for subsidy...');
                $this->withProgressBar(
                    $subsidyStages,
                    function (SubsidyStage $subsidyStage) use ($cacheService) {
                        $cacheService->cacheSubsidyStage($subsidyStage);
                    }
                );

                $this->newLine();

                $this->info("Cached forms:");
                foreach ($subsidyStages as $subsidyStage) {
                    $this->info("v" . $subsidyStage->version . ': ' . $subsidyStage->id);
                }
            }

            $this->newLine();
        }

        $this->info('Done caching forms');
        return self::SUCCESS;
    }
}
