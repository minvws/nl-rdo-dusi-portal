<?php

/**
 * phpcs:disable PSR1.Files.SideEffects
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Dompdf\Canvas;
use Dompdf\FontMetrics;
use Illuminate\Support\Facades\Log;
use MinVWS\Codable\JSON\JSONDecoder;
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
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationMessageRepository;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransitionMessage;

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
        private ApplicationStageEncryptionService $encryptionService,
        private JSONDecoder $jsonDecoder,
    ) {
    }

    private function convertAnswersToTemplateData(AnswersByApplicationStage $answers): ApplicationStages
    {
        $result = new ApplicationStages();
        foreach ($answers->stages as $applicationStageAnswers) {
            $stageKey = $applicationStageAnswers->stage->subsidyStage->stage;
            $stageData = new ApplicationStageData();

            $encrypter = $this->encryptionService->getEncrypter($applicationStageAnswers->stage);

            foreach ($applicationStageAnswers->answers as $answer) {
                assert($answer->field !== null);
                $answerKey = $answer->field->code;

                $value = $encrypter->decrypt($answer->encrypted_answer);
                if ($value === null) {
                    continue;
                }

                $value = match ($answer->field->type) {
                    FieldType::Upload => $this->jsonDecoder->decode($value)->decodeObject(FileList::class),
                    default => $value,
                };

                $answer = new ApplicationStageAnswer($answerKey, $value);
                $stageData->put($answerKey, $answer);
            }

            $result->put($stageKey, $stageData);
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
        $manifestPath = file_get_contents(public_path('build/manifest.json'));

        if (!$manifestPath) {
            return '';
        }

        $manifest = json_decode($manifestPath, true);
        $cssFile = $manifest['resources/scss/pdf.scss']['file'];

        return public_path('build/' . $cssFile);
    }


    private function collectGenericDataForTemplate(ApplicationStage $stage): LetterData
    {
        $answers = $this->applicationRepository->getAnswersForApplicationStagesUpToIncluding($stage);
        $data = $this->convertAnswersToTemplateData($answers);

        $cssPath = $this->getCssPath();
        $logoPath = public_path('img/vws_dusi_logo.svg');
        $signaturePath = public_path('img/vws_dusi_signature.jpg');

        return new LetterData(
            subsidyTitle: $stage->subsidyStage->subsidyVersion->subsidy->title,
            stages: $data,
            createdAt: $stage->application->created_at,
            contactEmailAddress: $stage->subsidyStage->subsidyVersion->contact_mail_address,
            reference: $stage->application->reference,
            motivation: '', // TODO: replace with motivation
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
            $values[] = $data->getStage($stageKey)?->get($answerKey);
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

        if (!$mailToNameIdentifier || !$mailToAddressIdentifier) {
            Log::info('No mailToNameIdentifier or mailToAddressIdentifier found');
            return;
        }

        $mailToAddress = $this->extractDataFromAnswers($mailToAddressIdentifier, $data);
        $mailToName = $this->extractDataFromAnswers($mailToNameIdentifier, $data);

        if (!$mailToAddress || !$mailToName) {
            Log::info('No mailToName or mailToAddress found');
            return;
        }

        LetterGeneratedEvent::dispatch(new DispositionMailData($mailToName, $mailToAddress));
    }

    /**
     * @throws Exception
     */
    public function generateLetters(SubsidyStageTransitionMessage $message, ApplicationStage $stage): void
    {
        $data = $this->collectGenericDataForTemplate($stage);

        $pdf = $this->generatePDFLetter($message->content_pdf, $data);
        $pdfPath = sprintf(
            'applications/%s/letters/%d/%s.pdf',
            $stage->application->id,
            $stage->sequence_number,
            'letter'
        );
        // TODO: encrypt
        $pdf->save($pdfPath, Disk::APPLICATION_FILES);

        $html = $this->generateHTMLLetter($message->content_html, $data);
        $htmlPath = sprintf(
            'applications/%s/letters/%d/%s.html',
            $stage->application->id,
            $stage->sequence_number,
            'letter'
        );
        // TODO: encrypt
        $this->filesystemManager->disk(Disk::APPLICATION_FILES)->put($htmlPath, $html);

        $this->messageRepository->createMessage($stage, $message->subject, $htmlPath, $pdfPath);

        $this->triggerMailNotification($stage, $data);
    }
}
