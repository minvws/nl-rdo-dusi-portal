<?php
/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

declare(strict_types=1);

namespace Database\Seeders\Traits;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

trait CreateField
{
    private function createField(
        string $subsidyStageId,
        string $code,
        string $title,
        string $type,
        ?string $description = null,
        ?array $params = null,
        bool $isRequired = true
    ): string {
        $id = Uuid::uuid4()->toString();

        DB::table('fields')->insert([
            'id' => $id,
            'code' => $code,
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'params' => json_encode($params),
            'is_required' => $isRequired,
        ]);

        DB::table('field_subsidy_stage')->insert([
            'subsidy_stage_id' => $subsidyStageId,
            'field_id' => $id,
        ]);

        return $id;
    }

    private function createTextField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        ?string $inputMode = null,
        ?int $maxLength = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: $inputMode !== null ? "text:$inputMode" : 'text',
            params: ['maxLength' => $maxLength],
            isRequired: $isRequired,
        );
    }

    private function createCheckboxField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'checkbox',
            isRequired: $isRequired,
        );
    }

    private function createSelectField(
        string $subsidyStageId,
        string $code,
        string $title,
        array $options,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'select',
            params: ['options' => $options],
            isRequired: $isRequired,
        );
    }

    private function createTextAreaField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'textarea',
            isRequired: $isRequired,
        );
    }

    private function createPostalCodeField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'custom:postalcode',
            isRequired: $isRequired,
        );
    }

    private function createCountryField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'custom:country',
            isRequired: $isRequired,
        );
    }

    private function createBankAccountField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'custom:bankaccount',
            isRequired: $isRequired,
        );
    }

    private function createUploadField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'upload',
            isRequired: $isRequired,
        );
    }
}
