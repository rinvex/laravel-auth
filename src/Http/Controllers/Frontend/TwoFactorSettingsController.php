<?php

/*
 * NOTICE OF LICENSE
 *
 * Part of the Rinvex Fort Package.
 *
 * This source file is subject to The MIT License (MIT)
 * that is bundled with this package in the LICENSE file.
 *
 * Package: Rinvex Fort Package
 * License: The MIT License (MIT)
 * Link:    https://rinvex.com
 */

namespace Rinvex\Fort\Http\Controllers\Frontend;

use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Rinvex\Fort\Services\TwoFactorTotpProvider;
use Rinvex\Fort\Http\Controllers\AuthenticatedController;
use Rinvex\Fort\Http\Requests\Frontend\TwoFactorTotpUpdateRequest;
use Rinvex\Fort\Http\Requests\Frontend\TwoFactorPhoneUpdateRequest;

class TwoFactorSettingsController extends AuthenticatedController
{
    /**
     * Show the Two-Factor TOTP enable form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\TwoFactorTotpUpdateRequest $request
     * @param \Rinvex\Fort\Services\TwoFactorTotpProvider                    $totpProvider
     *
     * @return \Illuminate\Http\Response
     */
    public function enableTotp(TwoFactorTotpUpdateRequest $request, TwoFactorTotpProvider $totpProvider)
    {
        $currentUser = $request->user($this->getGuard());
        $settings = $currentUser->getTwoFactor();

        if (array_get($settings, 'totp.enabled') && ! session()->get('success') && ! session()->get('errors')) {
            $messageBag = new MessageBag([trans('rinvex/fort::messages.verification.twofactor.totp.already')]);
            $errors = (new ViewErrorBag())->put('default', $messageBag);
        }

        if (! $secret = array_get($settings, 'totp.secret')) {
            array_set($settings, 'totp.enabled', false);
            array_set($settings, 'totp.secret', $secret = $totpProvider->generateSecretKey());

            $currentUser->update([
                'two_factor' => $settings,
            ]);
        }

        $qrCode = $totpProvider->getQRCodeInline(config('rinvex.fort.twofactor.issuer'), $currentUser->email, $secret);

        return view('rinvex/fort::frontend/user.twofactor', compact('secret', 'qrCode', 'settings', 'errors'));
    }

    /**
     * Process the Two-Factor TOTP enable form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\TwoFactorTotpUpdateRequest $request
     * @param \Rinvex\Fort\Services\TwoFactorTotpProvider                    $totpProvider
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateTotp(TwoFactorTotpUpdateRequest $request, TwoFactorTotpProvider $totpProvider)
    {
        $currentUser = $request->user($this->getGuard());
        $settings = $currentUser->getTwoFactor();
        $secret = array_get($settings, 'totp.secret');
        $backup = array_get($settings, 'totp.backup');
        $backupAt = array_get($settings, 'totp.backup_at');

        if ($totpProvider->verifyKey($secret, $request->get('token'))) {
            array_set($settings, 'totp.enabled', true);
            array_set($settings, 'totp.secret', $secret);
            array_set($settings, 'totp.backup', $backup ?: $this->generateTotpBackups());
            array_set($settings, 'totp.backup_at', $backupAt ?: (new Carbon())->toDateTimeString());

            // Update Two-Factor settings
            $currentUser->update([
                'two_factor' => $settings,
            ]);

            return intend([
                'back' => true,
                'with' => ['success' => trans('rinvex/fort::messages.verification.twofactor.totp.enabled')],
            ]);
        }

        return intend([
            'back'       => true,
            'withErrors' => ['token' => trans('rinvex/fort::messages.verification.twofactor.totp.invalid_token')],
        ]);
    }

    /**
     * Process the Two-Factor TOTP disable.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\TwoFactorTotpUpdateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function disableTotp(TwoFactorTotpUpdateRequest $request)
    {
        $currentUser = $request->user($this->getGuard());
        $settings = $currentUser->getTwoFactor();

        array_set($settings, 'totp', []);

        $currentUser->update([
            'two_factor' => $settings,
        ]);

        return intend([
            'route' => 'rinvex.fort.frontend.user.settings',
            'with'  => ['success' => trans('rinvex/fort::messages.verification.twofactor.totp.disabled')],
        ]);
    }

    /**
     * Process the Two-Factor Phone enable.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\TwoFactorPhoneUpdateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function enablePhone(TwoFactorPhoneUpdateRequest $request)
    {
        $currentUser = $request->user($this->getGuard());

        if (! $currentUser->phone || ! $currentUser->phone_verified) {
            return intend([
                'route'      => 'rinvex.fort.frontend.user.settings',
                'withErrors' => ['phone' => trans('rinvex/fort::messages.account.phone_required')],
            ]);
        }

        $settings = $currentUser->getTwoFactor();

        array_set($settings, 'phone.enabled', true);

        $currentUser->update([
            'two_factor' => $settings,
        ]);

        return intend([
            'route' => 'rinvex.fort.frontend.user.settings',
            'with'  => ['success' => trans('rinvex/fort::messages.verification.twofactor.phone.enabled')],
        ]);
    }

    /**
     * Process the Two-Factor Phone disable.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\TwoFactorPhoneUpdateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function disablePhone(TwoFactorPhoneUpdateRequest $request)
    {
        $currentUser = $request->user($this->getGuard());
        $settings = $currentUser->getTwoFactor();

        array_set($settings, 'phone.enabled', false);

        $currentUser->update([
            'two_factor' => $settings,
        ]);

        return intend([
            'route' => 'rinvex.fort.frontend.user.settings',
            'with'  => ['success' => trans('rinvex/fort::messages.verification.twofactor.phone.disabled')],
        ]);
    }

    /**
     * Process the Two-Factor OTP backup.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\TwoFactorTotpUpdateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function backupTotp(TwoFactorTotpUpdateRequest $request)
    {
        $currentUser = $request->user($this->getGuard());
        $settings = $currentUser->getTwoFactor();

        if (! array_get($settings, 'totp.enabled')) {
            return intend([
                'route'      => 'rinvex.fort.frontend.user.settings',
                'withErrors' => ['rinvex.fort.verification.twofactor.totp.cant_backup' => trans('rinvex/fort::messages.verification.twofactor.totp.cant_backup')],
            ]);
        }

        array_set($settings, 'totp.backup', $this->generateTotpBackups());
        array_set($settings, 'totp.backup_at', (new Carbon())->toDateTimeString());

        $currentUser->update([
            'two_factor' => $settings,
        ]);

        return intend([
            'back' => true,
            'with' => ['success' => trans('rinvex/fort::messages.verification.twofactor.totp.rebackup')],
        ]);
    }

    /**
     * Generate Two-Factor OTP backup codes.
     *
     * @return array
     */
    protected function generateTotpBackups()
    {
        $backup = [];

        for ($x = 0; $x <= 9; $x++) {
            $backup[] = str_pad(random_int(0, 9999999999), 10, 0, STR_PAD_BOTH);
        }

        return $backup;
    }
}
