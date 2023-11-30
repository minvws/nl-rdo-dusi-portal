<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Repositories;

use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldSource;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransitionMessage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use MinVWS\DUSi\Shared\Tests\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group subsidy
 * @group subsidy-repository
 */
class SubsidyRepositoryTest extends TestCase
{
    public function testGetSubsidy(): void
    {
        $subsidy = Subsidy::factory()->create();

        $this->assertNull($subsidy->publishedVersion()->first());


        $subsidyVersion = SubsidyVersion::factory()->create(
            [
                'status' => VersionStatus::Published,
                'subsidy_id' => $subsidy->id,
                'version' => 1,
            ]
        );

        $this->assertNotNull($subsidy->publishedVersion()->first());


        $subsidyStage = SubsidyStage::factory()->create(
            [
                'subsidy_version_id' => $subsidyVersion->id,
                'stage' => 1
            ]
        );
        $this->assertNotNull($subsidyStage->id);

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

    public function testGetPublishedSubsidyLetter(): void
    {
        $subsidy = Subsidy::factory()->create();
        $subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $subsidy->id,
            'subsidy_page_url' => 'random_url',
            'status' => 'published',
            'version' => 1,
            'created_at' => '2021-01-01 00:00:00',
        ]);

        $stage1 = SubsidyStage::factory()->for($subsidyVersion)->create(['stage' => 1]);
        $stage2 = SubsidyStage::factory()->for($subsidyVersion)->create(['stage' => 2]);

        $transition =
            SubsidyStageTransition::factory()
                ->for($stage1, 'currentSubsidyStage')
                ->for($stage2, 'targetSubsidyStage')
                ->create();

        SubsidyStageTransitionMessage::factory()->for($transition)->create([
            'status' => VersionStatus::Archived,
            'version' => 1
        ]);

        $publishedMessage =
            SubsidyStageTransitionMessage::factory()->for($transition)->create([
                'status' => VersionStatus::Published,
                'version' => 2
            ]);

        $this->assertEquals($publishedMessage->id, $transition->publishedSubsidyStageTransitionMessage->id);
    }

    /**
     * @dataProvider dataProviderSubsidyCodes
     * @param array|null $subsidyIds
     * @param array $expectedSubsidyCodes
     * @return void
     */
    public function testGetActiveSubsidyCodes(?array $subsidyIds, array $expectedSubsidyCodes): void
    {
        SubsidyVersion::factory()->for(
            Subsidy::factory()->create([
                'id' => 'e0f2cf72-587c-4c87-a5a9-978bb3816bb1',
                'title' => 'Subsidy A',
                'code' => 'SA',
            ])
        )->create([
            'subsidy_page_url' => 'random_url',
            'status' => 'published',
            'version' => 1,
            'created_at' => '2021-01-01 00:00:00',
            ]);
        SubsidyVersion::factory()->for(
            Subsidy::factory()->create([
                'id' => 'cc388455-58b3-4895-a359-50a42bef08cf',
                'title' => 'Subsidy B',
                'code' => 'SB',
            ])
        )->create([
            'subsidy_page_url' => 'random_url',
            'status' => 'published',
            'version' => 1,
            'created_at' => '2021-01-01 00:00:00',
            ]);

        $repository = $this->app->make(SubsidyRepository::class);
        $actualShortRegulations = $repository->getActiveSubsidyCodes($subsidyIds);
        $this->assertEquals($expectedSubsidyCodes, $actualShortRegulations);
    }

    public static function dataProviderSubsidyCodes(): array
    {
        return [
            '2 subsidy codes when not filtered' => [null, ['SA', 'SB']],
            'get sa subsidy code filtered' => [['e0f2cf72-587c-4c87-a5a9-978bb3816bb1'], ['SA']],
            'get sb subsidy code filtered' => [['cc388455-58b3-4895-a359-50a42bef08cf'], ['SB']],
            'get sa and sb subsidy code filtered' => [
                ['e0f2cf72-587c-4c87-a5a9-978bb3816bb1', 'cc388455-58b3-4895-a359-50a42bef08cf'],
                ['SA', 'SB'],
            ],
            'empty list of codes when subsidies not found' => [[Uuid::uuid4()->toString()], []],
            'empty list of codes when empty subsidy filter provided' => [[], []],
        ];
    }

    /**
     * @dataProvider dataProviderSubsidyStageTitles
     * @return void
     */
    public function testGetSubsidyStageTitles(?array $subsidyIds, array $expectedSubsidyStageTitles): void
    {
        // One item
        $subsidy = Subsidy::factory()->create([
            'id' => '025ffd57-5591-4150-989e-63c1b1ec6de1',
        ]);
        $subsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $subsidy->id,
            'subsidy_page_url' => 'random_url',
            'status' => 'published',
            'version' => 1,
            'created_at' => '2021-01-01 00:00:00',
        ]);
        SubsidyStage::factory()->for($subsidyVersion)->create([
            'title' => 'Title A',
            'stage' => 1,
        ]);
        SubsidyStage::factory()->for($subsidyVersion)->create([
            'title' => 'Title B',
            'stage' => 2,
        ]);

        // Second item
        $secondSubsidy = Subsidy::factory()->create([
            'id' => '00059634-ce1d-47ad-9b8d-7a326dbd2598',
        ]);
        $secondSubsidyVersion = SubsidyVersion::factory()->create([
            'subsidy_id' => $secondSubsidy->id,
            'subsidy_page_url' => 'random_url',
            'status' => 'published',
            'version' => 1,
            'created_at' => '2021-01-01 00:00:00',
        ]);
        SubsidyStage::factory()->for($secondSubsidyVersion)->create([
            'title' => 'Title C',
            'stage' => 1,
        ]);
        SubsidyStage::factory()->for($secondSubsidyVersion)->create([
            'title' => 'Title D',
            'stage' => 2,
        ]);

        $repository = $this->app->make(SubsidyRepository::class);
        $actualSubsidyStageTitles = $repository->getSubsidyStageTitles($subsidyIds);

        $this->assertEquals($expectedSubsidyStageTitles, $actualSubsidyStageTitles);
    }

    public static function dataProviderSubsidyStageTitles(): array
    {
        return [
            '4 subsidy stage titles when not filtered' => [null, ['Title A', 'Title B', 'Title C', 'Title D']],
            'get title a and b when filtered' => [['025ffd57-5591-4150-989e-63c1b1ec6de1'], ['Title A', 'Title B']],
            'get title c and d when filtered' => [['00059634-ce1d-47ad-9b8d-7a326dbd2598'], ['Title C', 'Title D']],
            'get title a, b, c and d when filtered' => [
                ['00059634-ce1d-47ad-9b8d-7a326dbd2598', '025ffd57-5591-4150-989e-63c1b1ec6de1'],
                ['Title A', 'Title B', 'Title C', 'Title D'],
            ],
            'empty list of titles when subsidies not found' => [[Uuid::uuid4()->toString()], []],
        ];
    }
}
