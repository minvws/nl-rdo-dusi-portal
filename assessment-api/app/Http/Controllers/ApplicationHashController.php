<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationHashResource;
use MinVWS\DUSi\Assessment\API\Services\BankAccountDuplicatesService;
use MinVWS\DUSi\Shared\Application\Models\ApplicationHash;
use MinVWS\DUSi\Shared\Serialisation\Http\Responses\EncodableResponse;
use MinVWS\DUSi\Shared\Serialisation\Http\Responses\EncodableResponseBuilder;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;

class ApplicationHashController extends Controller
{
    public function __construct(private readonly BankAccountDuplicatesService $bankAccountDuplicatesService)
    {
    }

    public function getBankAccountDuplicates(Subsidy $subsidy): ResourceCollection
    {

        $array = [
                [
                    'hash' => '1151a36e738c7e5933927387b1a55931ce4fbcfc45c8197476d6050aac5b59a7',
                    'count' => 3,
                    'application_references' => [
                        'LZNM62-64708882', 'ZJBI17-14557380','KZNM62-64708882'
                    ]
                ],
                [
                    'hash' => '1151a36e738c7e5933927387b1a55931ce4fbcfc45c8197476d6050aac5b59a7',
                    'count' => 2,
                    'application_references' => [
                        'LZNM62-64708882', 'ZJBI17-14557380'
                    ]
                ],
        ];

        return ApplicationHashResource::collection(
            $this->bankAccountDuplicatesService->getDuplicatesForSubsidy($subsidy)
                ->map(fn($item) => ApplicationHashResource::make($item->hash, $item->count, $item->application_ids))
            );
//            collect($array));

        return EncodableResponseBuilder::create($array)->build();
    }
}
