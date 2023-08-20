<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Console\Commands;

use MinVWS\DUSi\User\Admin\API\Models\User;
use Illuminate\Console\Command;

class RemoveUserByEmail extends Command
{
    protected $signature = 'user:remove {email : The email of the user to be removed}';

    protected $description = 'Remove a user by email';

    public function handle(): void
    {
        $email = $this->argument('email');

        // Find the user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return;
        }

        // Delete the user
        $user->delete();

        $this->info("User '{$user->name}' with email '{$email}' has been removed.");
    }
}
