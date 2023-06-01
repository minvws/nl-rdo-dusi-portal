<?php

namespace App\Console\Commands;

use App\Repositories\FormRepository;
use App\Services\FormCacheService;
use Illuminate\Console\Command;

class CacheForm extends Command
{
    protected $signature = 'cache:form {id}';
    protected $description = 'Caches the active forms list and schemas';

    public function handle(FormRepository $formRepository, FormCacheService $formCacheService): int
    {
        $this->info('Retrieving form...');
        $form = $formRepository->getForm($this->argument('id'));

        if ($form === null) {
            $this->error('Form not found!');
            return self::FAILURE;
        }

        $this->newLine();

        $this->info('Caching form...');
        $formCacheService->cacheForm($form);

        $this->newLine();

        $this->info('Finished!');
        return self::SUCCESS;
    }
}
