<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationHashResource;
use MinVWS\DUSi\Assessment\API\Services\BankAccountDuplicatesService;
use MinVWS\DUSi\Shared\Application\DTO\ApplicationHash as ApplicationHashDTO;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;

class ApplicationHashController extends Controller
{
    public function __construct(
        private readonly BankAccountDuplicatesService $bankAccountDuplicatesService
    ) {
    }

    /**
     * @psalm-suppress InvalidTemplateParam
     */
    public function getBankAccountDuplicates(Subsidy $subsidy): ResourceCollection
    {
        return ApplicationHashResource::collection(
            $this->bankAccountDuplicatesService->getDuplicatesForSubsidy($subsidy)
                ->map(fn(ApplicationHashDTO $item) =>
                        ApplicationHashResource::make($item->hash, $item->count, $item->applicationIds))
        );
    }
}
