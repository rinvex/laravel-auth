<?php

declare(strict_types=1);

namespace Rinvex\Auth\Contracts;

interface CanVerifyPhoneContract
{
    /**
     * Get the phone for verification.
     *
     * @return string|null
     */
    public function getPhoneForVerification(): ?string;

    /**
     * Get the country for verification.
     *
     * @return string|null
     */
    public function getCountryForVerification(): ?string;

    /**
     * Determine if the user has verified their phone number.
     *
     * @return bool
     */
    public function hasVerifiedPhone(): bool;

    /**
     * Send the phone verification notification.
     *
     * @param string $method
     * @param bool   $force
     *
     * @return void
     */
    public function sendPhoneVerificationNotification(string $method, bool $force): void;
}
