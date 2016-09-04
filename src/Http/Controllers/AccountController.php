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

namespace Rinvex\Fort\Http\Controllers;

use Carbon\Carbon;
use Rinvex\Country\Models\Country;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Facades\Lang;
use Rinvex\Fort\Http\Requests\AccountUpdate;
use Rinvex\Fort\Http\Requests\TwoFactorTotp;
use Rinvex\Fort\Http\Requests\TwoFactorPhone;
use Rinvex\Fort\Services\TwoFactorTotpProvider;
use Rinvex\Fort\Contracts\UserRepositoryContract;

class AccountController extends FoundationController
{
    /**
     * The users repository instance.
     *
     * @var \Rinvex\Fort\Contracts\UserRepositoryContract
     */
    protected $users;

    /**
     * Create a new account controller instance.
     *
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $users
     *
     * @return void
     */
    public function __construct(UserRepositoryContract $users)
    {
        $this->users = $users;

        $this->middleware($this->getAuthMiddleware(), ['except' => $this->middlewareWhitelist]);
    }

    /**
     * Show the account update form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAccountUpdate(Country $country)
    {
        $twoFactor = $this->currentUser()->getTwoFactor();
        $countries = $country->findAll()->pluck('name.common', 'iso_3166_1_alpha2');

        return view('rinvex.fort::account.page', compact('twoFactor', 'countries'));
    }

    /**
     * Process the account update form.
     *
     * @param \Rinvex\Fort\Http\Requests\AccountUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processAccountUpdate(AccountUpdate $request)
    {
        $currentUser = $this->currentUser();
        $data        = $request->except(['_token', 'id']);
        $twoFactor   = $currentUser->getTwoFactor();

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $emailVerification = $data['email'] != $currentUser->email ? [
            'email_verified'    => false,
            'email_verified_at' => null,
        ] : [];

        $phoneVerification = $data['phone'] != $currentUser->phone ? [
            'phone_verified'    => false,
            'phone_verified_at' => null,
        ] : [];

        $countryVerification = $data['country'] !== $currentUser->country;

        if ($phoneVerification || $countryVerification) {
            array_set($twoFactor, 'phone.enabled', false);
        }

        $this->users->update($request->get('id'), $data + $emailVerification + $phoneVerification + $twoFactor);

        return intend([
            'back' => true,
            'with' => [
                          'rinvex.fort.alert.success' => Lang::get('rinvex.fort::message.account.'.(! empty($emailVerification) ? 'reverify' : 'updated')),
                      ] + ($twoFactor !== $currentUser->getTwoFactor() ? ['rinvex.fort.alert.warning' => Lang::get('rinvex.fort::message.verification.twofactor.phone.auto_disabled')] : []),
        ]);
    }

    /**
     * Show the account sessions.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAccountSessions()
    {
        return view('rinvex.fort::account.sessions');
    }

    /**
     * Flush the given session.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processSessionFlush($token = null)
    {
        $status = '';

        if ($token) {
            app('rinvex.fort.persistence')->delete($token);
            $status = Lang::get('rinvex.fort::message.auth.session.flushed');
        } elseif (request()->get('confirm')) {
            app('rinvex.fort.persistence')->deleteByUser($this->currentUser()->id);
            $status = Lang::get('rinvex.fort::message.auth.session.flushedall');
        }

        return intend([
            'back' => true,
            'with' => ['rinvex.fort.alert.warning' => $status],
        ]);
    }

    /**
     * Show the Two-Factor TOTP enable form.
     *
     * @param \Rinvex\Fort\Http\Requests\TwoFactorTotp    $request
     * @param \Rinvex\Fort\Services\TwoFactorTotpProvider $totpProvider
     *
     * @return \Illuminate\Http\Response
     */
    public function showTwoFactorTotpEnable(TwoFactorTotp $request, TwoFactorTotpProvider $totpProvider)
    {
        $currentUser = $this->currentUser();
        $settings    = $currentUser->getTwoFactor();

        if (array_get($settings, 'totp.enabled') && ! session()->get('rinvex.fort.alert.success') && ! session()->get('errors')) {
            $messageBag = new MessageBag([Lang::get('rinvex.fort::message.verification.twofactor.totp.already')]);
            $errors     = (new ViewErrorBag())->put('default', $messageBag);
        }

        if (! $secret = array_get($settings, 'totp.secret')) {
            array_set($settings, 'totp.enabled', false);
            array_set($settings, 'totp.secret', $secret = $totpProvider->generateSecretKey());

            $this->users->update($currentUser->id, [
                'two_factor' => $settings,
            ]);
        }

        $qrCode = $totpProvider->getQRCodeInline(config('rinvex.fort.twofactor.issuer'), $currentUser->email, $secret);

        return view('rinvex.fort::account.twofactor', compact('secret', 'qrCode', 'settings', 'errors'));
    }

