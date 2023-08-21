<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Console\Commands;

use MinVWS\DUSi\User\Admin\API\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

class UserOtpToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:otp {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show OTP token for specified user';

    /**
     * Execute the console command.
     *
     * @param Google2FA $google2fa
     * @return int
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function handle(Google2FA $google2fa): int
    {
        if (config('app.env') !== 'local') {
            $this->error('This command is only available in local environment');
            return self::FAILURE;
        }

        $email = $this->argument('email');
        if (!$email) {
            if (User::count() === 0) {
                $this->error('No users found');
                return self::FAILURE;
            }

            $email = $this->choice(
                'Which user do you want to get the OTP token for?',
                DB::table('users')->pluck('email')->toArray()
            );
        }

        $user = User::whereEmail($email)->first();
        if (!$user) {
            $this->error('User not found');
            return self::FAILURE;
        }

        $this->info('The OTP token for ' . $user->email . ' is:');
        $this->info($this->getOtpForUser($google2fa, $user));

        return self::SUCCESS;
    }

    public function getOtpForUser(Google2FA $google2fa, User $user): string
    {
        $twoFactorSecret = decrypt($user->two_factor_secret ?? '');
        if (!is_string($twoFactorSecret)) {
            throw new \RuntimeException('Could not decrypt two factor secret');
        }

        try {
            return $google2fa->getCurrentOtp($twoFactorSecret);
        } catch (
            IncompatibleWithGoogleAuthenticatorException
            | SecretKeyTooShortException
            | InvalidCharactersException $e
        ) {
            throw new \RuntimeException('Invalid two factor secret', 0, $e);
        }
    }
}
