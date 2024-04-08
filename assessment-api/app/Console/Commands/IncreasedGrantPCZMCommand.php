<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageTransition;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Application\Services\Exceptions\ApplicationFlowException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\EvaluationTrigger;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use PHPUnit\Event\RuntimeException;
use function Laravel\Prompts\confirm;

/**
 * This command can only be run when the migration to insert the new stage (PCZM_STAGE_6_UUID) and new
 * transition (PZCM_TRANSITION_STAGE_6_TO_INCREASE_EMAIL) is run.
 */
class IncreasedGrantPCZMCommand extends Command
{
    public const PCZM_VERSION_UUID = '513011cd-789b-4628-ba5c-2fee231f8959';
    public const PCZM_STAGE_6_UUID = 'ef2238cf-a8ce-4376-ab2e-e821bc43ddb5';
    public const PZCM_TRANSITION_STAGE_5_TO_APPROVED = 'a27195df-9825-4d18-acce-9b3492221d8a';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pczm:increase-grant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send increased grant email PCZM V1';

    public function __construct(
        private readonly ApplicationFlowService $applicationFlowService,
        private readonly ApplicationStageEncryptionService $encryptionService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            SubsidyStage::findOrFail(self::PCZM_STAGE_6_UUID);
        } catch (\Exception $e) {
            $this->info("Please run migration '2024_04_02_152701_add_stage_pczm_v1.sql' first!");
            return;
        }

        $query = Application::query()
            ->with('lastApplicationStage')
            ->where('subsidy_version_id', self::PCZM_VERSION_UUID)
            ->where('status', ApplicationStatus::Approved);
        $query->toRawSql();
        $approvedApplications = $query->get();

        $approvedApplications->chunk(10, function (Collection $applications) {
                if ($applications->count() === 0) {
                    $this->error('No applications found to process');
                    return;
                }

                $applications->each(function(Application $application) {
                    DB::transaction(function () use ($application) {
                        try {
                            $applicationStage = $this->insertIncreasedGrantApplicationStage($application);
                            $this->updateApprovedApplicationStageTransition($application, $applicationStage);

                            //Advance to next stage which will send the 'increased-grant' letter
                            $this->performApplicationFlow($applicationStage);
                            $this->info(sprintf('Successfully transitioned %s(%s)', $application->reference, $application->id));
                        } catch (Exception $e) {
                            $this->error(
                                sprintf(
                                    'Error processing application %s: %s',
                                    $application->reference,
                                    $e->getMessage()
                                )
                            );
                        }
                    });
                });
            });

        $this->info('');

        $this->info('Operation completed!');
    }

    private function insertIncreasedGrantApplicationStage(Application $application): ApplicationStage
    {
        if (
            ApplicationStage::query()
            ->where('application_id', $application->id)
            ->where('subsidy_stage_id', self::PCZM_STAGE_6_UUID)
            ->exists()
        ) {
            throw new RuntimeException('Stage already exists! Reference: ' . $application->reference);
        }

        $applicationStage = new ApplicationStage();
        $applicationStage->application_id = $application->id;
        $applicationStage->subsidy_stage_id = self::PCZM_STAGE_6_UUID;
        $applicationStage->is_current = true;
        $applicationStage->is_submitted = false;
        $applicationStage->sequence_number = $application->lastApplicationStage->sequence_number + 1;
        [$encryptedKey] = $this->encryptionService->generateEncryptionKey();
        $applicationStage->encrypted_key = $encryptedKey;

        $applicationStage->save();

        return $applicationStage;
    }

    private function updateApprovedApplicationStageTransition(
        Application $application,
        ApplicationStage $applicationStage
    ): void {
        $applicationStageTransition = ApplicationStageTransition::where(
            'subsidy_stage_transition_id',
            self::PZCM_TRANSITION_STAGE_5_TO_APPROVED
        )
            ->where('application_id', $application->id)
            ->firstOrFail();

        $applicationStageTransition->new_application_stage_id = $applicationStage->id;
        $applicationStageTransition->save();
    }

    private function performApplicationFlow(ApplicationStage $applicationStage): void
    {
        $this->applicationFlowService->evaluateApplicationStage(
            $applicationStage,
            EvaluationTrigger::Submit
        );
    }
}
