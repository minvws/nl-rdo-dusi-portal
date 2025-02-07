<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Console\Commands;

use MinVWS\DUSi\Application\API\Services\CacheService;
use Illuminate\Console\Command;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;
use Ramsey\Uuid\Uuid;

class CacheSubsidyStage extends Command
{
    protected $signature = 'cache:subsidy-stage {id}';
    protected $description = 'Caches a single subsidy stage';

    public function handle(SubsidyRepository $subsidyRepository, CacheService $cacheService): int
    {
        if (!Uuid::isValid($this->argument('id'))) {
            $this->error('Given argument is not a valid UUID!');
            return self::FAILURE;
        }

        $this->info('Retrieving subsidy stage...');
        $subsidyStage = $subsidyRepository->getSubsidyStage($this->argument('id'));

        if ($subsidyStage === null) {
            $this->error('Subsidy stage not found or not open!');
            return self::FAILURE;
        }

        $this->newLine();

        $this->info('Caching subsidy stage...');
        $cacheService->cacheSubsidyStage($subsidyStage);

        $this->newLine();

        $this->info('Done caching form');
        return self::SUCCESS;
    }
}
