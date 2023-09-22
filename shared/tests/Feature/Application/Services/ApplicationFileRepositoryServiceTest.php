<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Application\Services;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;
use MinVWS\DUSi\Shared\Application\Models\Submission\File;
use MinVWS\DUSi\Shared\Application\Models\Submission\FileList;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationFileRepository;
use MinVWS\DUSi\Shared\Application\Services\AesEncryption\ApplicationFileEncryptionService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileRepositoryService;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Tests\TestCase;
use MinVWS\DUSi\Shared\Tests\Trait\HsmEncryptionMockTrait;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * @group application
 * @group application-file-repository
 */
class ApplicationFileRepositoryServiceTest extends TestCase
{
    use WithFaker;
    use MockeryPHPUnitIntegration;
    use HsmEncryptionMockTrait;

    private ApplicationFileEncryptionService $applicationFileEncryptionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->applicationFileEncryptionService = new ApplicationFileEncryptionService(
            hsmEncryptionService: $this->getHsmEncryptionServiceMock(),
            hsmDecryptionService: $this->getHsmDecryptionServiceMock(),
            jsonDecoder: new JSONDecoder(),
            jsonEncoder: new JSONEncoder(),
        );
    }

    public function testUnlinkUnusedFiles(): void
    {
        $fileSystem = Storage::fake('files');
        $repository = new ApplicationFileRepositoryService($this->applicationFileEncryptionService, new ApplicationFileRepository($fileSystem));

        $applicationStage = new ApplicationStage();
        $applicationStage->id = $this->faker->uuid();

        $field = new Field();
        $field->type = FieldType::Upload;
        $field->code = $this->faker->word;

        $allIds = [$this->faker->uuid(), $this->faker->uuid(), $this->faker->uuid(), $this->faker->uuid()];

        foreach ($allIds as $id) {
            $repository->writeFile($applicationStage, $field, $id, $id);
        }

        $fieldValue = new FieldValue($field, new FileList([
            new File($allIds[0], null, null),
            new File($allIds[1], null, null),
        ]));
        $usedIds = [$allIds[0], $allIds[1]];

        $repository->cleanUpUnusedFiles($applicationStage, $fieldValue);

        foreach ($allIds as $id) {
            $exists = $repository->fileExists($applicationStage, $field, $id);
            $this->assertEquals(in_array($id, $usedIds), $exists);
        }
    }
}
