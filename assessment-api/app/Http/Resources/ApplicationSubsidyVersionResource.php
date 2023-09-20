<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use MinVWS\DUSi\Assessment\API\Models\Enums\UIType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Assessment\API\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use stdClass;

//TODO: Move logic to service

/**
 *  @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationSubsidyVersionResource extends JsonResource
{
    public function __construct(
        readonly Application $application,
        readonly SubsidyVersion $subsidyVersion,
        private readonly ?string $publicKey = null, // @phpstan-ignore-line
        private readonly ResponseEncryptionService $responseEncryptionService,  // @phpstan-ignore-line
        private readonly ApplicationDataService $applicationDataService,
    ) {
        parent::__construct(['application' => $application, 'subsidyVersion' => $subsidyVersion]);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        $stages = $this['subsidyVersion']->subsidyStages->map(function ($subsidyStage) {
            $applicationStage = $this->resource['application']->applicationStages()
                ->where('subsidy_stage_id', '=', $subsidyStage->id)
                ->orderBy('sequence_number', 'desc')
                ->first();

            if (!isset($applicationStage) || $applicationStage->is_current) {
                $uiType = UIType::Input;
                $ui = $subsidyStage->publishedUI?->input_ui;
            } else {
                $uiType = UIType::View;
                $ui = $subsidyStage->publishedUI?->view_ui;
            }
            return [
                'metadata' => [
                    "title" => $subsidyStage->title,
                    "uiType" => $uiType,
                    "subjectRole" => $subsidyStage->subject_role,
                    "subjectOrganisation" => $subsidyStage->subject_organisation,
                ],
                'dataschema' => $this->createDataSchema($subsidyStage),
                'values' => $this->createValues($applicationStage),
                'uischema' => $ui,
            ];
        });
        return [
            'metadata' => $this->createMetadata(),
            'stages' => $stages,
        ];
    }

    /**
     * @param ApplicationStage|null $applicationStage
     */
    private function createValues(?ApplicationStage $applicationStage): ?stdClass
    {
        if ($applicationStage === null) {
            return null;
        }

        return $this->applicationDataService->getApplicationStageData($applicationStage);
    }

    private function createMetadata(): array
    {
        return [
            'subsidyVersionId' => $this->resource['subsidyVersion']->id,
            'applicationId' => $this->resource['application']->id,
            'subsidy' => [
                'id' => $this->resource['subsidyVersion']->subsidy->id,
                'title' => $this->resource['subsidyVersion']->subsidy->title,
                'description' => $this->resource['subsidyVersion']->subsidy->description,
                'validFrom' => $this->resource['subsidyVersion']->subsidy->valid_from->format('Y-m-d'),
                'validTo' => $this->resource['subsidyVersion']->subsidy->valid_to?->format('Y-m-d')
            ]
        ];
    }

    private function createDataSchema(SubsidyStage $subsidyStage): array
    {
        $result = [];
        $result['type'] = 'object';
        $result['properties'] = [];

        $required = [];
        foreach ($subsidyStage->fields as $field) {
            $result['properties'][$field->code] = $this->createFieldDataSchema($field);
            if ($field->is_required) {
                $required[] = $field->code;
            }
        }

        if (count($required) > 0) {
            $result['required'] = $required;
        }

        return $result;
    }

    /**
     * @param Field $field
     * @return array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function createFieldDataSchema(Field $field): array
    {

        $type = match ($field->type) {
            FieldType::TextNumeric => 'integer',
            FieldType::Checkbox => 'boolean',
            FieldType::Multiselect => 'array',
            default => 'string'
        };

        $result = [
            'type' => $type,
            'title' => $field->title,
        ];

        if ($type === 'integer') {
            $result['minimum'] = 0;
        }

        if (!empty($field->description)) {
            $result['description'] = $field->description;
        }

        if ($field->type === FieldType::Select) {
            $result['enum'] = $field->params['options'];
        } elseif ($field->type === FieldType::Multiselect) {
            $result['items'] = ["enum" => $field->params['options'], "type" => "string"];
        } elseif ($field->type === FieldType::Upload) {
            $result['file'] = true;
        } elseif ($field->type === FieldType::CustomBankAccount) {
            $result['iban'] = true;
        } elseif ($field->type === FieldType::TextTel) {
            $result['tel'] = true;
        } elseif ($field->type === FieldType::Checkbox) {
            $result['const'] = true;
        } elseif ($field->type === FieldType::TextEmail) {
            $result['format'] = 'email';
        }

        return $result;
    }
}
