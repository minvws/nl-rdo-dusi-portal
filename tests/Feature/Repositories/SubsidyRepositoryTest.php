<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Models\Connection;
use App\Models\Enums\FieldSource;
use App\Models\Enums\FieldType;
use App\Models\Enums\VersionStatus;
use App\Models\Field;
use App\Models\SubsidyStage;
use App\Models\Subsidy;
use App\Models\SubsidyVersion;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

/**
 * @group form
 * @group form-repository
 */
class SubsidyRepositoryTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected array $connectionsToTransact = [Connection::FORM];

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
}
