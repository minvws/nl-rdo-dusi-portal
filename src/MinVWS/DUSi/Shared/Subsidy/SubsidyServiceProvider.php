<?php

namespace MinVWS\DUSi\Shared\Subsidy;

use Illuminate\Support\ServiceProvider;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\FieldGroup;
use MinVWS\DUSi\Shared\Subsidy\Models\FieldGroupPurpose;
use MinVWS\DUSi\Shared\Subsidy\Models\FieldGroupUI;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHashField;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageUI;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldSource;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;

class SubsidyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('subsidyRepository', function () {
            return new Repositories\SubsidyRepository();
        });
        $this->app->bind('subsidy', function () {
            return new Subsidy();
        });
        $this->app->bind('subsidyVersion', function () {
            return new SubsidyVersion();
        });
        $this->app->bind('subsidyStage', function () {
            return new SubsidyStage();
        });
        $this->app->bind('subsidyStageHash', function () {
            return new SubsidyStageHash();
        });
        $this->app->bind('subsidyStageHashField', function () {
            return new SubsidyStageHashField();
        });
        $this->app->bind('subsidyStageUI', function () {
            return new SubsidyStageUI();
        });
        $this->app->bind('field', function () {
            return new Field();
        });
        $this->app->bind('fieldGroup', function () {
            return new FieldGroup();
        });
        $this->app->bind('fieldGroupPurpose', function () {
            return new FieldGroupPurpose();
        });
        $this->app->bind('fieldGroupUI', function () {
            return new FieldGroupUI();
        });

        $this->app->bind('fieldSource', FieldSource::class);

        $this->app->bind('fieldType', FieldType::class);

        $this->app->bind('fieldStatus', FieldStatus::class);
        $this->app->bind('versionStatus', VersionStatus::class);
        $this->app->bind('subjectRole', SubjectRole::class);
    }


    public function boot()
    {
        //
    }
}
