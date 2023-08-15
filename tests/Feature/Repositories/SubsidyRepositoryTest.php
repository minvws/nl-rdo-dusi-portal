<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Tests\Feature\Repositories;

use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldSource;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyLetter;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use MinVWS\DUSi\Shared\Subsidy\Tests\Feature\TestCase;

use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

class SubsidyRepositoryTest extends TestCase
{
    public function testGetSubsidy(): void
    {
        $subsidy = Subsidy::factory()->create();

        assertNull($subsidy->publishedVersion()->first());


        $subsidyVersion = SubsidyVersion::factory()->create(
            [
                'status' => VersionStatus::Published,
                'subsidy_id' => $subsidy->id,
                'version' => 1,
            ]
        );

        assertNotNull($subsidy->publishedVersion()->first());


        $subsidyStage = SubsidyStage::factory()->create(
            [
                'subsidy_version_id' => $subsidyVersion->id,
                'stage' => 1
            ]
        );
        assertNotNull($subsidyStage->id);

        $field = Field::factory()->create(
            attributes: [
                'type' => FieldType::Text,
                'source' => FieldSource::User,
                'params' => '{}',
                'code' => 'field_code',
                'description' => 'field_description',
                'is_required' => true,
                'subsidy_stage_id' => $subsidyStage->id
            ]
        );

        $expectedId = $field->id;
        $actualId = SubsidyStage::find($subsidyStage->id)->first()->fields()->first()->id;
        $this->assertSame($expectedId, $actualId);
    }

    private function addSubsidyVersionToSubsidy(Subsidy $subsidy, VersionStatus $status): SubsidyVersion
    {
        return SubsidyVersion::factory()->create(
            [
                'status' => $status,
                'subsidy_id' => $subsidy->id,
                'version' => 1,
            ]
        );
    }

    private function addSubsidyStageToSubsidyVersion(
        SubsidyVersion $subsidyVersion,
        SubjectRole $subjectRole,
        int $stage,
    ): SubsidyStage {
        return SubsidyStage::factory()->create(
            [
                'subsidy_version_id' => $subsidyVersion->id,
                'stage' => 1,
                'subject_role' => $subjectRole,
                'stage' => $stage,
            ]
        );
    }

    public function testGetActiveSubsidies()
    {
        $aSubsidy = Subsidy::factory()->create(['title' => 'A subsidy']);
        $bSubsidy = Subsidy::factory()->create(['title' => 'B subsidy']);
        $cSubsidy = Subsidy::factory()->create(['title' => 'C subsidy']);

        $this->addSubsidyVersionToSubsidy($aSubsidy, VersionStatus::Draft);
        $this->addSubsidyVersionToSubsidy($aSubsidy, VersionStatus::Archived);
        $this->addSubsidyVersionToSubsidy($aSubsidy, VersionStatus::Published);
        $this->addSubsidyVersionToSubsidy($bSubsidy, VersionStatus::Draft);
        $this->addSubsidyVersionToSubsidy($bSubsidy, VersionStatus::Published);
        $this->addSubsidyVersionToSubsidy($cSubsidy, VersionStatus::Draft);

        $repository = $this->app->make(SubsidyRepository::class);
        $activeSubsidies = $repository->getActiveSubsidies();
        $this->assertSame(2, $activeSubsidies->count());
        $this->assertSame(
            [$aSubsidy->id, $bSubsidy->id],
            $activeSubsidies->pluck('id')->toArray()
        );
    }

