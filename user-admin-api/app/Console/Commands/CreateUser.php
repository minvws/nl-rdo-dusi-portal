<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Console\Commands;

use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Models\Organisation;
use MinVWS\DUSi\Shared\User\Models\User;
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
    protected $signature = 'user:create {email} {name} {password} {role?} {--secret=}';

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

        $organisation = Organisation::query()->first();
        if ($organisation === null) {
            $this->error("No organisation found, please create one first");
            return 1;
        }

        $user = User::updateOrCreate([
            "email" => $this->argument('email'),
            ], [
            "name" => $this->argument('name'),
            "password" => Hash::make($passwd),
            "organisation_id" => Organisation::query()->first()?->id,
        ]);

        $secret = $this->option('secret') ?: $this->authProvider->generateSecretKey();

        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => null,
            'password_updated_at' => now(),
        ]);

        $role = $this->getRoleFromArgument($this->argument('role'));
        if ($role !== null) {
            $user->attachRole($role);
        }

        $user->save();

        $this->info("User " . $user->email . " created. Please add the following to your authenticator app:");
        $this->info($user->twoFactorQrCodeUrl());
        $this->newLine();

        return 0;
    }

    private function getRoleFromArgument(mixed $roleArgument): ?Role
    {
        if (!is_string($roleArgument)) {
            return null;
        }

        return Role::tryFrom($roleArgument);
    }
}
