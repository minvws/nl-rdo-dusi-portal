<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Console\Commands;

use MinVWS\DUSi\User\Admin\API\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {email} {name} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a regular user';

    /**
     * Create a new command instance.
     *
     * @param TwoFactorAuthenticationProvider $authProvider
     */
    public function __construct(protected TwoFactorAuthenticationProvider $authProvider)
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
        $passwd = $this->argument('password');
        if (!is_string($passwd)) {
            $this->error("Incorrect password");
            return 1;
        }

        $user = User::create([
            "email" => $this->argument('email'),
            "name" => $this->argument('name'),
            "password" => Hash::make($passwd)
        ]);

        $user->forceFill([
            'two_factor_secret' => encrypt($this->authProvider->generateSecretKey()),
            'two_factor_recovery_codes' => null
        ]);
        $user->save();

        $this->info(
            "User created. Please add the following to your authenticator app: \n" .
                $user->twoFactorQrCodeUrl()
        );

        return 0;
    }
}
