<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationHashResource;
use MinVWS\DUSi\Assessment\API\Services\BankAccountDuplicatesService;
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
        $this->authorize('viewBankAccountDuplicates', $subsidy);

        return ApplicationHashResource::collection(
            $this->bankAccountDuplicatesService->getDuplicatesForSubsidy($subsidy)
        );
    }
}
