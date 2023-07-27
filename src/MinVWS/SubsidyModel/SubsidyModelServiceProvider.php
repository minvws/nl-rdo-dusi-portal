<?php

namespace MinVWS\SubsidyModel;

use Illuminate\Support\ServiceProvider;

class SubsidyModelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('subsidyRepository', function () {
            return new Repositories\SubsidyRepository();
        });
        $this->app->bind('subsidy', function () {
            return new Models\Subsidy();
        });
        $this->app->bind('subsidyVersion', function () {
            return new Models\SubsidyVersion();
        });
        $this->app->bind('subsidyStage', function () {
            return new Models\SubsidyStage();
        });
        $this->app->bind('subsidyStageHash', function () {
            return new Models\SubsidyStageHash();
        });
        $this->app->bind('subsidyStageHashField', function () {
            return new Models\SubsidyStageHashField();
        });
        $this->app->bind('subsidyStageUI', function () {
            return new Models\SubsidyStageUI();
        });
        $this->app->bind('field', function () {
            return new Models\Field();
        });
        $this->app->bind('fieldGroup', function () {
            return new Models\FieldGroup();
        });
        $this->app->bind('fieldGroupPurpose', function () {
            return new Models\FieldGroupPurpose();
        });
        $this->app->bind('fieldGroupUI', function () {
            return new Models\FieldGroupUI();
        });
        $this->app->bind('fieldSource', function () {
            return new Models\Enums\FieldSource();
        });
        $this->app->bind('fieldType', function () {
            return new Models\Enums\FieldType();
        });
        $this->app->bind('fieldStatus', function () {
            return new Models\Enums\FieldStatus();
        });
        $this->app->bind('versionStatus', function () {
            return new Models\Enums\VersionStatus();
        });
        $this->app->bind('subjectRole', function () {
            return new Models\Enums\SubjectRole();
        });
    }


    public function boot()
    {
        //
    }
}
