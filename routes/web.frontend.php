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
    'middleware' => ['web', 'abilities'],
    'as'         => 'rinvex.fort.frontend.',
    'namespace'  => 'Rinvex\Fort\Http\Controllers\Frontend',
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

        Route::get('login')->name('login')->uses('AuthenticationController@form');
        Route::post('login')->name('login.process')->uses('AuthenticationController@login');
        Route::post('logout')->name('logout')->uses('AuthenticationController@logout');

        /*
        |--------------------------------------------------------------------------
        | Registration Routes
        |--------------------------------------------------------------------------
        */

        Route::get('register')->name('register')->uses('RegistrationController@form');
        Route::post('register')->name('register.post')->uses('RegistrationController@register');

        /*
        |--------------------------------------------------------------------------
        | Social Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::get('github')->name('social.github')->uses('SocialAuthenticationController@redirectToGithub');
        Route::get('github/callback')->name('social.github.callback')->uses('SocialAuthenticationController@handleGithubCallback');
    });

    /*
    |--------------------------------------------------------------------------
    | User Account Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'user.', 'prefix' => 'user'], function () {

        /*
        |--------------------------------------------------------------------------
        | Account Page Routes
        |--------------------------------------------------------------------------
        */

        Route::get('settings')->name('settings')->uses('UserSettingsController@edit');
        Route::post('settings')->name('settings.update')->uses('UserSettingsController@update');

        /*
        |--------------------------------------------------------------------------
        | Sessions Manipulation Routes
        |--------------------------------------------------------------------------
        */

        Route::get('sessions')->name('sessions')->uses('UserSessionsController@index');
        Route::delete('sessions/{token?}')->name('sessions.flush')->uses('UserSessionsController@flush');

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
                Route::get('enable')->name('enable')->uses('TwoFactorSettingsController@enableTotp');
                Route::post('update')->name('update')->uses('TwoFactorSettingsController@updateTotp');
                Route::get('disable')->name('disable')->uses('TwoFactorSettingsController@disableTotp');
                Route::get('backup')->name('backup')->uses('TwoFactorSettingsController@backupTotp');
            });

            /*
            |--------------------------------------------------------------------------
            | Two-Factor Phone Routes
            |--------------------------------------------------------------------------
            */

            Route::group(['as' => 'phone.', 'prefix' => 'phone'], function () {
                Route::get('enable')->name('enable')->uses('TwoFactorSettingsController@enablePhone');
                Route::get('disable')->name('disable')->uses('TwoFactorSettingsController@disablePhone');
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Password Reset Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['as' => 'passwordreset.', 'prefix' => 'passwordreset'], function () {
        Route::get('request')->name('request')->uses('PasswordResetController@request');
        Route::post('send')->name('send')->uses('PasswordResetController@send');
        Route::get('reset')->name('reset')->uses('PasswordResetController@reset');
        Route::post('process')->name('process')->uses('PasswordResetController@process');
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

        Route::group(['as' => 'phone.', 'prefix' => 'phone'], function () {
            Route::get('request')->name('request')->uses('PhoneVerificationController@request');
            Route::post('send')->name('send')->uses('PhoneVerificationController@send');
            Route::get('verify')->name('verify')->uses('PhoneVerificationController@verify');
            Route::post('process')->name('process')->uses('PhoneVerificationController@process');
        });

        /*
        |--------------------------------------------------------------------------
        | Email Verification Routes
        |--------------------------------------------------------------------------
        */

        Route::group(['as' => 'email.', 'prefix' => 'email'], function () {
            Route::get('request')->name('request')->uses('EmailVerificationController@request');
            Route::post('send')->name('send')->uses('EmailVerificationController@send');
            Route::get('verify')->name('verify')->uses('EmailVerificationController@verify');
        });
    });
});
