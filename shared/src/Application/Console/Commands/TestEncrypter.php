<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;

class TestEncrypter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-ecrypter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $key = random_bytes(32);

        $encrypter = new Encrypter($key, 'AES-256-CBC');
        $value = $encrypter->encrypt('test');

        $this->info("Encrypted:");
        $this->info($value);

        $this->info("Decrypted:");
        $this->info($encrypter->decrypt($value));
    }
}
