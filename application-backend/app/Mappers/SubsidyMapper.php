<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Mappers;

use MinVWS\DUSi\Shared\Serialisation\Models\Application\Form as FormDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Subsidy as SubsidyDTO;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

/**
 * @psalm-suppress InvalidTemplateParam
 */
class SubsidyMapper
{
    public function mapSubsidyVersionToSubsidyDTO(SubsidyVersion $subsidyVersion): SubsidyDTO
    {
        return new SubsidyDTO(
            $subsidyVersion->subsidy->code,
            $subsidyVersion->title ?? $subsidyVersion->subsidy->title,
            $subsidyVersion->subsidy->description,
            $subsidyVersion->subsidy_page_url,
            $subsidyVersion->subsidy->valid_from,
            $subsidyVersion->subsidy->valid_to,
            $subsidyVersion->subsidy->allow_multiple_applications,
            $subsidyVersion->subsidy->is_open_for_new_applications,
        );
    }

    public function mapSubsidyToSubsidyDTO(Subsidy $subsidy): SubsidyDTO
    {
        return new SubsidyDTO(
            $subsidy->code,
            $subsidy->title,
            $subsidy->description,
            null,
            $subsidy->valid_from,
            $subsidy->valid_to,
            $subsidy->allow_multiple_applications,
            $subsidy->is_open_for_new_applications,
        );
    }

    public function mapSubsidyVersionToFormDTO(SubsidyVersion $subsidyVersion): FormDTO
    {
        // TODO: form should be based on subsidy code and version
        $subsidyStage =
            $subsidyVersion
                ->subsidyStages
                ->filter(fn (SubsidyStage $stage) => $stage->subject_role === SubjectRole::Applicant)
                ->first();
        assert($subsidyStage instanceof SubsidyStage);
        return new FormDTO($subsidyStage->id, $subsidyVersion->version);
    }
}
