<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Console\Commands;

use Illuminate\Console\Command;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmDecryptionService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;

class TestHsmEncrypt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-hsm-encryption';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test public key encryption with HSM public key and decrypt with HSM';

    /**
     * Execute the console command.
     */
    public function handle(HsmEncryptionService $encryptionService, HsmDecryptionService $decryptionService): void
    {
        $message = $this->ask("Enter message to encrypt with HSM public key");

        $encryptedMessage = $encryptionService->encrypt($message);

        $this->info("Encrypted:");
        $this->info(json_encode($encryptedMessage) ?: '');
        $this->newLine();

        $decryptedMessage = $decryptionService->decrypt($encryptedMessage);
        $this->info("Decrypted with HSM:");
        $this->info($decryptedMessage);
        $this->newLine();

        if ($message !== $decryptedMessage) {
            $this->error("Decrypted message does not match original message");
        } else {
            $this->info("Decrypted message matches original message");
        }
    }
}
