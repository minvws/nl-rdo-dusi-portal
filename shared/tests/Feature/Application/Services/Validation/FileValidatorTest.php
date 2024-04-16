<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Feature\Application\Services\Validation;

use Generator;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\UploadedFile;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use MinVWS\DUSi\Shared\Application\Services\ClamAv\ClamAvService;
use MinVWS\DUSi\Shared\Application\Services\Validation\FileValidator;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Tests\TestCase;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;

/**
 * @group validation
 */
class FileValidatorTest extends TestCase
{
    protected TranslatorContract $translator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = new Translator(new ArrayLoader(), 'nl');
    }

    public function testGetValidator(): void
    {
        $fileValidator = new FileValidator(
            clamAvService: new ClamAvService(enabled: false),
            logger: $this->mock(LoggerInterface::class),
            translator: $this->translator,
        );

        $field = Field::factory()->create();
        $file = $this->mock(UploadedFile::class);

        $this->assertInstanceOf(Validator::class, $fileValidator->getValidator($field, $file));
    }

    public static function mimeTypeProvider(): Generator
    {
        yield 'gif is not accepted' => ['tests/fixtures/images/empty.gif', false];
        yield 'jpeg is accepted' => ['tests/fixtures/images/empty.jpeg', true];
        yield 'jpg is accepted' => ['tests/fixtures/images/empty.jpg', true];
        yield 'png is accepted' => ['tests/fixtures/images/empty.png', true];
        yield 'pdf is accepted' => ['tests/fixtures/test.pdf', true];
    }

    /**
     * @dataProvider mimeTypeProvider
     * @return void
     */
    public function testMimeTypes(string $path, bool $success): void
    {
        $fileValidator = new FileValidator(
            clamAvService: new ClamAvService(enabled: false),
            logger: $this->mock(LoggerInterface::class, function (MockInterface $mock) {
                $mock->shouldReceive('warning')->with('Skipping ClamAV scan because ClamAV is not enabled.');
            }),
            translator: $this->translator,
        );

        $field = Field::factory()->create([
            'params' => [
                'mimeTypes' => ["image/jpeg","image/png","application/pdf"],
            ]
        ]);
        $file = UploadedFile::fake()->createWithContent(basename($path), file_get_contents($path));

        $validator = $fileValidator->getValidator($field, $file);
        $this->assertSame($success, !$validator->fails());
    }
}
