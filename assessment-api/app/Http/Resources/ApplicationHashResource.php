<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\Models\Application;

class ApplicationHashResource extends JsonResource
{
    public function __construct(string $hash, int $count, Collection $applications)
    {
        parent::__construct([
            'hash' => $hash,
            'count' => $count,
            'applications' => $applications,
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        /** @var Collection $applications */
        $applications = $this['applications'];

        return [
            'hash' => $this['hash'],
            'count' => $this['count'],
            'applications' => $applications->map(
                function (Application $application) {
                    return ['id' => $application->id, 'reference' => $application->reference];
                }
            )->toArray(),
        ];
    }
}
