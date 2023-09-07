<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use MinVWS\DUSi\Assessment\API\Models\Enums\UIType;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MinVWS\DUSi\Assessment\API\Services\EncryptionService;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

//TODO: Move logic to service

/**
 *  @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationSubsidyVersionResource extends JsonResource
{
    private string|null $publicKey;

    /**
     * Create a new resource instance.
     *
     * @param Application $application
     * @param SubsidyVersion $subsidyVersion
     * @param string|null $publicKey
     */
    public function __construct(
        Application $application,
        SubsidyVersion $subsidyVersion,
        string|null $publicKey = null,
        private EncryptionService $encryptionService,
    ) {
        parent::__construct(['application' => $application, 'subsidyVersion' => $subsidyVersion]);
        $this->publicKey = $publicKey;
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
                ->orderBy('sequence_number', 'desc')
                ->filter(function ($applicationStage) use ($subsidyStage) {
                    return $applicationStage->subsidy_stage_id === $subsidyStage->id;
                })->first();

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
                'values' => $this->createValues($applicationStage, $subsidyStage->fields),
                'uischema' => $ui,
            ];
        });
        return [
            'metadata' => $this->createMetadata(),
            'stages' => $stages,
        ];
    }

    /**
     * @param string $value
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Exception
     */
    private function encrypt(string $value): string
    {
        try {
            if (isset($this->publicKey)) {
                return $this->encryptionService->sodiumEncrypt($value, $this->publicKey);
            } else {
                return $value; // TODO: remove when frontend can decrypt the sodium
            }
        } catch (\SodiumException $e) {
            throw new \SodiumException('Encryption failed', 0, $e);
        }
    }

    /**
     * @param string $value
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Exception
     */
    private function decrypt(string $value): string
    {
        return $this->encryptionService->decryptData($value);
    }

    /**
     * @param Collection<string, Field> $fields
     */
    private function createValues(?ApplicationStage $applicationStage, Collection $fields): ?array
    {
        $fieldsById = $fields->mapToDictionary(function ($field) {
            return [ $field->id => $field->code ];
        })
            ->map(function ($item) {
                return $item[0];
            });

        /** @var array<Answer>|null $answers */
        $answers = $applicationStage?->answers->all();
        if ($answers === null || count($answers) === 0) {
            return null;
        }

        return array_map(
            function ($answer) use ($fieldsById) {
                return [
                    $fieldsById[$answer->field_id] => $this->encrypt(
                        $this->decrypt($answer->encrypted_answer)
                    )
                ];
            },
            $answers
        );
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
