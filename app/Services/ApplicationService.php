<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Application;
use App\Shared\Models\Connection;
use App\Models\Submission\FieldValue;
use App\Models\Submission\FormSubmit;
use App\Repositories\ApplicationRepository;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class ApplicationService
{
    public function __construct(
        private FormDecodingService $decodingService,
        private EncryptionService $encryptionService,
        private ApplicationRepository $applicationRepository
    ) {
    }

    private function createApplication(FormSubmit $formSubmit): Application
    {
        $application = $this->applicationRepository->makeApplication($formSubmit->form);
        $this->applicationRepository->saveApplication($application);
        return $application;
    }

    private function createAnswer(Application $application, FieldValue $value): void
    {
        $answer = $this->applicationRepository->makeAnswer($application, $value->field);
        $answer->encryption_key_id = '';
        $answer->encrypted_answer = $this->encryptionService->encryptFieldValue($value);
        $this->applicationRepository->saveAnswer($answer);
    }

    /**
     * @throws Throwable
     */
    public function processFormSubmit(string $id, string $data): Application
    {
        $formSubmit = $this->decodingService->decodeFormSubmit($id, $data);

        $application = DB::connection(Connection::Application)->transaction(function () use ($formSubmit) {
            $application = $this->createApplication($formSubmit);

            foreach ($formSubmit->values as $value) {
                $this->createAnswer($application, $value);
            }

            return $application;
        });

        assert($application instanceof Application);
        return $application;
    }
}
