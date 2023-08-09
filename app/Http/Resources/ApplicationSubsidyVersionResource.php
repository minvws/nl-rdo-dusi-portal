<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class ApplicationSubsidyVersionResource extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @param Application $application
     * @param SubsidyStage $subsidyStage
     * @return void
     */
    public function __construct(Application $application, SubsidyVersion $subsidyVersion)
    {
        parent::__construct(['application' => $application, 'subsidyVersion' => $subsidyVersion]);
    }

    public function toArray(Request $request)
    {
        $stages = $this['subsidyVersion']->subsidyStages->map(function ($subsidyStage) {
            $applicationStage = $this['application']->applicationStages
                ->filter(function ($applicationStage) use ($subsidyStage) {
                    return $applicationStage->subsidy_stage_id === $subsidyStage->id;
                })->first();
            $latestApplicationStageVersion = $applicationStage?->latestVersion;
            if( isset($applicationStage->latestVersion)
                && $latestApplicationStageVersion->status === ApplicationStageVersionStatus::Submitted)
            {
                $ui = $subsidyStage->publishedUI?->input_ui;
            } else {
                $ui = $subsidyStage->publishedUI?->review_ui;
            }
            return [
                'metadata' => [
                  $subsidyStage->title,
                ],
                'dataSchema' => $this->createDataSchema($subsidyStage),
                'values' => $this->createValues($subsidyStage, $latestApplicationStageVersion),
                'uiSchema' => $ui,
            ];
        });

        return [
            'metadata' => $this->createMetadata(),
            'stages' => $stages,
        ];
    }

    private function encrypt($value, $key)
    {
        //TODO encrypt
        return base64_encode($value);
    }

    private function decrypt($value, $key)
    {
        //TODO decrypt
        try{
            return base64_decode($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    private function createValues(SubsidyStage $subsidyStage, ?ApplicationStageVersion $applicationStageVersion)
        : ?array {
        $encryption_key = "";
        return $applicationStageVersion?->answers->map(function($answer) use ($encryption_key){
           return $this->encrypt($this->decrypt($answer->encrypted_answer, $encryption_key), $encryption_key);
        })->toArray();
    }

    private function createMetadata(): array
    {
        return [
            'id' => $this['subsidyVersion']->id,
            'subsidy' => [
                'id' => $this['subsidyVersion']->subsidy->id,
                'title' => $this['subsidyVersion']->subsidy->title,
                'description' => $this['subsidyVersion']->subsidy->description,
                'validFrom' => $this['subsidyVersion']->subsidy->valid_from->format('Y-m-d'),
                'validTo' => $this['subsidyVersion']->subsidy->valid_to?->format('Y-m-d')
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

    private function createFieldDataSchema(Field $field): array
    {

        $type = match ($field->type) {
            FieldType::TextNumeric => 'integer',
            FieldType::Checkbox => 'boolean',
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
