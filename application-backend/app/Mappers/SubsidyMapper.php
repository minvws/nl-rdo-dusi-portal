<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Mappers;

use MinVWS\DUSi\Shared\Serialisation\Models\Application\Form as FormDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Subsidy as SubsidyDTO;
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
        return new FormDTO($subsidyVersion->subsidy->id, $subsidyVersion->version);
    }
}
