<?php

/**
 * phpcs:disable PSR1.Files.SideEffects
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services;

use Barryvdh\DomPDF\Facade\Pdf as PDFHelper;
use Carbon\CarbonImmutable;
use Dompdf\Canvas;
use Dompdf\FontMetrics;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Latte\Engine as RenderEngine;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\DUSi\Shared\Application\DTO\AnswersByApplicationStage;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationStageAnswer;
use MinVWS\DUSi\Shared\Application\DTO\LetterStages;
use MinVWS\DUSi\Shared\Application\DTO\DispositionMailData;
use MinVWS\DUSi\Shared\Application\DTO\LetterData;
use MinVWS\DUSi\Shared\Application\DTO\LetterStageData;
use MinVWS\DUSi\Shared\Application\Events\LetterGeneratedEvent;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
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
        private ApplicationFileManager $applicationFileManager,
        private ApplicationMessageRepository $messageRepository,
        private ApplicationRepository $applicationRepository,
        private ApplicationStageEncryptionService $encryptionService,
        private JSONDecoder $jsonDecoder,
        private RenderEngine $engine,
    ) {
    }

    private function convertAnswersToTemplateData(AnswersByApplicationStage $answers): LetterStages
    {
        $result = new LetterStages();
        foreach ($answers->stages as $applicationStageAnswers) {
            $stageKey = 'stage' . $applicationStageAnswers->stage->subsidyStage->stage;
            $stageData = new LetterStageData();

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

    public function generatePDFLetter(string $template, LetterData $data): string
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

        return $pdf->output();
    }

    private function collectGenericDataForTemplate(ApplicationStage $stage): LetterData
    {
        $answers = $this->applicationRepository->getAnswersForApplicationStagesUpToIncluding($stage);
        $data = $this->convertAnswersToTemplateData($answers);
        $submittedAt = $stage->application->submitted_at;
        assert($submittedAt !== null);

        return new LetterData(
            subsidyTitle: $stage->subsidyStage->subsidyVersion->subsidy->title,
            stages: $data,
            createdAt: CarbonImmutable::now(),
            contactEmailAddress: $stage->subsidyStage->subsidyVersion->contact_mail_address,
            reference: $stage->application->reference,
            submittedAt: CarbonImmutable::createFromInterface($submittedAt)
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
            'applications/%s/letters/%d/%s',
            $stage->application->id,
            $stage->sequence_number,
            Str::uuid(),
        );
        $this->applicationFileManager->writeEncryptedFile($pdfPath, $pdf);

        $html = $this->generateHTMLLetter($message->content_html, $data);
        $htmlPath = sprintf(
            'applications/%s/letters/%d/%s',
            $stage->application->id,
            $stage->sequence_number,
            Str::uuid(),
        );
        $this->applicationFileManager->writeEncryptedFile($htmlPath, $html);

        $this->messageRepository->createMessage($stage, $message->subject, $htmlPath, $pdfPath);

        $this->triggerMailNotification($stage, $data);
    }

    public function generatePreview(SubsidyStageTransitionMessage $message, ApplicationStage $stage): string
    {
        $data = $this->collectGenericDataForTemplate($stage);
        $html = $this->generateHTMLLetter($message->content_html, $data);
        return $html;
    }
}
