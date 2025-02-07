<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Mappers;

use MinVWS\DUSi\Shared\Serialisation\Models\Application\Form as FormDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Subsidy as SubsidyDTO;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

class SubsidyMapper
{
    public function mapSubsidyVersionToSubsidyDTO(SubsidyVersion $subsidyVersion, ?Subsidy $subsidy = null): SubsidyDTO
    {
        $subsidy = $subsidy ?? $subsidyVersion->subsidy;

        return new SubsidyDTO(
            $subsidy->code,
            $subsidyVersion->title ?? $subsidy->title,
            $subsidy->description,
            $subsidyVersion->subsidy_page_url,
            $subsidy->valid_from,
            $subsidy->valid_to,
            $subsidy->allow_multiple_applications,
            $subsidy->is_open_for_new_applications,
        );
    }

    public function mapSubsidyVersionToFormDTO(SubsidyVersion $subsidyVersion): FormDTO
    {
        $subsidyStage =
            $subsidyVersion
                ->subsidyStages
                ->filter(fn (SubsidyStage $stage) => $stage->subject_role === SubjectRole::Applicant)
                ->first();
        assert($subsidyStage instanceof SubsidyStage);
        return new FormDTO($subsidyStage->id, $subsidyVersion->version);
    }
}
