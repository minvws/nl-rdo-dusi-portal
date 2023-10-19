<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Console\Commands;

use Illuminate\Console\View\Components\Error;
use Illuminate\Console\View\Components\Info;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;

class SqlOutputMigrator extends Migrator
{
    private string $migrationsDir =  __DIR__ . '/../../../../dusi-shared/database/migrations';
    private string $sqlMigrationsDir
        = __DIR__ . '/../../../../dusi-shared/database/sql/dusi_app_db';

    public function __construct(
        MigrationRepositoryInterface $repository,
        Resolver $resolver,
        Filesystem $files,
        Dispatcher $dispatcher = null
    ) {
        parent::__construct($repository, $resolver, $files, $dispatcher);
    }

    /**
     * @throws \Exception
     */
    public function migrateToOutputFile(string $outputFileName): void
    {
        $files = $this->getMigrationFilesAfterCurrentMigration();

        $this->runDownMigrationsUntilCurrentMigration($files);

        \DB::connection()->enableQueryLog();

        $this->runUpMigrations($files);

        $queries = $this->filterOutMigrationQueries(\DB::getRawQueryLog());

        if (empty($queries)) {
            $this->write(Error::class, "No queries to write to SQL migration file.");
            return;
        }

        $this->writeMigrationsFile($outputFileName, $queries);

        $this->updateCurrentMigrationFile(end($files));
    }

    protected function getMigrationFilesAfterCurrentMigration(): array
    {
        $currentMigration = file_get_contents("{$this->migrationsDir}/current_migration.txt");
        $files = scandir($this->migrationsDir, SCANDIR_SORT_ASCENDING);
        if (!$files || !$currentMigration) {
            return [];
        }
        return array_filter($files, function ($file) use ($currentMigration) {
            return str_ends_with($file, '.php') && strcasecmp($file, $currentMigration) > 0;
        });
    }

    protected function runDownMigrationsUntilCurrentMigration(array $files): void
    {
        foreach (array_reverse($files) as $file) {
            $lastRunMigrations = $this->repository->getLast();
            $lastRunMigration = reset($lastRunMigrations);
            $migration = $this->resolvePath($this->migrationsDir . '/' . $file);

            if (!$lastRunMigration || $lastRunMigration->migration != substr($file, 0, -4)) {
                return;
            }

            $this->runMigration($migration, 'down');
            $this->repository->delete($lastRunMigration);
            $this->write(Info::class, "downed {$file}");
        }
    }

    protected function runUpMigrations(array $files): void
    {
        $nextBatchNumber = $this->repository->getNextBatchNumber();

        foreach ($files as $file) {
            $migrationPath = $this->migrationsDir . '/' . $file;
            $this->runMigration($this->resolvePath($migrationPath), 'up');
            $this->repository->log($this->getMigrationName($migrationPath), $nextBatchNumber);
            $this->write(Info::class, "upped {$file}");
        }
    }

    protected function filterOutMigrationQueries(array $queries): array
    {
        $queriesToIgnore = [
            'select * from information_schema.tables',
            'select "migration" from "migrations"',
            'select max("batch") as aggregate from "migrations"',
            'insert into "migrations',
        ];
        return array_filter($queries, function ($query) use ($queriesToIgnore) {
            foreach ($queriesToIgnore as $queryToIgnore) {
                if (str_starts_with($query['raw_query'], $queryToIgnore)) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * @throws \Exception
     */
    protected function writeMigrationsFile(string $outputFileName, array $queries): void
    {
        $dateString = date('Y_m_d_His');
        $filePath = "{$this->sqlMigrationsDir}/{$dateString}_{$outputFileName}";

        $migrationFile = fopen($filePath, 'w');
        if (!$migrationFile) {
            $this->write(Error::class, "Could not open file {$filePath}");
            throw new \Exception("Could not open file {$filePath}");
        }
        foreach ($queries as $query) {
            fwrite($migrationFile, $query['raw_query'] . ";\n\n");
        }
        fclose($migrationFile);

        $this->write(Info::class, "Written " . count($queries) . " queries to {$filePath}");
    }

    /**
     * @throws \Exception
     */
    protected function updateCurrentMigrationFile(string $latestMigrationFile): void
    {
        $currentMigrationFile = fopen("{$this->migrationsDir}/current_migration.txt", "w");
        if (!$currentMigrationFile) {
            $this->write(
                Error::class,
                "Could not open file {$this->migrationsDir}/current_migration.txt"
            );
            throw new \Exception("Could not open file {$this->migrationsDir}/current_migration.txt");
        }
        fwrite($currentMigrationFile, $latestMigrationFile);
        fclose($currentMigrationFile);
    }
}
