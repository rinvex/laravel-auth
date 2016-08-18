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

/*
|--------------------------------------------------------------------------
| Rinvex Fort Routes
|--------------------------------------------------------------------------
|
| Here you can see all authentication, authorization, and verification
| routes. It's automatically loaded in the package's service provider.
|
*/

Route::group([
    'middleware' => 'web',
    'as'         => 'rinvex.fort.',
    'namespace'  => 'Rinvex\Fort\Http\Controllers',
], function () {
    Route::group(['as' => 'auth.', 'prefix' => 'auth'], function () {

        /*
        |--------------------------------------------------------------------------
        | Login Routes
        |--------------------------------------------------------------------------
        */

        Route::get('login', ['as' => 'login', 'uses' => 'AuthenticationController@showLogin']);
        Route::post('login', ['as' => 'login.post', 'uses' => 'AuthenticationController@processLogin']);
        Route::get('logout', ['as' => 'logout', 'uses' => 'AuthenticationController@logout']);

        /*
        |--------------------------------------------------------------------------
        | Registration Routes
        |--------------------------------------------------------------------------
        */

        Route::get('register', ['as' => 'register', 'uses' => 'AuthenticationController@showRegisteration']);
        Route::post('register', ['as' => 'register.post', 'uses' => 'AuthenticationController@processRegisteration']);

        /*
        |--------------------------------------------------------------------------
        | Social Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::get('github', ['as' => 'social.github', 'uses' => 'SocialAuthenticationController@redirectToGithub']);
        Route::get('github/callback', ['as' => 'social.github.callback', 'uses' => 'SocialAuthenticationController@handleGithubCallback']);
    });


    /*
    |--------------------------------------------------------------------------
    | User Account Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'account.', 'prefix' => 'account'], function () {
        Route::get('page', ['as' => 'page', 'uses' => 'AccountController@showAccountUpdate']);
        Route::post('page', ['as' => 'page.post', 'uses' => 'AccountController@processAccountUpdate']);

        Route::get('sessions', ['as' => 'sessions', 'uses' => 'AccountController@showAccountSessions']);
        Route::get('sessions/flush/{token}', ['as' => 'sessions.flush', 'uses' => 'AccountController@processSessionFlush'])->where('token', '[0-9a-zA-Z]+');
        Route::get('sessions/flushall', ['as' => 'sessions.flushall', 'uses' => 'AccountController@processSessionFlush']);

        /*
        |--------------------------------------------------------------------------
        | Two-Factor Authentication Routes
        |--------------------------------------------------------------------------
        */
        Route::group(['as' => 'twofactor.', 'prefix' => 'twofactor'], function () {
            Route::group(['as' => 'totp.', 'prefix' => 'totp'], function () {
                Route::get('backup', ['as' => 'backup', 'uses' => 'AccountController@processTwoFactorTotpBackup']);
                Route::get('disable', ['as' => 'disable', 'uses' => 'AccountController@processTwoFactorTotpDisable']);
                Route::get('enable', ['as' => 'enable', 'uses' => 'AccountController@showTwoFactorTotpEnable']);
                Route::post('enable', ['as' => 'enable.post', 'uses' => 'AccountController@processTwoFactorTotpEnable']);
            });

            Route::group(['as' => 'phone.', 'prefix' => 'phone'], function () {
                Route::get('enable', ['as' => 'enable', 'uses' => 'AccountController@processTwoFactorPhoneEnable']);
                Route::get('disable', ['as' => 'disable', 'uses' => 'AccountController@processTwoFactorPhoneDisable']);
            });
        });
    });


    /*
    |--------------------------------------------------------------------------
    | Reset Password Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'password.', 'prefix' => 'password'], function () {
        Route::get('request', ['as' => 'request', 'uses' => 'ResetterController@showPasswordResetRequest']);
        Route::post('request', ['as' => 'request.post', 'uses' => 'ResetterController@processPasswordResetRequest']);

        Route::get('reset', ['as' => 'reset', 'uses' => 'ResetterController@showPasswordReset']);
        Route::post('reset', ['as' => 'reset.post', 'uses' => 'ResetterController@processPasswordReset']);
    });


    Route::group(['as' => 'verification.', 'prefix' => 'verification'], function () {

        /*
        |--------------------------------------------------------------------------
        | Verification Routes
        |--------------------------------------------------------------------------
        */

        Route::get('phone', ['as' => 'phone', 'uses' => 'VerificationController@showPhoneVerificationRequest']);
        Route::post('phone', ['as' => 'phone.post', 'uses' => 'VerificationController@processPhoneVerificationRequest']);

        Route::get('phone/verify', ['as' => 'phone.token', 'uses' => 'VerificationController@showPhoneVerification']);
        Route::post('phone/verify', ['as' => 'phone.token.post', 'uses' => 'VerificationController@processPhoneVerification']);

        Route::get('email', ['as' => 'email', 'uses' => 'VerificationController@showEmailVerificationRequest']);
        Route::post('email', ['as' => 'email.post', 'uses' => 'VerificationController@processEmailVerificationRequest']);

        Route::get('email/verify', ['as' => 'email.token', 'uses' => 'VerificationController@processEmailVerification']);
    });
});
