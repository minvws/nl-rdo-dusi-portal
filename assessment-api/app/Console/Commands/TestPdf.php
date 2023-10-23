<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Console\Commands;

use DateTimeImmutable;
use Illuminate\Console\Command;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageAnswer;
use MinVWS\DUSi\Shared\Application\DTO\LetterData;
use MinVWS\DUSi\Shared\Application\DTO\LetterStageData;
use MinVWS\DUSi\Shared\Application\DTO\LetterStages;
use MinVWS\DUSi\Shared\Application\Services\LetterService;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Services\SubsidyFileManager;

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
        $template = <<<EOF
{layout 'letter_layout.latte'}

{block concern}
    Betreft: Vragen over aanvraag ....
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>Dit is een test PDF</p>

    <p>&nbsp;</p>
{/block}

{block sidebar}
    {include parent}
{/block}
EOF;

        $dataStage1 = new LetterStageData();
        $dataStage1->put('firstName', new ApplicationStageAnswer('firstName', 'John'));
        $dataStage1->put('lastName', new ApplicationStageAnswer('lastName', 'Doe'));
        $dataStage1->put('street', new ApplicationStageAnswer('street', 'Main Street'));
        $dataStage1->put('houseNumber', new ApplicationStageAnswer('houseNumber', '1'));
        $dataStage1->put('houseNumberSuffix', new ApplicationStageAnswer('houseNumberSuffix', 'A'));
        $dataStage1->put('postalCode', new ApplicationStageAnswer('postalCode', '1234 AB'));
        $dataStage1->put('city', new ApplicationStageAnswer('city', 'Amsterdam'));

        $stages = new LetterStages();
        $stages->put('stage1', $dataStage1);

        $subsidy = Subsidy::find('06a6b91c-d59b-401e-a5bf-4bf9262d85f8');
        if ($subsidy === null) {
            $this->error('Could not find fixed subsidy `06a6b91c-d59b-401e-a5bf-4bf9262d85f8` to create PDF');
            return;
        }

        $letterData = new LetterData(
            subsidy: $subsidy,
            stages: $stages,
            createdAt: new DateTimeImmutable(),
            contactEmailAddress: 'tester@rdobeheer.nl',
            reference: '123456789',
            submittedAt: new DateTimeImmutable(),
            fileManager: $this->fileManager
        );

        $pdfContent = $this->letterService->generatePDFLetter($template, $letterData);
        file_put_contents(storage_path('test.pdf'), $pdfContent);
    }
}
