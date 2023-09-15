<?php

/**
 * phpcs:disable PSR1.Files.SideEffects
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Dompdf\Canvas;
use Dompdf\FontMetrics;
use MinVWS\DUSi\Assessment\API\DTO\ApplicationStageAnswer;
use MinVWS\DUSi\Assessment\API\DTO\ApplicationStageData;
use MinVWS\DUSi\Assessment\API\DTO\ApplicationStages;
use MinVWS\DUSi\Assessment\API\DTO\DispositionMailData;
use MinVWS\DUSi\Assessment\API\DTO\LetterData;
use Barryvdh\DomPDF\Facade\Pdf as PDFHelper;
use Barryvdh\DomPDF\PDF;
use Exception;
use Illuminate\Filesystem\FilesystemManager;
use Latte\Engine as RenderEngine;
use MinVWS\DUSi\Assessment\API\Events\LetterGeneratedEvent;
use MinVWS\DUSi\Shared\Application\DTO\AnswersByApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationMessageRepository;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStageDecision;
use Illuminate\Support\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

readonly class LetterService
{
    public function __construct(
        private ApplicationRepository $applicationRepository,
        private ApplicationMessageRepository $messageRepository,
        private FilesystemManager $filesystemManager,
        private RenderEngine $engine,
        private EncryptionService $encryptionService,
    ) {
    }

    private function convertAnswersToTemplateData(AnswersByApplicationStage $answers): ApplicationStages
    {
        $result = new ApplicationStages();
        foreach ($answers->stages as $applicationStageAnswers) {
            $stageKey = 'stage' . $applicationStageAnswers->stage->subsidyStage->stage;
            $stageData = new ApplicationStageData($stageKey);

            foreach ($applicationStageAnswers->answers as $answer) {
                assert($answer->field !== null);
                $answerKey = $answer->field->code;
                $answerData = null;

                if ($answer->encrypted_answer !== null) {
                    $answerData = json_decode($this->encryptionService->decryptData($answer->encrypted_answer), true);
                }

                $answer = new ApplicationStageAnswer($answerKey, $answerData);
                $stageData->$answerKey = $answer;
            }

            $result->$stageKey = $stageData;
        }

        return $result;
    }

    private function generateHTMLLetter(string $template, LetterData $data): string
    {
        $templateKey = uniqid();
        $loader = $this->engine->getLoader();

        if ($loader instanceof LatteLetterLoaderService) {
            $loader->addTemplate($templateKey, $template);
        }

        return $this->engine->renderToString($templateKey, ['content' => $data]);
    }

    private function generatePDFLetter(string $template, LetterData $data): PDF
    {
        $html = $this->generateHTMLLetter($template, $data);

        $pdf = PDFHelper::loadHTML($html);
        $pdf->render();

        $pdfCanvas = $pdf->getDomPDF()->getCanvas();

        // add page numbers
        $pdfCanvas->page_script(
            function (int $currentPage, int $totalPages, Canvas $canvas, FontMetrics $fontMetrics) {
                $font = 'RO Sans Web';
                $color = [0, 0, 0];
                $size = 7;

                $text = sprintf('Pagina %s van %s', $currentPage, $totalPages);
                $textWidth = $fontMetrics->getTextWidth($text, $font, $size);

                $x = $canvas->get_width() - $textWidth - 88;
                $y = $canvas->get_height() - 35;

                $canvas->text($x, $y, $text, $font, $size, $color);
            }
        );

        if (!app()->environment('production')) {
            // This will add a watermark to every page when not in production
            $pdfCanvas->page_script(
                function (int $currentPage, int $totalPages, Canvas $canvas, FontMetrics $fontMetrics) {
                    $font = 'RO Sans Web';
                    $color = [0, 0, 0];
                    $size = 80;
                    $canvasWidth = $canvas->get_width();
                    $canvasHeight = $canvas->get_height();

                    $text = "TESTDOCUMENT";
                    $textWidth = $fontMetrics->getTextWidth($text, $font, $size);

                    $x = $canvasWidth - $textWidth + 20;
                    $y = $canvasHeight / 2;

                    if ($currentPage === 1) {
                        $y += 100;
                    }

                    $canvas->set_opacity(0.3, "Multiply");
                    $canvas->text($x, $y, $text, $font, $size, $color, 0, 2, -30);
                }
            );
        }

        return $pdf;
    }

    private function getCssPath(): string
    {
        $manifestPath = file_get_contents(__DIR__ . '/../../public/build/manifest.json');

        if (!$manifestPath) {
            return '';
        }

        $manifest = json_decode($manifestPath);
        $cssFile = $manifest->{'resources/scss/pdf.scss'}->file;

        return public_path('build/' . $cssFile);
    }


    private function collectGenericDataForTemplate(ApplicationStage $stage): LetterData
    {
        $answers = $this->applicationRepository->getAnswersForApplicationStagesUpToIncluding($stage);
        $data = $this->convertAnswersToTemplateData($answers);

        $cssPath = $this->getCssPath();
        $logoPath = public_path('img/vws_dusi_logo.svg');
        $signaturePath = public_path('img/vws_dusi_signature.jpg');

        // TODO/FIXME: This is temporal code to be able te generate letters easily from the command app:generate-pdf
        // or on application submit (application-backend)
        $generateLettersBypassConfig = Config('services.letter_service.generate_letters_bypass_assertions', false);
        if (is_null($stage->assessor_decision) && $generateLettersBypassConfig) {
            /** @var ApplicationStageDecision $randomDecision */
            $randomDecision = Collection::make(ApplicationStageDecision::cases())->random();
            $stage->assessor_decision = $randomDecision;
        }

        assert($stage->assessor_decision !== null);

        return new LetterData(
            subsidyTitle: $stage->subsidyStage->subsidyVersion->subsidy->title,
            decision: $stage->assessor_decision->value,
            stages: $data,
            createdAt: $stage->application->created_at,
            contactEmailAddress: $stage->subsidyStage->subsidyVersion->contact_mail_address,
            reference: $stage->application->reference,
            motivation: 'TODO: Motivation from decision', // TODO: replace with motivation
            appointedSubsidy: number_format(100099 / 100, 2, ',', '.'), // TODO: replace with appointed subsidy
            applicationCode: null,
            cssPath: $cssPath,
            logoPath: $logoPath,
            signaturePath: $signaturePath,
        );
    }

    private function extractDataFromAnswers(string $identifier, LetterData $data): string
    {
        // stageId:fieldCode
        if (str_contains($identifier, ':')) {
            [$stageKey, $fieldCode] = explode(':', $identifier);
        } else {
            $stageKey = 'stage1';
            $fieldCode = $identifier;
        }

        // when several fields are combined like "firstName;lastName", they are returned as "firstName lastName"
        $values = [];
        foreach (explode(';', $fieldCode) as $answerKey) {
            $values[] = $data->getStage($stageKey)?->getAnswerData($answerKey);
        }

        $valueString = implode(' ', $values);

        return trim(str_replace('  ', ' ', $valueString));
    }

    private function triggerMailNotification(ApplicationStage $stage, LetterData $data): void
    {
        $subsidyVersion = $stage->subsidyStage->subsidyVersion;

        $mailToAddressIdentifier = $subsidyVersion->mail_to_address_field_identifier;
        $mailToNameIdentifier = $subsidyVersion->mail_to_name_field_identifier;

        assert($mailToAddressIdentifier !== null && $mailToAddressIdentifier !== '');
        assert($mailToNameIdentifier !== null && $mailToNameIdentifier !== '');

        $mailToAddress = $this->extractDataFromAnswers($mailToAddressIdentifier, $data);
        $mailToName = $this->extractDataFromAnswers($mailToNameIdentifier, $data);

        LetterGeneratedEvent::dispatch(new DispositionMailData($mailToName, $mailToAddress));
    }

    /**
     * @throws Exception
     */
    public function generateLetters(ApplicationStage $stage): void
    {
        $letter = $stage->subsidyStage->subsidyVersion->publishedSubsidyLetter;
        if ($letter === null) {
            throw new Exception('No published subsidy letter template found!');
        }

        $data = $this->collectGenericDataForTemplate($stage);

        $pdf = $this->generatePDFLetter($letter->content_pdf, $data);
        $pdfPath = sprintf(
            'applications/%s/letters/%d/%s.pdf',
            $stage->application->id,
            $stage->sequence_number,
            'letter'
        );
        // TODO: encrypt
        $pdf->save($pdfPath, Disk::APPLICATION_FILES);

        $html = $this->generateHTMLLetter($letter->content_view, $data);
        $htmlPath = sprintf(
            'applications/%s/letters/%d/%s.html',
            $stage->application->id,
            $stage->sequence_number,
            'letter'
        );
        // TODO: encrypt
        $this->filesystemManager->disk(Disk::APPLICATION_FILES)->put($htmlPath, $html);

        $this->messageRepository->createMessage($stage, $htmlPath, $pdfPath);

        $this->triggerMailNotification($stage, $data);
    }
}
