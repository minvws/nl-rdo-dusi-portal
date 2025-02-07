<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Console\Commands;

use Database\Seeders\PCZMApplicationSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

class UpdateAssessorEnumAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-assessor-enum-answers {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates faulty enum assessor answers';

    protected ApplicationStageEncryptionService $encryptionService;
    protected bool $dryRun;

    /**
     * Execute the console command.
     */
    public function handle(ApplicationStageEncryptionService $applicationStageEncryptionService): void
    {
        $this->dryRun = $this->option('dry-run');
        $this->encryptionService = $applicationStageEncryptionService;

        if (!$this->dryRun) {
            $this->error(
                'This command will update the database. Use the --dry-run option to see what will be updated.'
            );
        } else {
            $this->info(
                'This command is running in dry-run mode. No changes will be made to the database.'
            );
        }

        if (
            !$this->confirm('
This command will update the assessor answers of the following fields:
- personalDataChecklist
- postCovidChecklist
Are you sure you want to continue?
        ')
        ) {
            return;
        }

        // phpcs:disable Generic.Files.LineLength
        DB::transaction(function () {
            $oldPersonalDataChecklistAnswer = 'Op basis van de SurePay terugkoppeling ben ik akkoord met het opgegeven rekeningnummer';
            $newPersonalDataChecklistAnswer = 'Op basis van de SurePay terugkoppeling, en de controle of de aanvrager onder bewind staat, ben ik akkoord met het opgegeven rekeningnummer';

            $this->updateAnswersOfField(
                fieldCode: 'personalDataChecklist',
                subsidyStageId: PCZMApplicationSeeder::PCZM_STAGE_2_UUID,
                answerNeedsToBeUpdated: fn (mixed $answer) => is_array($answer) && in_array($oldPersonalDataChecklistAnswer, $answer, true),
                newAnswer: fn (mixed $answer) => [
                    // Remove the old answer
                    ...array_diff($answer, [$oldPersonalDataChecklistAnswer]),
                    // Add the new answer
                    $newPersonalDataChecklistAnswer,
                ],
            );

            $oldPostCovidChecklistAnswer = 'Op basis van het sociaal-medisch verslag en/of de verklaring van de arts is vast te stellen dat er een post-COVID diagnose is gesteld';
            $newPostCovidChecklistAnswer = 'Op basis van het medisch onderzoeksverslag (medische rapportage) en/of de verklaring van de arts is vast te stellen dat er een post-COVID diagnose is gesteld';

            $this->updateAnswersOfField(
                fieldCode: 'postCovidChecklist',
                subsidyStageId: PCZMApplicationSeeder::PCZM_STAGE_2_UUID,
                answerNeedsToBeUpdated: fn (mixed $answer) => is_array($answer) && in_array($oldPostCovidChecklistAnswer, $answer, true),
                newAnswer: fn (mixed $answer) => [
                    // Remove the old answer
                    ...array_diff($answer, [$oldPostCovidChecklistAnswer]),
                    // Add the new answer
                    $newPostCovidChecklistAnswer,
                ],
            );
        });
        // phpcs:enable Generic.Files.LineLength
    }

    protected function updateAnswersOfField(
        string $fieldCode,
        string $subsidyStageId,
        callable $answerNeedsToBeUpdated,
        callable $newAnswer
    ): void {
        // Load field based on code and subsidy stage id because the field id is different in each environment
        $field = Field::whereCode($fieldCode)
            ->whereSubsidyStageId($subsidyStageId)
            ->first();

        if ($field === null) {
            $this->error("$fieldCode not found in subsidy stage $subsidyStageId");
            return;
        }

        $totalAnswersCount = Answer::whereFieldId($field->id)->count();
        $updatedAnswersCount = 0;

        $this->info("Updating answers for field $fieldCode");
        $this->info("Total answers: $totalAnswersCount");

        // Load answers of the field
        foreach (Answer::whereFieldId($field->id)->with('applicationStage')->lazy() as $answer) {
            $applicationStage = $answer->applicationStage;

            // Get application stage encrypter
            $encrypter = $this->encryptionService->getEncrypter($applicationStage);

            // Decrypt answer
            $decryptedAnswer = $encrypter->decrypt($answer->encrypted_answer);

            // Check if the answer needs to be updated
            if (!$answerNeedsToBeUpdated($decryptedAnswer)) {
                $this->info("Answer does not need to be updated");
                continue;
            }

            $this->info("Updating answer: {$answer->id}");
            $updatedAnswersCount++;

            if ($this->dryRun) {
                continue;
            }

            // Get new answer
            $newDecryptedAnswer = $newAnswer($decryptedAnswer);

            // Update answer
            $answer->encrypted_answer = $encrypter->encrypt($newDecryptedAnswer);
            $answer->save();
        }

        $this->info("Updated $updatedAnswersCount answers for field $fieldCode");
    }
}
