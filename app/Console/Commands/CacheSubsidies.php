<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;
use MinVWS\DUSi\Shared\Subsidy\Repositories\SubsidyRepository;

class CacheSubsidies extends Command
{
    protected $signature = 'cache:subsidies';
    protected $description = 'Caches the active subsidies';

    public function handle(SubsidyRepository $subsidyRepository, CacheService $cacheService): int
    {
        $this->info('Retrieving subsidies...');
        $activeSubsidies = $subsidyRepository->getActiveSubsidies();

        if (count($activeSubsidies) === 0) {
            $this->error('No active subsidies found!');
            return self::FAILURE;
        }

        $this->info('Caching active subsidies...');
        $cacheService->cacheActiveSubsidies($activeSubsidies);

        $this->info('Done caching active subsidies');
        return self::SUCCESS;
    }
}
