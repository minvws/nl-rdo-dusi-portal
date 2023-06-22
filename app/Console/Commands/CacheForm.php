<?php

namespace App\Console\Commands;

use App\Repositories\FormRepository;
use App\Services\CacheService;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;

class CacheForm extends Command
{
    protected $signature = 'cache:form {id}';
    protected $description = 'Caches a single form';

    public function handle(FormRepository $formRepository, CacheService $cacheService): int
    {
        if (!Uuid::isValid($this->argument('id'))) {
            $this->error('Given argument is not a valid UUID!');
            return self::FAILURE;
        }

        $this->info('Retrieving form...');
        $form = $formRepository->getForm($this->argument('id'));

        if ($form === null) {
            $this->error('Form not found or not open!');
            return self::FAILURE;
        }

        $this->newLine();

        $this->info('Caching form...');
        $cacheService->cacheForm($form);

        $this->newLine();

        $this->info('Done caching form');
        return self::SUCCESS;
    }
}
