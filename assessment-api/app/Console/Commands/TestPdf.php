<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Console\Commands;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageAnswer;
use MinVWS\DUSi\Shared\Application\DTO\LetterData;
use MinVWS\DUSi\Shared\Application\DTO\LetterStageData;
use MinVWS\DUSi\Shared\Application\DTO\LetterStages;
use MinVWS\DUSi\Shared\Application\Services\LetterService;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransitionMessage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Subsidy\Services\SubsidyFileManager;

use function Laravel\Prompts\error;
use function Laravel\Prompts\select;

class TestPdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-pdf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test basic PDF generation';

    public function __construct(protected LetterService $letterService, protected SubsidyFileManager $fileManager)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<array-key, SubsidyVersion> $subsidyVersions */
        $subsidyVersions = SubsidyVersion::with(['subsidy', 'subsidyStages'])->get();
        $subsidyVersionChoices = $this->getSubsidyVersionChoices($subsidyVersions);

        $chosenSubsidyVersionId = select(
            label: 'Which subsidy?',
            options: $subsidyVersionChoices,
        );

        /** @var SubsidyVersion|null $subsidyVersion */
        $subsidyVersion = $subsidyVersions->find($chosenSubsidyVersionId);
        if ($subsidyVersion === null) {
            error(sprintf('Subsidy version with ID %s not found!', $chosenSubsidyVersionId));
            return;
        }

        $transitionMessages = SubsidyStageTransitionMessage::query()
            ->published()
            ->whereHas('subsidyStageTransition', function ($query) use ($subsidyVersion) {
                $query->whereHas('currentSubsidyStage', function ($query) use ($subsidyVersion) {
                    $query->where('subsidy_version_id', $subsidyVersion->id);
                });
            })
            ->get();

        $transitionMessageChoices = $transitionMessages
            ->pluck('subject', 'id')
            ->toArray();

        $chosenTransitionMessageId = select(
            label: 'Which message?',
            options: $transitionMessageChoices,
        );

        /** @var SubsidyStageTransitionMessage|null $transitionMessage */
        $transitionMessage = $transitionMessages->find($chosenTransitionMessageId);
        if ($transitionMessage === null) {
            error(sprintf('Transition message with ID %s not found!', $chosenTransitionMessageId));
            return;
        }

        $stages = new LetterStages();
        foreach ($subsidyVersion->subsidyStages as $subsidyStage) {
            $stage = $this->fakeLetterStage();
            $stage->createdAt = new Carbon();
            $stage->submittedAt = new Carbon();
            $stage->closedAt = new Carbon();

            $stages->put('stage' . $subsidyStage->stage, $stage);
        }

        $letterData = new LetterData(
            subsidy: $subsidyVersion->subsidy,
            stages: $stages,
            createdAt: new CarbonImmutable(),
            contactEmailAddress: 'tester@irealisatie.nl',
            reference: '123456789',
            submittedAt: new CarbonImmutable(),
            fileManager: $this->fileManager,
            lastAllocatedAt: new CarbonImmutable()
        );

        $pdfContent = $this->letterService->generatePDFLetter($transitionMessage->content_pdf, $letterData);

        $fileNamePrefix = sprintf(
            'test-pdf-%s-%s',
            Str::slug($subsidyVersionChoices[$chosenSubsidyVersionId]),
            Str::slug($transitionMessageChoices[$chosenTransitionMessageId])
        );
        $fileName = tempnam(sys_get_temp_dir(), $fileNamePrefix . '-') . '.pdf';
        $result = file_put_contents($fileName, $pdfContent);
        if (!$result) {
            $this->error('Failed to write PDF to ' . $fileName);
            return;
        }

        $this->info('PDF written to ' . $fileName);

        // For local development with Laravel Sail, move the PDF to the storage directory
        if (env('LARAVEL_SAIL') !== null) {
            $newFileName = storage_path($fileNamePrefix . '.pdf');
            rename($fileName, $newFileName);
            $this->info('PDF moved to ' . $newFileName);
        }
    }

    protected function fakeLetterStage(): LetterStageData
    {
        return new class extends LetterStageData {
            public function get($key, $default = null): ?ApplicationStageAnswer
            {
                return new ApplicationStageAnswer($key, $default ?? '{' . $key . '}');
            }
        };
    }

    /**
     * @param Collection<array-key, SubsidyVersion> $subsidyVersions
     * @return array<string, string>
     */
    protected function getSubsidyVersionChoices(Collection $subsidyVersions): array
    {
        return $subsidyVersions->mapWithKeys(function (SubsidyVersion $version) {
            return [$version->id => $version->subsidy->code . ' - v' . $version->version];
        })->toArray();
    }
}
