<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationsFilter;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationStageEncryptionService;

class ApplicationExportService
{
    public function __construct(
        private readonly ApplicationStageEncryptionService $encryptionService,
    ) {
        //
    }

    public function exportApplications(ApplicationsFilter $filter): \Generator
    {
        $query = Application::query()
            ->with(['applicationSurePayResult'])
            ->orderBy('created_at');

        $this->applyFilters($query, $filter);

        foreach ($query->lazy(100) as $application) {
            assert($application instanceof Application);

            if ($application->firstApplicationStage === null) {
                Log::error(
                    sprintf(
                        'No applicationStage found for application %s / %s',
                        $application->reference,
                        $application->id
                    )
                );

                continue;
            }

            $firstStage = $application->firstApplicationStage;
            assert($firstStage instanceof ApplicationStage);

            $firstStage->load(['answers', 'answers.field']);

            $encrypter = $this->encryptionService->getEncrypter($firstStage);
            assert($encrypter instanceof Encrypter);

            if ($firstStage->answers->count() === 0) {
                Log::error(
                    sprintf(
                        'No answers found for application %s / %s',
                        $application->reference,
                        $application->id
                    )
                );
                continue;
            }

            $answers = $firstStage->answers->pluck('encrypted_answer', 'field.code');

            Log::debug(sprintf('Add data for %s', $application->reference));

            $lastName = [
                $this->getDecryptedAnswerByCode($answers, 'infix', $encrypter),
                $this->getDecryptedAnswerByCode($answers, 'lastName', $encrypter),
            ];

            $houseNumber = [
                $this->getDecryptedAnswerByCode($answers, 'houseNumber', $encrypter),
                $this->getDecryptedAnswerByCode($answers, 'houseNumberSuffix', $encrypter),
            ];

            $row = [
                'Dossiernummer' => $application->reference,
                'Voornaam' => $this->getDecryptedAnswerByCode($answers, 'firstName', $encrypter),
                'Tussenvoegsel + Achternaam' => trim(implode(' ', $lastName)),
                'Straatnaam' => $this->getDecryptedAnswerByCode($answers, 'street', $encrypter),
                'Huisnummer + HuisnummerToevoeging' => implode('', $houseNumber),
                'Postcode' => $this->getDecryptedAnswerByCode($answers, 'postalCode', $encrypter),
                'Woonplaats' => $this->getDecryptedAnswerByCode($answers, 'city', $encrypter),
                'NaamRekeninghouder' => $this->getDecryptedAnswerByCode($answers, 'bankAccountHolder', $encrypter),
                'IBAN' => $this->getDecryptedAnswerByCode($answers, 'bankAccountNumber', $encrypter),
                'SurePay resultaat IBAN-Bestaan' =>
                    $application->applicationSurePayResult?->account_number_validation->value,
                'SurePay resultaat Naam-IBAN' => $application->applicationSurePayResult?->name_match_result?->value,
                'SurePay resultaat AccountType (organisatie/persoon)' =>
                    $application->applicationSurePayResult?->account_type?->value,
                'SurePay resultaat Actief/inactief' => $application->applicationSurePayResult?->status?->value,
            ];

            yield $row;
        }
    }

    /**
     * @param Collection<array-key, mixed> $answers
     */
    private function getDecryptedAnswerByCode(
        Collection $answers,
        string $fieldCode,
        Encrypter $encrypter
    ): string|int|null {
        $encryptedAnswer = $answers->get($fieldCode);

        if ($encryptedAnswer === null) {
            Log::debug('No encryptedAnswer', ['field' => $fieldCode]);

            return null;
        }

        return $encrypter->decrypt($encryptedAnswer);
    }

    /**
     * @psalm-param Builder $query
     * @phpstan-param Builder<Application> $query
     * @param ApplicationsFilter $filter
     * @return void
     */
    private function applyFilters(Builder $query, ApplicationsFilter $filter): void
    {
        $filterValues = [
            'dateFrom' => 'updatedAtFrom',
            'dateTo' => 'updatedAtTo',
            'status' => 'status',
            'subsidy' => 'subsidyCode',
        ];

        foreach ($filterValues as $filterKey => $method) {
            $query->when(
                isset($filter->$filterKey) || is_array($filter->$filterKey),
                fn() => $query->$method($filter->$filterKey)->get()
            );
        }
    }
}
