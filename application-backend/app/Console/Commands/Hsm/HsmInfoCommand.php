<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Console\Commands\Hsm;

use Illuminate\Console\Command;
use MinVWS\DUSi\Application\Backend\Services\Hsm\Exceptions\HsmServiceException;
use MinVWS\DUSi\Application\Backend\Services\Hsm\HsmService;

class HsmInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hsm:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display information about the HSM API connection';

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
     */
    public function handle(): void
    {
        if (!$this->moduleAndSlotInConfig()) {
            $this->error('Module and slot must be set in the environment config. Please set
            HSM_API_MODULE and HSM_API_SLOT.');
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
            throw $e;
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
        if (count($slotResponse['objects']) === 0) {
            $this->error('No objects in slot.');
            $this->error('Create a public and private key with label `' . $this->hsmApiEncryptionKeyLabel .
                '` in slot `' . $this->hsmApiSlot . '`.');
            $this->info('For local development you can run the following artisan command `hsm:local-init`.');
            return;
        }

        $tableData = [];

        $publicKeyExists = false;
        $privateKeyExists = false;

        foreach ($slotResponse['objects'] as $objectType => $objects) {
            foreach ($objects as $object) {
                $tableData[] = [$objectType, $object['LABEL'] ?? ''];
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

        if (!$publicKeyExists || !$privateKeyExists) {
            $this->newLine();
            if (!$publicKeyExists) {
                $this->error('Public key does not exist. Public key is needed for encryption.');
            }

            if (!$privateKeyExists) {
                $this->error('Private key does not exist. Private key is needed for decryption.');
            }
            $this->newLine();

            $this->line('Found the following objects in slot `' . $this->hsmApiSlot . '`:');
            $this->newLine();
            $this->table(
                ['Object type(s)', 'Label(s)'],
                $tableData
            );

            $this->newLine();
            $this->error('HSM is not ready to use.');
            return;
        }

        $this->info('Public and private key with label `' . $this->hsmApiEncryptionKeyLabel .
            '` exists in slot `' . $this->hsmApiSlot . '`.');

        $this->newLine();
        $this->info('HSM is ready to use.');
    }

    protected function moduleAndSlotInConfig(): bool
    {
        return !empty($this->hsmApiModule) && !empty($this->hsmApiSlot);
    }

    protected function encryptionKeyLabelInConfig(): bool
    {
        return !empty($this->hsmApiEncryptionKeyLabel);
    }
}
