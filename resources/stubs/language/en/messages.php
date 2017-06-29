<?php

declare(strict_types=1);

return [

    'error' => '<strong>Whoops!</strong> There were some problems with your input.',
    'session_required' => 'You must login first!',

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
        'success_verify' => 'Registration completed successfully! We have e-mailed your verification link!',
        'disabled' => 'Sorry, registration is currently disabled!',
    ],

    'account' => [
        'phone_field_required' => 'You must enter your phone first!',
        'phone_verification_required' => 'You must verify your phone first!',
        'country_required' => 'You must select your country first!',
        'phone_required' => 'You must update your phone first!',
        'reverify' => 'Since you updated your email, you must reverify your account again. You will not be able to login next time until you verify your account.',
        'updated' => 'You have successfully updated your profile!',
    ],

    'auth' => [
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

    'passwordreset' => [
        'already_logged' => 'You are logged in, so you can change password from your account settings.',
    ],

    'verification' => [

        'email' => [
            'expired_token' => 'This email verification link is expired, please request a new one.',
            'already_verified' => 'Your email already verified!',
            'verified' => 'Your email has been verified!',
            'link_sent' => 'We have e-mailed your verification link!',
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
            'globaly_disabled' => 'Sorry, TwoFactor authentication globally disabled!',
            'totp' => [
                'required' => 'TwoFactor TOTP authentication enabled for your account, authentication code required to proceed.',
                'enabled' => 'TwoFactor TOTP authentication has been enabled and backup codes generated for your account.',
                'disabled' => 'TwoFactor TOTP authentication has been disabled for your account.',
                'rebackup' => 'TwoFactor TOTP authentication backup codes re-generated for your account.',
                'cant_backup' => 'TwoFactor TOTP authentication currently disabled for your account, thus backup codes can not be generated.',
                'already' => 'You have already configured TwoFactor TOTP authentication. This page allows you to switch to a different authentication app. If this is not what you\'d like to do, you can go back to your account settings.',
                'invalid_token' => 'Your passcode did not match, or expired after scanning. Remove the old barcode from your app, and try again. Since this process is time-sensitive, make sure your device\'s date and time is set to "automatic."',
                'globaly_disabled' => 'Sorry, TwoFactor TOTP authentication globally disabled!',
            ],
            'phone' => [
                'enabled' => 'TwoFactor phone authentication has been enabled for your account.',
                'disabled' => 'TwoFactor phone authentication has been disabled for your account.',
                'auto_disabled' => 'TwoFactor phone authentication has been disabled for your account. Changing country or phone results in TwoFactor auto disable. You need to enable it again manually.',
                'phone_required' => 'Phone field seems to be missing in your account, and since TwoFactor authentication already activated which require that field, you can NOT login unfortunately. Please contact staff to solve this issue.',
                'country_required' => 'Country field seems to be missing in your account, and since TwoFactor authentication already activated which require that field, you can NOT login unfortunately. Please contact staff to solve this issue.',
                'globaly_disabled' => 'Sorry, TwoFactor phone authentication globally disabled!',
            ],
        ],

    ],

];
