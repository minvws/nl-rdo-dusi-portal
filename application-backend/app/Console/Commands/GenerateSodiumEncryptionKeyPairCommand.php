<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Console\Commands;

use Illuminate\Console\Command;

use function sodium_crypto_box_keypair;
use function sodium_crypto_box_publickey;
use function sodium_crypto_box_secretkey;

class GenerateSodiumEncryptionKeyPairCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sodium:generate-key-pair';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sodium keypair';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $keypair = sodium_crypto_box_keypair();

        $this->info('Sodium keypair generated');
        $this->info("Public:" . base64_encode(sodium_crypto_box_publickey($keypair)));
        $this->newLine();
        $this->info("Private:" . base64_encode(sodium_crypto_box_secretkey($keypair)));
    }
}
