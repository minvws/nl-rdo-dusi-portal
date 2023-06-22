<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CacheAll extends Command
{
    protected $signature = 'cache:all';
    protected $description = 'Caches the active subsidies';

    public function handle(): int
    {
        $result = $this->call('cache:subsidies');
        if ($result !== self::SUCCESS) {
            return $result;
        }

        $this->newLine();

        return $this->call('cache:forms');
    }
}
