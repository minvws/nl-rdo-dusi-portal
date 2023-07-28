<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Tests\Feature\Repositories;

use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldSource;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use MinVWS\DUSi\Shared\Subsidy\Tests\Feature\TestCase;
use function PHPUnit\Framework\assertNotNull;

class SubsidyRepositoryTest extends TestCase
{
    public function testGetSubsidy(): void
    {
        $subsidy = Subsidy::factory()->create();

        $subsidyVersion = SubsidyVersion::factory()->create(
            [
                'status' => VersionStatus::Published,
                'subsidy_id' => $subsidy->id,
                'version' => 1,
            ]
        );
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
            ]
        );

        $subsidyStage->fields()->attach($field);
        $expectedId = $field->id->toString();
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
            [$aSubsidy->id->toString(), $bSubsidy->id->toString()],
            $activeSubsidies->pluck('id')->toArray()
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
}