    /**
     * Process the Two-Factor TOTP enable form.
     *
     * @param \Rinvex\Fort\Http\Requests\TwoFactorTotp    $request
     * @param \Rinvex\Fort\Services\TwoFactorTotpProvider $totpProvider
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processTwoFactorTotpEnable(TwoFactorTotp $request, TwoFactorTotpProvider $totpProvider)
    {
        $currentUser = $this->currentUser();
        $settings    = $currentUser->getTwoFactor();
        $secret      = array_get($settings, 'totp.secret');
        $backup      = array_get($settings, 'totp.backup');
        $backupAt    = array_get($settings, 'totp.backup_at');

        if ($totpProvider->verifyKey($secret, $request->get('token'))) {
            array_set($settings, 'totp.enabled', true);
            array_set($settings, 'totp.secret', $secret);
            array_set($settings, 'totp.backup', $backup ?: $this->generateTwoFactorTotpBackups());
            array_set($settings, 'totp.backup_at', $backupAt ?: (new Carbon())->toDateTimeString());

            // Update Two-Factor settings
            $this->users->update($currentUser->id, [
                'two_factor' => $settings,
            ]);

            return intend([
                'back' => true,
                'with' => ['rinvex.fort.alert.success' => Lang::get('rinvex.fort::message.verification.twofactor.totp.enabled')],
            ]);
        }

        return intend([
            'back'       => true,
            'withErrors' => ['token' => Lang::get('rinvex.fort::message.verification.twofactor.totp.invalid_token')],
        ]);
    }

    /**
     * Process the Two-Factor TOTP disable.
     *
     * @param \Rinvex\Fort\Http\Requests\TwoFactorTotp $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processTwoFactorTotpDisable(TwoFactorTotp $request)
    {
        $currentUser = $this->currentUser();
        $settings    = $currentUser->getTwoFactor();

        array_set($settings, 'totp', []);

        $this->users->update($currentUser->id, [
            'two_factor' => $settings,
        ]);

        return intend([
            'intended' => route('rinvex.fort.account.page'),
            'with'     => ['rinvex.fort.alert.success' => Lang::get('rinvex.fort::message.verification.twofactor.totp.disabled')],
        ]);
    }

    /**
     * Process the Two-Factor Phone enable.
     *
     * @param \Rinvex\Fort\Http\Requests\TwoFactorPhone $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processTwoFactorPhoneEnable(TwoFactorPhone $request)
    {
        $currentUser = $this->currentUser();

        if (! $currentUser->phone || ! $currentUser->phone_verified) {
            return intend([
                'intended'   => route('rinvex.fort.account.page'),
                'withErrors' => ['phone' => Lang::get('rinvex.fort::message.account.phone_required')],
            ]);
        }

        $settings = $this->currentUser()->getTwoFactor();

        array_set($settings, 'phone.enabled', true);

        $this->users->update($currentUser->id, [
            'two_factor' => $settings,
        ]);

        return intend([
            'intended' => route('rinvex.fort.account.page'),
            'with'     => ['rinvex.fort.alert.success' => Lang::get('rinvex.fort::message.verification.twofactor.phone.enabled')],
        ]);
    }

    /**
     * Process the Two-Factor Phone disable.
     *
     * @param \Rinvex\Fort\Http\Requests\TwoFactorPhone $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processTwoFactorPhoneDisable(TwoFactorPhone $request)
    {
        $currentUser = $this->currentUser();
        $settings    = $currentUser->getTwoFactor();

        array_set($settings, 'phone.enabled', false);

        $this->users->update($currentUser->id, [
            'two_factor' => $settings,
        ]);

        return intend([
            'intended' => route('rinvex.fort.account.page'),
            'with'     => ['rinvex.fort.alert.success' => Lang::get('rinvex.fort::message.verification.twofactor.phone.disabled')],
        ]);
    }

    /**
     * Process the Two-Factor OTP backup.
     *
     * @param \Rinvex\Fort\Http\Requests\TwoFactorTotp $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processTwoFactorTotpBackup(TwoFactorTotp $request)
    {
        $currentUser = $this->currentUser();
        $settings    = $currentUser->getTwoFactor();

        if (! array_get($settings, 'totp.enabled')) {
            return intend([
                'intended'   => route('rinvex.fort.account.page'),
                'withErrors' => ['rinvex.fort.verification.twofactor.totp.cant_backup' => Lang::get('rinvex.fort::message.verification.twofactor.totp.cant_backup')],
            ]);
        }

        array_set($settings, 'totp.backup', $this->generateTwoFactorTotpBackups());
        array_set($settings, 'totp.backup_at', (new Carbon())->toDateTimeString());

        $this->users->update($currentUser->id, [
            'two_factor' => $settings,
        ]);

        return intend([
            'back' => true,
            'with' => ['rinvex.fort.alert.success' => Lang::get('rinvex.fort::message.verification.twofactor.totp.rebackup')],
        ]);
    }

    /**
     * Generate Two-Factor OTP backup codes.
     *
     * @return array
     */
    protected function generateTwoFactorTotpBackups()
    {
        $backup = [];

        for ($x = 0; $x <= 9; $x++) {
            $backup[] = str_pad(random_int(0, 9999999999), 10, 0, STR_PAD_BOTH);
        }

        return $backup;
    }

    /**
     * Get current user.
     *
     * @return \Rinvex\Fort\Contracts\AuthenticatableContract
     */
    protected function currentUser()
    {
        return Auth::guard($this->getGuard())->user();
    }
}
