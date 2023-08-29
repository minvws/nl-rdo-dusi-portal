<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Console\Commands;

use MinVWS\DUSi\User\Admin\API\Models\Organisation;
use Illuminate\Console\Command;

class CreateOrganisation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'organisation:create {organisation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the initial organisation';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $organisationName = $this->argument('organisation');
        if (!is_string($organisationName)) {
            $this->error("Incorrect organisation");
            return 1;
        }

        $organisation = Organisation::create([
            "name" => $organisationName,
        ]);

        $this->info("Organisation created. Id: {$organisation->id}");

        return 0;
    }
}
