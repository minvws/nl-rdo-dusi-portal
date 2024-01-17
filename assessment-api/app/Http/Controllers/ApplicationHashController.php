<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use MinVWS\DUSi\Assessment\API\Events\Logging\ClaimAssessmentEvent;
use MinVWS\DUSi\Assessment\API\Events\Logging\ViewBankAccountDuplicatesEvent;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationHashResource;
use MinVWS\DUSi\Assessment\API\Services\BankAccountDuplicatesService;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\Logging\Laravel\LogService;

class ApplicationHashController extends Controller
{
    public function __construct(
        private readonly BankAccountDuplicatesService $bankAccountDuplicatesService,
        private readonly LogService $logger,
    ) {
    }

    /**
     * @psalm-suppress InvalidTemplateParam
     */
    public function getBankAccountDuplicates(Subsidy $subsidy, Authenticatable $user): ResourceCollection
    {
        $this->authorize('viewBankAccountDuplicates', $subsidy);

        $duplicates = $this->bankAccountDuplicatesService->getDuplicatesForSubsidy($subsidy);

        assert($user instanceof User);

        $this->logger->log(
            (new ViewBankAccountDuplicatesEvent())
                ->withActor($user)
                ->withData(
                    [
                        'subsidyId' => $subsidy->id,
                    ]
                )
        );

        return ApplicationHashResource::collection($duplicates);
    }
}
