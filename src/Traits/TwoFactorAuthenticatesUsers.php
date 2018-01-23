<?php

namespace Rinvex\Fort\Traits;

use PragmaRX\Google2FA\Google2FA;
use Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract;

trait TwoFactorAuthenticatesUsers
{
    /**
     * Return login attempt user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|object|null
     */
    public function attemptUser()
    {
        if (! empty($twofactor = $this->session->get('_twofactor'))) {
            return $this->provider->retrieveById($twofactor['user_id']);
        }
    }

    /**
     * Verify TwoFactor authentication.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract $user
     * @param string                                                  $token
     *
     * @return string
     */
    public function attemptTwoFactor(AuthenticatableTwoFactorContract $user, $token): string
    {
        // Verify TwoFactor authentication
        if (request()->session()->has('rinvex.fort.twofactor') && ($this->isValidTwoFactorTotp($user, $token) || $this->isValidTwoFactorBackup($user, $token) || $this->isValidTwoFactorPhone($user, $token))) {
            request()->session()->forget('rinvex.fort.twofactor');

            return static::AUTH_LOGIN;
        }

        // This is NOT login attempt, it's just account update -> phone verification
        if (! request()->session()->has('rinvex.fort.twofactor') && $this->isValidTwoFactorPhone($user, $token)) {
            return 'messages.verification.phone.verified';
        }

        return 'messages.verification.twofactor.invalid_token';
    }

    /**
     * Invalidate given backup code for the given user.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract $user
     * @param                                                         $token
     *
     * @return void
     */
    protected function invalidateTwoFactorBackup(AuthenticatableTwoFactorContract $user, $token): void
    {
        $settings = $user->getTwoFactor();
        $backup = array_get($settings, 'totp.backup');

        unset($backup[array_search($token, $backup)]);

        array_set($settings, 'totp.backup', $backup);

        // Update TwoFactor OTP backup codes
        $user->fill(['two_factor' => $settings])->forceSave();
    }

    /**
     * Determine if the given token is a valid TwoFactor Phone token.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract $user
     * @param                                                         $token
     *
     * @return bool
     */
    protected function isValidTwoFactorPhone(AuthenticatableTwoFactorContract $user, $token): bool
    {
        $settings = $user->getTwoFactor();
        $authyId = array_get($settings, 'phone.authy_id');

        return in_array(mb_strlen($token), [6, 7, 8]) && app('rinvex.authy.token')->verify($token, $authyId)->succeed();
    }

    /**
     * Determine if the given token is a valid TwoFactor Backup code.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract $user
     * @param                                                         $token
     *
     * @return bool
     */
    protected function isValidTwoFactorBackup(AuthenticatableTwoFactorContract $user, $token): bool
    {
        $backup = array_get($user->getTwoFactor(), 'totp.backup', []);
        $result = mb_strlen($token) === 10 && in_array($token, $backup);
        ! $result || $this->invalidateTwoFactorBackup($user, $token);

        return $result;
    }

    /**
     * Determine if the given token is a valid TwoFactor TOTP token.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract $user
     * @param                                                         $token
     *
     * @return bool
     */
    protected function isValidTwoFactorTotp(AuthenticatableTwoFactorContract $user, $token): bool
    {
        $totpProvider = app(Google2FA::class);
        $secret = array_get($user->getTwoFactor(), 'totp.secret');

        return mb_strlen($token) === 6 && request()->session()->get('rinvex.fort.twofactor.totp') && $totpProvider->verifyKey($secret, $token);
    }
}
