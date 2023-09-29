<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Console\Commands;

use Illuminate\Contracts\Console\PromptsForMissingInput;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\Organisation;
use MinVWS\DUSi\Shared\User\Models\User;
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
    protected $signature = 'admin:create {email} {name} {password} {--secret=}';

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

        $secret = $this->option('secret') ?: $this->authProvider->generateSecretKey();

        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => null,
        ]);
        $user->save();

        $user->attachRole(Role::UserAdmin);

        $this->info("User admin " . $user->email . " created. Please add the following to your authenticator app:");
        $this->info($user->twoFactorQrCodeUrl());
        $this->newLine();

        return 0;
    }
}
