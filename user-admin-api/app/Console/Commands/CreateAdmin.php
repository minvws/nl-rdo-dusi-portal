<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Console\Commands;

use Illuminate\Contracts\Console\PromptsForMissingInput;
use MinVWS\DUSi\User\Admin\API\Models\Organisation;
use MinVWS\DUSi\User\Admin\API\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

class CreateAdmin extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {email} {name} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the initial admin user';

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
        /** @var User $user */
        $user = User::updateOrCreate([
            "email" => $this->argument('email'),
        ], [
            "name" => $this->argument('name'),
            "password" => Hash::make($this->argument('password')),
            "organisation_id" => Organisation::query()->first()?->id,
        ]);

        $user->forceFill([
            'two_factor_secret' => encrypt($this->authProvider->generateSecretKey()),
            'two_factor_recovery_codes' => null,
        ]);
        $user->save();

        $user->attachRole('admin');

        $this->info(
            "Admin user created. Please add the following to your authenticator app: \n" .
            $user->twoFactorQrCodeUrl()
        );

        return 0;
    }
}
