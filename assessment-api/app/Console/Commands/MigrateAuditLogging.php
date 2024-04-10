<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use MinVWS\DUSi\Shared\Application\Services\LetterService;
use MinVWS\DUSi\Shared\Subsidy\Services\SubsidyFileManager;
use MinVWS\Logging\Laravel\Models\AuditLog;

class MigrateAuditLogging extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-audit-logging {--their-pub-nacl-key=} {--our-priv-nacl-key=} {--audit-file=}';

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
        $contents = file_get_contents($this->option('audit-file'));
        $lines = explode("\n", $contents); // this is your array of words

        $ourPriv = base64_decode($this->option('our-priv-nacl-key'));
        $theirPub = base64_decode($this->option('their-pub-nacl-key'));

        $encryption_secret = base64_decode('JOJQALLzzLVnr/mPBIzidJs/9KjN4f/wd/Y2euxGQQo=');
        $encryption_public = base64_decode('bHTP2+EeQ1ydVYhBZsc0L1S4ANgtmR6P0FCMmxnTRVw=');
        $decryption_secret = base64_decode('QND4gkBgBopVGGpIqcuuMbIe0n+7HAsw4p6t/R/lwyY=');
        $decryption_public = base64_decode('x8YDbw9sEripm/qilgNLjiKEvmF+4mx3RREMrVzIPQ4=');

        $decryption_keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
            $decryption_secret, $encryption_public);

        foreach($lines as $line) {
            $this->handleLine($line, $decryption_keypair);
        }
    }

    public function handleLine(string $line, $decryption_keypair): void
    {
        $parts = explode(' ', $line);
        if (count($parts) < 7) {
            $this->warn("skipping: $line");
            return;
        }


        $logIndex = array_search('AuditLog:', $parts) + 1;
        $auditLog = $parts[$logIndex];

        $encrypted = base64_decode($auditLog);

        $nonce = substr($encrypted, 0, SODIUM_CRYPTO_BOX_NONCEBYTES);
        $encrypted = substr($encrypted, SODIUM_CRYPTO_BOX_NONCEBYTES);

        $decrypted = sodium_crypto_box_open($encrypted, $nonce, $decryption_keypair);
        if (!$decrypted) {
            $this->warn("failed to decrypt: $line");
            return;
        }
        $logEvent = json_decode($decrypted, true);
//        dd(new AuditLog($logEvent));
//        dd($logEvent);
        if (isset($logEvent['request'])){
            $logEvent['context'] = $logEvent['request'];
            unset($logEvent['request']);
        }
        $auditLog = new AuditLog($logEvent);
        $auditLog->save();
    }
}
