<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use JsonException;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZM\PCZMApplicationStageUISeeder;

class PCZMSubsidyStageUIBuilder extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local:build-pczm-subsidy-stage-ui {--input-ui} {--view-schema}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws JsonException
     */
    public function handle(): void
    {
        $seeder = new PCZMApplicationStageUISeeder();

        $needsBuildInputUi = $this->option('input-ui');
        $needsBuildViewSchema = $this->option('view-schema');

        if (!$needsBuildInputUi && !$needsBuildViewSchema) {
            $needsBuildInputUi = true;
            $needsBuildViewSchema = true;
        }

        $inputUi = $needsBuildInputUi ? $seeder->buildInputUi() : null;
        $viewSchema = $needsBuildViewSchema ? $seeder->buildViewSchema() : null;

        $query = implode(PHP_EOL, array_filter([
            "UPDATE public.subsidy_stage_uis",
            implode("," . PHP_EOL, array_filter([
                $this->getSetQuery('input_ui', $inputUi),
                $this->getSetQuery('view_schema', $viewSchema),
            ])),
            "WHERE id = '" . PCZMApplicationStageUISeeder::PCZM_STAGE1_V1_UUID . "';",
        ]));

        $this->info($query);
    }

    protected function getSetQuery(string $column, mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            $value = json_encode($value, JSON_THROW_ON_ERROR);
        }

        return  "SET $column = '$value'";
    }
}
