<?php

namespace App\Console\Commands;

use App\Models\Form;
use App\Repositories\FormRepository;
use App\Services\FormCacheService;
use Illuminate\Console\Command;

class CacheForms extends Command
{
    protected $signature = 'cache:forms';
    protected $description = 'Caches the active forms list and schemas';

    public function handle(FormRepository $formRepository, FormCacheService $formCacheService): void
    {
        $oldKeys = $formCacheService->getKeys();
        $newKeys = [];

        $this->info('Retrieving forms...');
        $forms = $formRepository->getActiveForms();

        if (count($forms) === 0) {
            $this->warn('No active forms found!');
        }

        $this->newLine();

        $this->info('Cache form list...');
        $newKeys[] = $formCacheService->cacheFormList($forms);
        $this->newLine();

        if (count($forms) > 0) {
            $this->info('Cache forms...');
            $this->withProgressBar($forms, function (Form $form) use ($formCacheService, &$newKeys) {
                $newKeys[] = $formCacheService->cacheForm($form);
            });
            $this->newLine(2);
        }

        $obsoleteKeys = array_diff($oldKeys, $newKeys);
        if (count($obsoleteKeys) > 0) {
            $this->info('Purge obsolete forms...');
            $this->withProgressBar($obsoleteKeys, function (string $obsoleteKey) use ($formCacheService) {
                $formCacheService->purge($obsoleteKey);
            });
            $this->newLine(2);
        }

        $this->info('Finished!');
    }
}