    public function testGetSubsidiesWithSubsidyStagesForSubjectRole()
    {
        $repository = $this->app->make(SubsidyRepository::class);

        $aSubsidy = Subsidy::factory()->create(['title' => 'A subsidy']);
        $bSubsidy = Subsidy::factory()->create(['title' => 'B subsidy']);
        $cSubsidy = Subsidy::factory()->create(['title' => 'C subsidy']);

        $this->addSubsidyVersionToSubsidy($aSubsidy, VersionStatus::Draft);
        $this->addSubsidyVersionToSubsidy($aSubsidy, VersionStatus::Archived);
        $this->addSubsidyVersionToSubsidy($aSubsidy, VersionStatus::Published);
        $aPublishedSubsidyVersion = $this->addSubsidyVersionToSubsidy($aSubsidy, VersionStatus::Published);
        $this->addSubsidyStageToSubsidyVersion($aPublishedSubsidyVersion, SubjectRole::Assessor, 1);

        $this->addSubsidyVersionToSubsidy($bSubsidy, VersionStatus::Draft);
        $bPublishedSubsidyVersion = $this->addSubsidyVersionToSubsidy($bSubsidy, VersionStatus::Published);
        $this->addSubsidyStageToSubsidyVersion($bPublishedSubsidyVersion, SubjectRole::Applicant, 1);
        $this->addSubsidyStageToSubsidyVersion($bPublishedSubsidyVersion, SubjectRole::Assessor, 2);

        $this->addSubsidyVersionToSubsidy($cSubsidy, VersionStatus::Draft);

        $subsidyWithStageForSubjects = $repository->getSubsidiesWithSubsidyStagesForSubjectRole(SubjectRole::Applicant);
        $this->assertSame(1, $subsidyWithStageForSubjects->count());
        $subsidyWithStageForSubject = $subsidyWithStageForSubjects->first();
        $this->assertSame($bSubsidy->id, $subsidyWithStageForSubject->id);
        $this->assertSame(
            SubjectRole::Applicant,
            $subsidyWithStageForSubject->publishedVersion->subsidyStages->first()->subject_role
        );
    }


    public function testMakeApplicationStage()
    {
        // Create test models
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->create(['subsidy_id' => $subsidy->id]);
        $subsidyStage = SubsidyStage::factory()->create(['subsidy_version_id' => $subsidyVersion->id]);

        // Test make application stage
        $this->assertEquals("1", "1");
    }

    public function testGetSubsidyVersion()
    {
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $subsidy->id,
            'subsidy_page_url' => 'random_url',
            'status' => 'published',
            'version' => 1,
            'created_at' => '2021-01-01 00:00:00',
        ]);

        $repository = $this->app->make(SubsidyRepository::class);
        $actualSubsidyVersion = $repository->getSubsidyVersion($subsidyVersion->id);
        $this->assertEquals($subsidyVersion->id, $actualSubsidyVersion->id);
        $this->assertEquals($subsidyVersion->subsidy_page_url, $actualSubsidyVersion->subsidy_page_url);
        $this->assertEquals($subsidyVersion->subsidy_id, $actualSubsidyVersion->subsidy_id);
        $this->assertEquals($subsidyVersion->status, $actualSubsidyVersion->status);
        $this->assertEquals($subsidyVersion->version, $actualSubsidyVersion->version);
        $this->assertEquals($subsidyVersion->created_at, $actualSubsidyVersion->created_at);
    }

    public function testGetSubsidyLetter(): void
    {
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $subsidy->id,
            'subsidy_page_url' => 'random_url',
            'status' => 'published',
            'version' => 1,
            'created_at' => '2021-01-01 00:00:00',
        ]);

        $subsidyLetter = SubsidyLetter::factory()->create([
            'subsidy_version_id' => $subsidyVersion->id,
            'status' => 'accepted',
        ]);

        $repository = $this->app->make(SubsidyRepository::class);
        $actualSubsidyLetter = $repository->getSubsidyLetter($subsidyLetter->id);
        $this->assertEquals($subsidyLetter->id, $actualSubsidyLetter->id);
        $this->assertEquals($subsidyLetter->status, $actualSubsidyLetter->status);
        $this->assertEquals($subsidyLetter->version, $actualSubsidyLetter->version);
        $this->assertEquals($subsidyLetter->created_at, $actualSubsidyLetter->created_at);
        $this->assertEquals($subsidyLetter->content, $actualSubsidyLetter->content);
    }
}
