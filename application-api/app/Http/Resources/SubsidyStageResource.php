<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Shared\Subsidy\Helpers\SubsidyStageDataSchemaBuilder;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;

/**
 * @property mixed $publishedUI
 * @property mixed $id
 * @property mixed $subsidyVersion
 * @property mixed $fields
 */
class SubsidyStageResource extends JsonResource
{
    public function __construct(
        private readonly SubsidyStage $subsidyStage,
        private readonly SubsidyStageDataSchemaBuilder $dataSchemaBuilder
    ) {
        parent::__construct($subsidyStage);
    }

    /**
     * @param Request $request
     *
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        return [
            'metadata' => $this->createMetadata(),
            'dataschema' => $this->createDataSchema(),
            'uischema' => $this->publishedUI?->input_ui,
            'viewschema' => $this->publishedUI?->view_ui
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function createMetadata(): array
    {
        return [
            'id' => $this->id,
            'subsidy' => [
                'id' => $this->subsidyVersion->subsidy->id,
                'title' => $this->subsidyVersion->subsidy->title,
                'description' => $this->subsidyVersion->subsidy->description,
                'validFrom' => $this->subsidyVersion->subsidy->valid_from->format('Y-m-d'),
                'validTo' => $this->subsidyVersion->subsidy->valid_to?->format('Y-m-d')
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function createDataSchema(): array
    {
        return $this->dataSchemaBuilder->buildDataSchema($this->subsidyStage);
    }
}
