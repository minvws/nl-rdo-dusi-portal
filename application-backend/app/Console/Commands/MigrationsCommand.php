<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\Application;

class MigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sqlmigration {migration_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate postgresql';

    public function handle(Application $app): void
    {
        $app->singleton('sqlOutputMigrator', function ($app) {
            $repository = $app['migration.repository'];

            return new SqlOutputMigrator($repository, $app['db'], $app['files'], $app['events']);
        });
        $loggingMigrator = $app->make('sqlOutputMigrator');

        $migrationName = $this->argument('migration_name') . '.sql';
        $loggingMigrator->setOutput($this->output);
        $loggingMigrator->migrateToOutputFile($migrationName);
    }
}
