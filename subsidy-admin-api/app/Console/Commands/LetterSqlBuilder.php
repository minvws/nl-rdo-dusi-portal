<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Console\Commands;

use Illuminate\Console\Command;
use JsonException;

class LetterSqlBuilder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local:build-letter-update-sql {--html} {--pdf}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SQL query to update letter.';

    /**
     * Execute the console command.
     * @throws JsonException
     */
    public function handle(): void
    {
        $needsBuildHtml = $this->option('html');
        $needsBuildPdf = $this->option('pdf');

        if (!$needsBuildHtml && !$needsBuildPdf) {
            $needsBuildHtml = true;
            $needsBuildPdf = true;
        }

        // Example of pdf letter, needs to be replaced with the needed letter
        // Also the query needs to be updated with the correct id

        // phpcs:ignore
        $html = $needsBuildHtml ? file_get_contents(base_path('database/seeders/PCZMv2/resources/letters/letter-approved-view.latte')) : null;
        // phpcs:ignore
        $pdf = $needsBuildPdf ? file_get_contents(base_path('database/seeders/PCZMv2/resources/letters/letter-approved-pdf.latte')) : null;

        $query = implode(PHP_EOL, array_filter([
            "UPDATE public.subsidy_stage_transition_messages",
            $this->getSetQuery(array_filter([
                'content_html' => $html,
                'content_pdf' => $pdf,
                'updated_at' => 'now()',
            ], mode: ARRAY_FILTER_USE_BOTH)),
            "WHERE id = '';",
        ]));

        $this->info($query);
    }

    /**
     * @param array<string, mixed> $data
     * @return string|null
     * @throws JsonException
     */
    protected function getSetQuery(array $data): ?string
    {
        $numberOfItems = count($data);
        if (count($data) === 0) {
            return null;
        }

        $query = "SET ";

        $index = 0;
        foreach ($data as $column => $value) {
            $index++;
            if ($value === null) {
                continue;
            }
            if (is_array($value)) {
                $value = json_encode($value, JSON_THROW_ON_ERROR);
            }
            $value = str_replace("'", "''", $value);

            $query .= "$column = '$value'";
            if ($index !== $numberOfItems) {
                $query .= "," . PHP_EOL;
            }
        }

        return  $query;
    }
}
