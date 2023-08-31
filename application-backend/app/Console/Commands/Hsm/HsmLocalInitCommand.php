<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Console\Commands\Hsm;

use Illuminate\Console\Command;
use MinVWS\DUSi\Application\Backend\Services\Hsm\Exceptions\HsmServiceException;
use MinVWS\DUSi\Application\Backend\Services\Hsm\HsmService;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class HsmLocalInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hsm:local-init {--no-prompts=true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init the local HSM (only for development)';

    public function __construct(
        protected HsmService $service,
        protected string $hsmApiModule,
        protected string $hsmApiSlot,
        protected string $hsmApiEncryptionKeyLabel,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
     */
    public function handle(): void
    {
        if (!$this->moduleAndSlotInConfig()) {
            $this->error('Module and slot must be set in the environment config. Please set HSM_API_MODULE
            and HSM_API_SLOT.');
            return;
        }

        if (!$this->encryptionKeyLabelInConfig()) {
            $this->error('Encryption key label must be set in the environment config. Please set
            HSM_API_ENCRYPTION_KEY_LABEL.');
            return;
        }

        try {
            $listResponse = $this->service->getList();
            if (!isset($listResponse['modules']) || !is_array($listResponse['modules'])) {
                $this->error('HSM does not have any modules. Please check your HSM configuration.');
                return;
            }
            $this->info('HSM connection was successful.');
            $this->newLine();
        } catch (HsmServiceException $e) {
            $this->error('HSM connection was not possible. Last error: ', $e->getMessage());
            return;
        }

        $moduleResponse = $this->service->getModule();
        if (!isset($moduleResponse['slots']) || !is_array($moduleResponse['slots'])) {
            $this->error('HSM does not have any slots in module: ' . ($moduleResponse['module'] ?? '')  .
                '. Please check your HSM configuration.');
            return;
        }
        $this->info('Configured module `' . ($moduleResponse['module'] ?? '') . '` exists and has ' .
            count($moduleResponse['slots']) . ' slot(s).');

        $slotResponse = $this->service->getSlot();
        if (!isset($slotResponse['objects']) || !is_array($slotResponse['objects'])) {
            $this->error('Something went wrong with loading slot information for module: ' .
                ($slotResponse['module'] ?? '') . ', and slot: ' . ($slotResponse['slot'] ?? ''));
            return;
        }

        $this->info('Configured slot `' . ($slotResponse['slot'] ?? '') . '` exists.');
        if (count($slotResponse['objects']) > 0) {
            $tableData = [];
            foreach ($slotResponse['objects'] as $objectType => $objects) {
                foreach ($objects as $object) {
                    $tableData[] = [$objectType, $object['LABEL'] ?? ''];
                }
            }

            $this->newLine();
            $this->table(
                ['Object type(s)', 'Label(s)'],
                $tableData
            );
        } else {
            $this->info('No objects in slot.');
        }


        $publicKeyExists = false;
        $privateKeyExists = false;
        foreach ($slotResponse['objects'] as $objectType => $objects) {
            foreach ($objects as $object) {
                if (($object['LABEL'] ?? '') !== $this->hsmApiEncryptionKeyLabel) {
                    continue;
                }

                if ($objectType === 'PUBLIC_KEY') {
                    $publicKeyExists = true;
                } elseif ($objectType === 'PRIVATE_KEY') {
                    $privateKeyExists = true;
                }
            }
        }



        if (
            $publicKeyExists && $this->confirm('Do you want to remove the public key with label `' .
                $this->hsmApiEncryptionKeyLabel . '`?', false)
        ) {
            $response = $this->service->destroyObjectInSlot(
                objectType: 'PUBLIC_KEY',
                label: $this->hsmApiEncryptionKeyLabel,
            );
            if ($response === false) {
                $this->error('Something went wrong with removing the public key.');
                return;
            }
            $publicKeyExists = false;
            $this->info('Removed public key successfully.');
        }

        if (
            $privateKeyExists && $this->confirm('Do you want to remove the private key with label `' .
                $this->hsmApiEncryptionKeyLabel . '`?', false)
        ) {
            $response = $this->service->destroyObjectInSlot(
                objectType: 'PRIVATE_KEY',
                label: $this->hsmApiEncryptionKeyLabel
            );
            if ($response === false) {
                $this->error('Something went wrong with removing the private key.');
                return;
            }
            $privateKeyExists = false;
            $this->info('Removed private key successfully.');
        }

        if ($privateKeyExists) {
            $privateKeyResponse = $this->service->getObject(
                objectType: 'PRIVATE_KEY',
                label: $this->hsmApiEncryptionKeyLabel,
            );

            $publicKey = $privateKeyResponse['objects'][0]['publickey'] ?? null;
            if (empty($publicKey)) {
                $this->error('Public key of private key is empty.');
                return;
            }

            if (
                !$publicKeyExists && $this->confirm(
                    'Do you want import the public key into the HSM?',
                    true
                )
            ) {
                $this->info('Public key of private key is: ');
                $this->newLine();
                $this->info($publicKey);

                $import = $this->service->importObjectInSlot(
                    objectType: "PUBLIC_KEY",
                    label: $this->hsmApiEncryptionKeyLabel,
                    pem: $publicKey,
                );

                if (!isset($import['objects'][0])) {
                    $this->error('Something went wrong with importing the public key.');
                    return;
                }
                $publicKeyExists = true;
                $this->info('Imported public key successfully.');
            }
        }

        if (!$privateKeyExists && !$publicKeyExists) {
            $this->newLine();
            $this->warn('No public or private key exists with label `' . $this->hsmApiEncryptionKeyLabel .
                ' in slot`.');

            if (
                $this->confirm('Do you want to generate a new RSA key pair with label `' .
                $this->hsmApiEncryptionKeyLabel . '`?', true)
            ) {
                $rsa = $this->service->generateRsa(label: $this->hsmApiEncryptionKeyLabel);

                if (
                    !isset($rsa['result'])
                    || !is_array($rsa['result'])
                    || count($rsa['result']) < 2
                ) {
                    $this->error('Something went wrong with generating RSA key pair.');
                    return;
                }

                $privateKeyExists = true;
                $publicKeyExists = true;
                $this->info('Generated RSA key pair successfully.');
            }
        }



        if (!$publicKeyExists) {
            $this->error('Public key does not exist. Public key is needed for encryption.');
            if ($privateKeyExists) {
                $this->newLine();
                $this->error('Please run this command again and choose to import the public key.');
            }
        }
        if (!$privateKeyExists) {
            $this->error('Private key does not exist. Private key is needed for decryption.');
            if ($publicKeyExists) {
                $this->error('Public key exists. Please run this command again, remove the public key and
                 generate a new RSA key pair.');
            } else {
                $this->newLine();
                $this->error("Please run this command again and generate a new RSA key pair.");
            }
        }

        if ($publicKeyExists && $privateKeyExists) {
            $publicKeyObject = $this->service->getObject(
                objectType: 'PUBLIC_KEY',
                label: $this->hsmApiEncryptionKeyLabel,
            );
            $publicKey = $publicKeyObject['objects'][0]['publickey'] ?? null;


            $privateKeyObject = $this->service->getObject(
                objectType: 'PRIVATE_KEY',
                label: $this->hsmApiEncryptionKeyLabel,
            );

            $privatePublicKey = $privateKeyObject['objects'][0]['publickey'] ?? null;

            if ($publicKey !== $privatePublicKey) {
                $this->error('Public key is not the same as the public key of private key.');
                return;
            }

            try {
                $this->publicKeyToFile($publicKey, env('HSM_PUBLIC_KEY_FILE_PATH'));
            } catch (\Exception $e) {
                $this->error('Could not write public key to file.');
                return;
            }

            $this->info('HSM is ready to use.');
        }
    }

    /**
     * @throws \Exception
     */
    protected function publicKeyToFile(string $key, string $filePath): void
    {
        // Open the file for writing (create if not exists)
        $fileHandle = fopen($filePath, 'w');

        if ($fileHandle) {
            // Write the content to the file
            fwrite($fileHandle, $key);

            // Close the file handle
            fclose($fileHandle);
        } else {
            throw new \Exception('Could not open file for writing.');
        }
    }

    protected function moduleAndSlotInConfig(): bool
    {
        return !empty($this->hsmApiModule) && !empty($this->hsmApiSlot);
    }

    protected function encryptionKeyLabelInConfig(): bool
    {
        return !empty($this->hsmApiEncryptionKeyLabel);
    }

    public function confirm($question, $default = false): bool
    {
        if ($this->option('no-prompts') === 'true') {
            return $default;
        }

        return parent::confirm($question, $default);
    }
}
