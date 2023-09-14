<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Mappers;

use MinVWS\DUSi\Shared\Serialisation\Models\Application\Form as FormDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Subsidy as SubsidyDTO;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class SubsidyMapper
{
    public function mapSubsidyVersionToSubsidyDTO(SubsidyVersion $subsidyVersion): SubsidyDTOgit
    {
        return new SubsidyDTO(
            $subsidyVersion->subsidy->code,
            $subsidyVersion->title ?? $subsidyVersion->subsidy->title,
            $subsidyVersion->subsidy->description,
            $subsidyVersion->subsidy_page_url
        );
    }

    public function mapSubsidyVersionToFormDTO(SubsidyVersion $subsidyVersion): FormDTO
    {
        // TODO: form should be based on subsidy code and version
        $subsidyStage =
            $subsidyVersion
                ->subsidyStages
                ->filter(fn ($s) => $s->subject_role === SubjectRole::Applicant)
                ->first();
        return new FormDTO($subsidyStage?->id ?? $subsidyVersion->id, $subsidyVersion->version);
    }
}
