<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Monolog\Logger;
use MinVWS\DUSi\Shared\Application\Services\LetterService;
use MinVWS\DUSi\Shared\Subsidy\Services\SubsidyFileManager;
use MinVWS\Logging\Laravel\Models\AuditLog;
use Monolog\Handler\StreamHandler;
use SplFileObject;

class MigrateAuditLogging extends Command implements PromptsForMissingInput
{
    private Logger $errorLogger;

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
        $filePath = $this->argument('audit-file');
        if (!file_exists($filePath)) {
            $this->error('Audit logging file does not exist');
        }

        $logFilePath = getcwd() . DIRECTORY_SEPARATOR . 'error.log';
        $this->errorLogger = new Logger('errorLogger');
        $this->errorLogger->pushHandler(new StreamHandler($logFilePath, Logger::DEBUG));

        $ourPrivatKey = base64_decode('a/NBbsb96Kl0o7vGCYoWWMnvr+okSTWk7VuYPVF9PmM=');
        $theirPublicKey = base64_decode('q7qP8+ptuihG6fmwH3xXqPqg77Or5J3RG7nda4V3Gms=');

        $decryptionKeypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
            $ourPrivatKey,
            $theirPublicKey
        );

        $countLines = $this->countLines($filePath);
        $bar = $this->output->createProgressBar($countLines);
        $bar->start();

        $file = new SplFileObject($filePath, 'r');

        $successfullMigrations = 0;
        iterator_apply($file, function (array|string|false $line) use (
            $decryptionKeypair,
            $bar,
            &$successfullMigrations
) {
            if ($this->handleLine($line, $decryptionKeypair) === true) {
                $successfullMigrations++;
            }
            $bar->advance();
            return true;
        }, [$file]);

        $bar->finish();

        $this->info('');

        if ($successfullMigrations > 0) {
            $this->info("Successfully migrated $successfullMigrations audit logging lines.");
        }

        if ($successfullMigrations !== $countLines) {
            $this->warn("Errors were reported in $logFilePath.");
        }
    }

    public function handleLine(array|string|false $line, string $decryptionKeypair): bool
    {
        if (!is_string($line) || strpos($line, 'AuditLog:') === false) {
            $this->errorLogger->error(sprintf("No valid AuditLog found: %s", json_encode($line)));
            return false;
        }

        $auditLog = substr($line, strpos($line, 'AuditLog:') + 10);

        $encrypted = base64_decode($auditLog);

        $nonce = substr($encrypted, 0, SODIUM_CRYPTO_BOX_NONCEBYTES);
        $encrypted = substr($encrypted, SODIUM_CRYPTO_BOX_NONCEBYTES);

        $decrypted = sodium_crypto_box_open($encrypted, $nonce, $decryptionKeypair);
        if (!$decrypted) {
            $this->errorLogger->error("failed to decrypt: $line");
            return false;
        }

        $logEvent = json_decode($decrypted, true);

        if (isset($logEvent['request'])) {
            $logEvent['context'] = $logEvent['request'];
            unset($logEvent['request']);
        }

        $auditLog = new AuditLog($logEvent);
        $auditLog->save();

        return true;
    }


    private function countLines(string $filePath): int
    {
        $lineCount = 0;
        $handle = fopen($filePath, "r");

        if ($handle === false) {
            return 0;
        }

        while (!feof($handle)) {
            if (fgets($handle) !== false) {
                $lineCount++;
            }
        }

        fclose($handle);
        return $lineCount;
    }
}
