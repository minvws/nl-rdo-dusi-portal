<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Services\LetterService;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransitionMessage;
use MinVWS\DUSi\Shared\Subsidy\Services\SubsidyFileManager;

class TestPdfForApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-application-pdf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(protected LetterService $letterService, protected SubsidyFileManager $fileManager)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (!app()->isLocal()) {
            $this->error('This command should only be used locally');

            return;
        }

        // phpcs:disable Generic.Files.LineLength
        if (Str::startsWith(config('hsm_api.client_certificate_key_path'), '../')) {
            $this->error(
                'HSM_API_CLIENT_CERTIFICATE_KEY_PATH is set to use relative path which breaks PDF generation from console, remove the "../" from .env'
            );

            return;
        }

        if (Str::startsWith(config('hsm_api.client_certificate_path'), '../')) {
            $this->error(
                'HSM_API_CLIENT_CERTIFICATE_PATH is set to use relative path which breaks PDF generation from console, remove the "../" from .env'
            );

            return;
        }

        // Override the chroot for local to link the right assets of shared. Default this will resolve to /var/www/html
        // which fails for the shared folder
        config(['dompdf.options.chroot' => '/var/www']);

        $applications = Application::query();
        $applicationChoices = $applications->pluck('reference', 'id')->toArray();

        $selectedApplicationId = $this->choice('Which application would you like to use?', $applicationChoices);

        $selectedApplication = $applications->findOrFail($selectedApplicationId);
        assert($selectedApplication instanceof Application);

        $this->info(
            sprintf(
                'Selected "%s", with status: "%s"',
                $selectedApplication->reference,
                $selectedApplication->status->name
            )
        );

        $applicationMessageChoices = $selectedApplication->applicationMessages()->pluck('subject', 'id')->toArray();

        $selectedApplicationMessageId = $this->choice(
            'Which application message would you like to use?',
            $applicationMessageChoices
        );

        $selectedMessage = $selectedApplication->applicationMessages()->find($selectedApplicationMessageId);
        assert($selectedMessage instanceof ApplicationMessage);

        $this->info(sprintf('Selected "%s"', $selectedMessage->subject));

        $subsidyMessage = $selectedMessage
            ->applicationStageTransition
            ->subsidyStageTransition
            ->publishedSubsidyStageTransitionMessage
        ;
        assert($subsidyMessage instanceof SubsidyStageTransitionMessage);

        $pdfContent = $this->letterService->generatePdfPreview(
            $subsidyMessage,
            $selectedMessage->applicationStageTransition->previousApplicationStage
        );

        $filePath = storage_path('test.pdf');
        file_put_contents($filePath, $pdfContent);

        $this->info('PDF written to ' . $filePath);
    }
}
