<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use MinVWS\DUSi\Shared\Application\Services\LetterService;
use MinVWS\DUSi\Shared\Subsidy\Services\SubsidyFileManager;
use MinVWS\Logging\Laravel\Models\AuditLog;
use SplFileObject;

class MigrateAuditLogging extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-audit-logging {--their-pub-nacl-key=} {--our-priv-nacl-key=} {audit-file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate the audit logging form file the configured logging';

    public function __construct(protected LetterService $letterService, protected SubsidyFileManager $fileManager)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (!file_exists($this->argument('audit-file'))) {
            $this->error('Audit logging file does not exist');
        }

//        $ourPrivatKey = base64_decode($this->option('our-priv-nacl-key'));
//        $theirPublicKey = base64_decode($this->option('their-pub-nacl-key'));

        $ourPrivatKey = base64_decode('a/NBbsb96Kl0o7vGCYoWWMnvr+okSTWk7VuYPVF9PmM=');
        $theirPublicKey = base64_decode('q7qP8+ptuihG6fmwH3xXqPqg77Or5J3RG7nda4V3Gms=');

        $decryption_keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
            $ourPrivatKey, $theirPublicKey);

        $file = new SplFileObject($this->argument('audit-file'), 'r');

        foreach ($file as $line) {
            $this->handleLine($line, $decryption_keypair);
        }
    }

    public function handleLine(string $line, $decryption_keypair): void
    {
        if (strpos($line, 'AuditLog:') === false) {
            $this->warn("No AuditLog found: $line");
            return;
        }

        $auditLog = substr($line, strpos($line, 'AuditLog:') + 10);

        $encrypted = base64_decode($auditLog);

        $nonce = substr($encrypted, 0, SODIUM_CRYPTO_BOX_NONCEBYTES);
        $encrypted = substr($encrypted, SODIUM_CRYPTO_BOX_NONCEBYTES);

        $decrypted = sodium_crypto_box_open($encrypted, $nonce, $decryption_keypair);
        if (!$decrypted) {
            $this->warn("failed to decrypt: $line");
            return;
        }

        $logEvent = json_decode($decrypted, true);

        if (isset($logEvent['request'])){
            $logEvent['context'] = $logEvent['request'];
            unset($logEvent['request']);
        }

        $auditLog = new AuditLog($logEvent);
        $auditLog->save();
    }
}
