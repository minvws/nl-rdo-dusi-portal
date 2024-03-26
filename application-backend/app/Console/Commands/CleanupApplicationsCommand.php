<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Console\Commands;

use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Application\Backend\Events\Logging\DeleteApplicationEvent;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationReferenceRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\Logging\Laravel\LogService;

class CleanupApplicationsCommand extends Command
{
    protected $signature = 'app:cleanup-applications {--dry-run}';

    protected $description = 'Clean up applications';

    private bool $dryRun = false;

    public function __construct(
        private readonly ApplicationReferenceRepository $applicationReferenceRepository,
        private readonly LogService $logService
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->dryRun = $this->option('dry-run') === true;

        if ($this->dryRun) {
            $this->comment('DryRun is set!');
        }

        /** @var Collection<int, Application> $applications */
        $applications = Application::query()
            ->where(function (Builder $query) {
                $query
                    ->where('updated_at', '<=', CarbonImmutable::now()->subDays(42)->startOfDay())
                    ->where('status', '=', ApplicationStatus::Draft)
                ;
            })
            ->orWhere(function (Builder $query) {
                $query
                    ->where('updated_at', '<=', CarbonImmutable::now()->subYears(10)->startOfDay())
                    ->whereIn(
                        'status',
                        [
                            ApplicationStatus::Approved,
                            ApplicationStatus::Rejected,
                            ApplicationStatus::Reclaimed,
                        ]
                    );
            })
            ->chunk(100, function (Collection $applications) {
                if ($applications->count() === 0) {
                    $this->error('No applications found to delete');

                    return;
                }

                DB::transaction(function () use ($applications) {
                    /** @var Collection<int, Identity> $identitiesCollection */
                    $identitiesCollection = new Collection();

                    $this->info(sprintf('Start cleanup of %d applications', $applications->count()));

                    foreach ($applications as $application) {
                        $applicationReference = $application->reference;
                        $this->info(sprintf('%s | Processing', $applicationReference));



                        $identitiesCollection->add($application->identity);

                        $this->deleteMessages($application);
                        $this->deleteApplicationStageTransitions($application);
                        $this->deleteApplicationStages($application);
                        $this->deleteApplicationHashes($application);
                        $this->deleteSurePayResults($application);

                        if (!$this->dryRun) {
                            $this->applicationReferenceRepository->setReferenceToDeleted($applicationReference);
                            $application->delete();

                            $this->logService->log((new DeleteApplicationEvent())
                                ->withData([
                                    'reference' => $application->reference,
                                ]));
                        }

                        $this->info(sprintf('%s | Deleted application', $applicationReference));
                        $this->newLine();
                    }

                    $this->info(sprintf('Check %d identities', $identitiesCollection->count()));

                    $i = 0;
                    foreach ($identitiesCollection as $identity) {
                        $applicationsForIdentityCount = $identity->applications->count();

                        if ($applicationsForIdentityCount === 0) {
                            if (!$this->dryRun) {
                                $identity->delete();
                            }
                            $i++;
                        }
                    }

                    $this->info(sprintf('Deleted %d of %d identities', $i, $identitiesCollection->count()));
                });
            })
        ;
    }

    private function deleteMessages(Application $application): void
    {
        $applicationReference = $application->reference;
        $applicationMessageCount = $application->applicationMessages->count();

        if (!$this->dryRun) {
            $application->applicationMessages()->delete();
        }

        $this->info(sprintf('%s | Deleted %d messages', $applicationReference, $applicationMessageCount));
    }

    private function deleteApplicationStageTransitions(Application $application): void
    {
        $applicationReference = $application->reference;
        $stageTransitionsCount = $application->applicationStageTransitions->count();

        if (!$this->dryRun) {
            $application->applicationStageTransitions()->delete();
        }

        $this->info(sprintf('%s | Deleted %d stageTransitions', $applicationReference, $stageTransitionsCount));
    }

    private function deleteApplicationStages(Application $application): void
    {
        $applicationReference = $application->reference;
        $applicationStageCount = $application->applicationStages->count();

        foreach ($application->applicationStages as $applicationStage) {
            $answerCount = $applicationStage->answers->count();

            if (!$this->dryRun) {
                $applicationStage->answers()->delete();
                $applicationStage->delete();
            }

            $this->info(
                sprintf(
                    '%s | Deleted %d answers from stage and deleted stage %s',
                    $applicationReference,
                    $answerCount,
                    $applicationStage->sequence_number,
                )
            );
        }

        $this->info(sprintf('%s | Deleted %d stages', $applicationReference, $applicationStageCount));
    }

    private function deleteApplicationHashes(Application $application): void
    {
        $applicationReference = $application->reference;
        $applicationHashesCount = $application->applicationHashes->count();

        if (!$this->dryRun) {
            $application->applicationHashes()->delete();
        }

        $this->info(sprintf('%s | Deleted %d hashes', $applicationReference, $applicationHashesCount));
    }

    private function deleteSurePayResults(Application $application): void
    {
        $applicationReference = $application->reference;

        if (!$this->dryRun) {
            $application->applicationSurePayResult?->delete();
        }

        $this->info(sprintf('%s | Deleted surePayResult', $applicationReference));
    }
}
