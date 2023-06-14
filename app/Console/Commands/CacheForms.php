<?php

namespace App\Console\Commands;

use App\Models\Form;
use App\Repositories\FormRepository;
use App\Repositories\SubsidyRepository;
use App\Services\CacheService;
use Illuminate\Console\Command;

class CacheForms extends Command
{
    protected $signature = 'cache:forms';
    protected $description = 'Caches the open forms for the active subsidies';

    public function handle(SubsidyRepository $subsidyRepository, FormRepository $formRepository, CacheService $formCacheService): int
    {
        $this->info('Retrieving subsidies...');
        $activeSubsidies = $subsidyRepository->getActiveSubsidies();

        if (count($activeSubsidies) === 0) {
            $this->error('No active subsidies found!');
            return self::FAILURE;
        }

        $this->newLine();

        foreach ($activeSubsidies as $subsidy) {
            $this->info('Retrieving forms for subsidy "' . $subsidy->title . '"...');
            $forms = $formRepository->getOpenFormsForSubsidy($subsidy);

            if (count($forms) === 0) {
                $this->warn('No active forms found for subsidy!');
                $this->newLine();
                continue;
            }

            if (count($forms) > 0) {
                $this->info('Caching forms for subsidy...');
                $this->withProgressBar($forms, function (Form $form) use ($formCacheService, &$newKeys) {
                    $formCacheService->cacheForm($form);
                });
                $this->newLine();
            }

            $this->newLine();
        }

        $this->info('Done caching forms');
        return self::SUCCESS;
    }
}
