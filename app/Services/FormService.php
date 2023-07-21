<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Enums\FieldType;
use App\Models\Enums\VersionStatus;
use App\Models\Field;
use App\Models\SubsidyStage;
use App\Models\Subsidy;
use App\Repositories\FormRepository;
use DateTimeInterface;

class FormService
{
    public function __construct(
        private FormRepository $formRepository
    ) {
    }

    public function createSubsidy(
        string $title,
        string $description,
        DateTimeInterface $validFrom,
        DateTimeInterface $validTo
    ): void {
        $subsidy = $this->formRepository->makeSubsidy();
        $subsidy->title = $title;
        $subsidy->description = $description;
        $subsidy->valid_from = $validFrom;
        $subsidy->valid_to = $validTo;
        $this->formRepository->saveSubsidy($subsidy);
    }

    public function createForm(Subsidy $subsidy, int $version, VersionStatus $status): void
    {
        $form = $this->formRepository->makeForm($subsidy);
        $form->version = $version;
        $form->status = $status;
        $this->formRepository->saveForm($form);
    }

    public function createField(
        SubsidyStage $form,
        string       $title,
        string       $description,
        FieldType    $type,
        array        $params,
        bool         $isRequired,
        string       $code,
        string       $source
    ): void {
        $field = $this->formRepository->makeField($form);
        $field->title = $title;
        $field->description = $description;
        $field->type = $type;
        $field->params = $params;
        $field->is_required = $isRequired;
        $field->code = $code;
        $field->source = $source;
        $this->formRepository->saveField($field);
    }

    public function createFormUI(SubsidyStage $form, int $version, VersionStatus $status, array $uiArray): void
    {
        $formUI = $this->formRepository->makeFormUI($form);
        $formUI->version = $version;
        $formUI->status = $status;
        $formUI->ui = $uiArray;
        $this->formRepository->saveFormUI($formUI);
    }

    public function updateForm(SubsidyStage $form, string $subsidyId, string $version, string $status): void
    {
        $form->update([
            'subsidy_id' => $subsidyId,
            'version' => $version,
            'status' => $status,
        ]);
        $this->formRepository->saveForm($form);
    }

    public function deleteForm(SubsidyStage $form): void
    {
        Field::query()->where('form_id', $form->id)->delete();
        SubsidyStage::destroy($form->id);
    }
}
