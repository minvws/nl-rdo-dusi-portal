<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Repositories;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Application\Backend\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;

/**
 * @group application
 * @group application-file-repository
 */
class ApplicationFileRepositoryTest extends TestCase
{
    use WithFaker;

    public function testUnlinkUnusedFiles(): void
    {
        $fileSystem = Storage::fake('files');
        $repository = new ApplicationFileRepository($fileSystem);

        $applicationStage = new ApplicationStage();
        $applicationStage->id = $this->faker->uuid();

        $field = new Field();
        $field->code = $this->faker->word;

        $allIds = [$this->faker->uuid(), $this->faker->uuid(), $this->faker->uuid(), $this->faker->uuid()];

        foreach ($allIds as $id) {
            $repository->writeFile($applicationStage, $field, $id, $id);
        }

        $usedIds = array_slice($allIds, 0, 2);
        $repository->unlinkUnusedFiles($applicationStage, $field, $usedIds);

        foreach ($allIds as $id) {
            $exists = $repository->fileExists($applicationStage, $field, $id);
            $this->assertEquals(in_array($id, $usedIds), $exists);
        }
    }
}
