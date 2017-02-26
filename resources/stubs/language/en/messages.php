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

return [

    'error' => '<strong>Whoops!</strong> There were some problems with your input.',

    'sessions' => [
        'flush_single_heading' => 'Flush Selected Session',
        'flush_single_body' => 'Selected session will be flushed, and thus re-login again will be required on effected device.',
        'flush_all_heading' => 'Flush All Sessions',
        'flush_all_body' => 'All active sessions of your account, including this one will be flushed, and you will be forced to re-login again!',
    ],

    'ability' => [
        'not_found' => 'Sorry! Requested ability not found!',
        'saved' => 'Congrats! Ability [:abilityId] saved successfully!',
        'deleted' => 'Done! Ability [:abilityId] deleted successfully!',
        'invalid_policy' => 'The policy must be a valid class method.',
    ],

    'role' => [
        'not_found' => 'Sorry! Requested role not found!',
        'saved' => 'Congrats! Role [:roleId] saved successfully!',
        'deleted' => 'Done! Role [:roleId] deleted successfully!',
    ],

    'user' => [
        'not_found' => 'Sorry! Requested user not found!',
        'saved' => 'Congrats! User [:userId] saved successfully!',
        'deleted' => 'Done! User [:userId] deleted successfully!',
    ],

    'register' => [
        'success' => 'Registration completed successfully!',
        'success_verify' => 'Registration completed successfully! Email verification request has been sent to you!',
        'disabled' => 'Sorry, registration is currently disabled!',
    ],

    'account' => [
        'phone_verification_required' => 'You must verify your phone first!',
        'country_required' => 'You must select your country first!',
        'phone_required' => 'You must update your phone first!',
        'reverify' => 'Since you updated your email, you must reverify your account again. You will not be able to login next time until you verify your account.',
        'updated' => 'You have successfully updated your profile!',
    ],

    'auth' => [
        'authorize' => 'Requested resource must be authorized.',
        'unauthorized' => 'Sorry, you do not have access to the requested resource!',
        'moderated' => 'Your account is currently moderated!',
        'unverified' => 'Your account in currently unverified!',
        'failed' => 'These credentials do not match our records.',
        'lockout' => 'Too many login attempts. Please try again in :seconds seconds.',
        'login' => 'You have successfully logged in!',
        'logout' => 'You have successfully logged out!',
        'already' => 'You are already authenticated!',
        'session' => [
            'required' => 'You must login first!',
            'expired' => 'Session expired, please login again!',
            'flushed' => 'Your selected session has been successfully flushed!',
            'flushedall' => 'All your active sessions has been successfully flushed!',
        ],
    ],

    'verification' => [

        'email' => [
            'already' => 'Your email already verified!',
            'verified' => 'Your email has been verified!',
            'link_sent' => 'Email verification request has been sent to you!',
            'invalid_token' => 'This verification token is invalid.',
            'invalid_user' => 'We can not find a user with that email address.',
        ],

        'phone' => [
            'verified' => 'Your phone has been verified!',
            'sent' => 'We have sent your verification token to your phone!',
            'failed' => 'Weird problem happen while verifying your phone, this issue has been logged and reported to staff.',
            'invalid_user' => 'We can not find a user with that email address.',
        ],

        'twofactor' => [
            'invalid_token' => 'This verification token is invalid.',
            'totp' => [
                'required' => 'Two-Factor TOTP authentication enabled for your account, authentication code required to proceed.',
                'enabled' => 'Two-Factor TOTP authentication has been enabled and backup codes generated for your account.',
                'disabled' => 'Two-Factor TOTP authentication has been disabled for your account.',
                'rebackup' => 'Two-Factor TOTP authentication backup codes re-generated for your account.',
                'cant_backup' => 'Two-Factor TOTP authentication currently disabled for your account, thus backup codes can not be generated.',
                'already' => 'You have already configured Two-Factor TOTP authentication. This page allows you to switch to a different authentication app. If this is not what you\'d like to do, you can go back to your account settings.',
                'invalid_token' => 'Your passcode did not match, or expired after scanning. Remove the old barcode from your app, and try again. Since this process is time-sensitive, make sure your device\'s date and time is set to "automatic."',
                'globaly_disabled' => 'Sorry, Two-Factor TOTP authentication globally disabled!',
            ],
            'phone' => [
                'enabled' => 'Two-Factor phone authentication has been enabled for your account.',
                'disabled' => 'Two-Factor phone authentication has been disabled for your account.',
                'auto_disabled' => 'Two-Factor phone authentication has been disabled for your account. Changing country or phone results in Two-Factor auto disable. You need to enable it again manually.',
                'country_required' => 'Country field seems to be missing in your account, and since Two-Factor authentication already activated which require that field, you can NOT login. Please contact staff to solve this issue.',
                'globaly_disabled' => 'Sorry, Two-Factor phone authentication globally disabled!',
            ],
        ],

    ],

];
