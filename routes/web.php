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

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */

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

        Route::get('register', ['as' => 'register', 'uses' => 'RegistrationController@showRegisteration']);
        Route::post('register', ['as' => 'register.post', 'uses' => 'RegistrationController@processRegisteration']);

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

        /*
        |--------------------------------------------------------------------------
        | Account Page Routes
        |--------------------------------------------------------------------------
        */

        Route::get('page', ['as' => 'page', 'uses' => 'AccountController@showAccountUpdate']);
        Route::post('page', ['as' => 'page.post', 'uses' => 'AccountController@processAccountUpdate']);

        /*
        |--------------------------------------------------------------------------
        | Sessions Manipulation Routes
        |--------------------------------------------------------------------------
        */

        Route::get('sessions', ['as' => 'sessions', 'uses' => 'AccountController@showAccountSessions']);
        Route::get('sessions/flush/{token}', ['as' => 'sessions.flush', 'uses' => 'AccountController@processSessionFlush'])->where('token', '[0-9a-zA-Z]+');
        Route::get('sessions/flushall', ['as' => 'sessions.flushall', 'uses' => 'AccountController@processSessionFlush']);

        /*
        |--------------------------------------------------------------------------
        | Two-Factor Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::group(['as' => 'twofactor.', 'prefix' => 'twofactor'], function () {

            /*
            |--------------------------------------------------------------------------
            | Two-Factor TOTP Routes
            |--------------------------------------------------------------------------
            */

            Route::group(['as' => 'totp.', 'prefix' => 'totp'], function () {
                Route::get('enable', ['as' => 'enable', 'uses' => 'AccountController@showTwoFactorTotpEnable']);
                Route::post('enable', ['as' => 'enable.post', 'uses' => 'AccountController@processTwoFactorTotpEnable']);
                Route::get('disable', ['as' => 'disable', 'uses' => 'AccountController@processTwoFactorTotpDisable']);
                Route::get('backup', ['as' => 'backup', 'uses' => 'AccountController@processTwoFactorTotpBackup']);
            });

            /*
            |--------------------------------------------------------------------------
            | Two-Factor Phone Routes
            |--------------------------------------------------------------------------
            */

            Route::group(['as' => 'phone.', 'prefix' => 'phone'], function () {
                Route::get('enable', ['as' => 'enable', 'uses' => 'AccountController@processTwoFactorPhoneEnable']);
                Route::get('disable', ['as' => 'disable', 'uses' => 'AccountController@processTwoFactorPhoneDisable']);
            });
        });
    });


    /*
    |--------------------------------------------------------------------------
    | Password Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'password.', 'prefix' => 'password'], function () {

        /*
        |--------------------------------------------------------------------------
        | Forgot Password Routes
        |--------------------------------------------------------------------------
        */

        Route::get('forgot', ['as' => 'forgot', 'uses' => 'ForgotPasswordController@showForgotPassword']);
        Route::post('forgot', ['as' => 'forgot.post', 'uses' => 'ForgotPasswordController@processForgotPassword']);

        /*
        |--------------------------------------------------------------------------
        | Reset Password Routes
        |--------------------------------------------------------------------------
        */

        Route::get('reset', ['as' => 'reset', 'uses' => 'ResetPasswordController@showResetPassword']);
        Route::post('reset', ['as' => 'reset.post', 'uses' => 'ResetPasswordController@processResetPassword']);
    });


    /*
    |--------------------------------------------------------------------------
    | Verification Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'verification.', 'prefix' => 'verification'], function () {

        /*
        |--------------------------------------------------------------------------
        | Phone Verification Routes
        |--------------------------------------------------------------------------
        */

        Route::get('phone', ['as' => 'phone', 'uses' => 'PhoneVerificationController@showPhoneVerificationRequest']);
        Route::post('phone', ['as' => 'phone.post', 'uses' => 'PhoneVerificationController@processPhoneVerificationRequest']);
        Route::get('phone/verify', ['as' => 'phone.verify', 'uses' => 'PhoneVerificationController@showPhoneVerification']);
        Route::post('phone/verify', ['as' => 'phone.verify.post', 'uses' => 'PhoneVerificationController@processPhoneVerification']);

        /*
        |--------------------------------------------------------------------------
        | Email Verification Routes
        |--------------------------------------------------------------------------
        */

        Route::get('email', ['as' => 'email', 'uses' => 'EmailVerificationController@showEmailVerificationRequest']);
        Route::post('email', ['as' => 'email.post', 'uses' => 'EmailVerificationController@processEmailVerificationRequest']);
        Route::get('email/verify', ['as' => 'email.verify', 'uses' => 'EmailVerificationController@processEmailVerification']);
    });
});
