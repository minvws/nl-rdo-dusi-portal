<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Console\Commands;

use MinVWS\DUSi\Shared\User\Models\User;
use Illuminate\Console\Command;

class ShowUserInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:show-info {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show various user information';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (!$user) {
            $this->error("User not found");
            return 1;
        }

        $this->info($user->toJson());

        return 0;
    }
}
