<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Translation\PotentiallyTranslatedString;
use MinVWS\DUSi\Shared\Application\Services\ClamAv\ClamAvService;
use Psr\Log\LoggerInterface;

readonly class ClamAv implements ValidationRule
{
    public function __construct(
        private ClamAvService $clamAvService,
        private ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * Run the validation rule.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     * @throws Exception
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->clamAvService->enabled()) {
            $this->logger?->warning('Skipping ClamAV scan because ClamAV is not enabled.');
            return;
        }

        if (!($value instanceof UploadedFile)) {
            $fail('The :attribute must be an uploaded file.');
            return;
        }

        $result = $this->clamAvService->scanFile($value->getPathname());

        if (!$result->isOk()) {
            $this->logger?->notice(
                'ClamAV scan failed',
                [
                    'failed' => $result->hasFailed(),
                    'error' => $result->isError(),
                    'found' => $result->isFound(),
                    'reason' => $result->getReason(),
                ],
            );

            $fail('The :attribute is not a valid file.');
            return;
        }

        $this->logger?->debug('ClamAV scan succeeded');
    }
}
