<?php

declare(strict_types=1);

namespace MinVWS\Tests\SubsidyModel\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldSource;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Orchestra\Testbench\TestCase;
use function PHPUnit\Framework\assertNotNull;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;


class SubsidyRepositoryTest extends TestCase
{
    use DatabaseMigrations;
//    use RefreshDatabase;
//    protected array $connectionsToTransact = [Connection::FORM];

    public function setUp() :void {
        parent::setUp();

//        parent::setUp();
//        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
//
//        $this->artisan('migrate',
//            ['--database' => 'pgsql_form'])->run();//        $this->runDatabaseMigrations();
//        $this->artisan = $this->app->make('Illuminate\Contracts\Console\Kernel');
//        $this->artisan('migrate')->run();
    }

    protected function getPackageProviders($app)
    {
        return [
            'MinVWS\DUSi\Shared\Subsidy\SubsidyServiceProvider',
        ];
    }

    protected function defineEnvironment($app): void
    {
        // Setup default database to use sqlite :memory:
        tap($app->make('config'), function (Repository $config) {
            $config->set('database.default', 'pgsql_form');
            $config->set('database.connections.pgsql_form', [
                'driver' => 'pgsql',
                'url' => env('DATABASE_FORM_URL'),
                'host' => env('DB_FORM_HOST', '127.0.0.1'),
                'port' => env('DB_FORM_PORT', '5432'),
                'database' => env('DB_FORM_DATABASE', 'forge'),
                'username' => env('DB_FORM_USERNAME', 'forge'),
                'password' => env('DB_FORM_PASSWORD', ''),
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ]);
        });
    }

    public function testGetSubsidy(): void
    {
        $subsidy = Subsidy::factory()->create();
//        dd(config('database'));

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

