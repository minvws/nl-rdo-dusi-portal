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
        $this->info('Retrieving forms...');
        $forms = $formRepository->getActiveForms();

        if (count($forms) === 0) {
            $this->warn('No active forms found!');
        }

        $this->newLine();

        $this->info('Cache form list...');
        $formCacheService->cacheFormList($forms);
        $this->newLine();

        if (count($forms) > 0) {
            $this->info('Cache forms...');
            $this->withProgressBar($forms, function (Form $form) use ($formCacheService, &$newKeys) {
                $formCacheService->cacheForm($form);
            });
            $this->newLine(2);
        }

        $this->info('Finished!');
    }
}
