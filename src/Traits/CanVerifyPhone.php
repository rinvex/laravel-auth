<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Rinvex\Fort\Notifications\PhoneVerificationNotification;

trait CanVerifyPhone
{
    /**
     * Get the phone for verification.
     *
     * @return string
     */
    public function getPhoneForVerification(): string
    {
        return $this->phone;
    }

    /**
     * Get the country for verification.
     *
     * @return string
     */
    public function getCountryForVerification(): string
    {
        return $this->country_code ? country($this->country_code)->getCallingCode() : null;
    }

    /**
     * Determine if phone is verified or not.
     *
     * @return bool
     */
    public function isPhoneVerified(): bool
    {
        return (bool) $this->phone_verified;
    }

    /**
     * Send the phone verification notification.
     *
     * @param string $method
     * @param bool   $force
     *
     * @return void
     */
    public function sendPhoneVerificationNotification($method, $force): void
    {
        $this->notify(new PhoneVerificationNotification($method, $force));
    }
}
