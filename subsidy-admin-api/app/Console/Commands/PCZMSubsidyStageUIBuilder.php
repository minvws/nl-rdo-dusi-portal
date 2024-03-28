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
    protected $signature = 'local:build-pczm-subsidy-stage-ui {--input-ui} {--view-ui}';

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
        $needsBuildViewUi = $this->option('view-ui');

        if (!$needsBuildInputUi && !$needsBuildViewUi) {
            $needsBuildInputUi = true;
            $needsBuildViewUi = true;
        }

        $inputUi = $needsBuildInputUi ? $seeder->buildInputUi() : null;
        $viewUi = $needsBuildViewUi ? $seeder->buildViewUI() : null;

        $query = implode(PHP_EOL, array_filter([
            "UPDATE public.subsidy_stage_uis",
            $this->getSetQuery(array_filter([
                'input_ui' => $inputUi,
                'view_ui' => $viewUi,
            ], mode: ARRAY_FILTER_USE_BOTH)),
            "WHERE id = '" . PCZMApplicationStageUISeeder::PCZM_STAGE1_V1_UUID . "';",
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

        $i = 0;
        foreach ($data as $column => $value) {
            $i++;
            if ($value === null) {
                continue;
            }
            if (is_array($value)) {
                $value = json_encode($value, JSON_THROW_ON_ERROR);
            }
            $value = str_replace("'", "''", $value);

            $query .= "$column = '$value'";
            if ($i !== $numberOfItems) {
                $query .= "," . PHP_EOL;
            }
        }

        return  $query;
    }
}
