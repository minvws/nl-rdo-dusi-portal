<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Enums\FieldType;
use App\Models\Enums\VersionStatus;
use App\Models\Field;
use App\Models\Form;
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
        DateTimeInterface $valid_from,
        DateTimeInterface $valid_to
    ): void {
        $subsidy = $this->formRepository->makeSubsidy();
        $subsidy->title = $title;
        $subsidy->description = $description;
        $subsidy->valid_from = $valid_from;
        $subsidy->valid_to = $valid_to;
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
        Form $form,
        string $title,
        string $description,
        FieldType $type,
        array $params,
        bool $is_required,
        string $code,
        string $source
    ): void {
        $field = $this->formRepository->makeField($form);
        $field->title = $title;
        $field->description = $description;
        $field->type = $type;
        $field->params = $params;
        $field->is_required = $is_required;
        $field->code = $code;
        $field->source = $source;
        $this->formRepository->saveField($field);
    }

    public function createFormUI(Form $form, int $version, VersionStatus $status, array $ui): void
    {
        $formUI = $this->formRepository->makeFormUI($form);
        $formUI->version = $version;
        $formUI->status = $status;
        $formUI->ui = $ui;
        $this->formRepository->saveFormUI($formUI);
    }

    public function updateForm(Form $form, string $subsidy_id, string $version, string $status): void
    {
        $form->update([
            'subsidy_id' => $subsidy_id,
            'version' => $version,
            'status' => $status,
        ]);
        $this->formRepository->saveForm($form);
    }

    public function deleteForm(Form $form): void
    {
        Field::query()->where('form_id', $form->id)->delete();
        Form::destroy($form->id);
    }
}
