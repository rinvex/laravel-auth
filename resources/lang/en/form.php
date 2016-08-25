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

    'sessions' => [
        'flush_single'        => 'Flush Selected Session',
        'flush_single_notice' => '<strong>Warning:</strong> Selected session will be flushed, and thus re-login again will be required on effected device.',
        'flush_all'           => 'Flush All Sessions',
        'flush_all_notice'    => '<strong>Warning:</strong> All active sessions of your account, including this one will be flushed, and you\'ll be forced to re-login again!',
    ],

    'register' => [
        'heading'               => 'Register',
        'username'              => 'Username',
        'email'                 => 'Email Address',
        'password'              => 'Password',
        'password_confirmation' => 'Password Confirmation',
        'submit'                => 'Register',
    ],

    'login' => [
        'heading'       => 'Login',
        'loginfield'    => 'Username / Email',
        'password'      => 'Password',
        'resetpassword' => 'Reset Password',
        'remember'      => 'Remember Me',
        'submit'        => 'Login',
    ],

    'password' => [
        'email'                 => 'Email Address',
        'password'              => 'Password',
        'password_confirmation' => 'Password Confirmation',

        'request' => [
            'heading' => 'Request Password Reset',
            'submit'  => 'Request Password Reset',
        ],

        'reset'   => [
            'heading' => 'Reset Password',
            'submit'  => 'Reset Password',
        ],
    ],

    'verification' => [
        'email' => [
            'heading' => 'Request Email Verification',
            'field'   => 'Email Address',
            'submit'  => 'Request Email Verification',
        ],

        'phone' => [
            'request' => [
                'heading'    => 'Request Phone Verification',
                'phone'      => 'Phone Number',
                'submit'     => 'Request Phone Verification',
            ],

            'verify' => [
                'heading'       => 'Verify Phone',
                'token'         => 'Authentication Code',
                'submit'        => 'Verify Phone',
                'backup'        => 'Use backup codes.',
                'backup_sms'    => 'Use backup codes, or request <a href=":href">SMS code</a>.',
                'backup_notice' => 'Problems with your verification app?',
            ],
        ],
    ],

    'account' => [
        'heading'               => 'Update Account',
        'phone'                 => 'Phone',
        'country'               => 'Country',
        'country_select'        => 'Select your country',
        'email'                 => 'Email',
        'username'              => 'Username',
        'first_name'            => 'First Name',
        'middle_name'           => 'Middle Name',
        'last_name'             => 'Last Name',
        'password'              => 'Password',
        'password_confirmation' => 'Password Confirmation',
        'active_sessions'       => 'Active Sessions',
        'submit'                => 'Update Account',
        'settings'              => 'Settings',
        'you'                   => 'You',
        'enable'                => 'Enable',
        'disable'               => 'Disable',
        'code'                  => 'Authentication Code',
        'email_verified'        => 'Email verified at <date>:date</date>.',
        'email_unverified'      => 'Email not yet verified! <a href=":href">Verify Email</a>',
        'phone_verified'        => 'Phone verified at <date>:date</date>.',
        'phone_unverified'      => 'Phone not yet verified! <a href=":href">Verify Phone</a>',
        'two_factor_notice'     => 'Protect your account with an extra layer of security by requiring access to your phone. Once configured, you\'ll be required to enter both your password and an authentication code from your mobile phone in order to sign into your account.',
        'two_factor_active'     => 'Two-Factor Authentication currently <strong>active</strong>, click to activate!',
        'two_factor_inactive'   => 'Two-Factor Authentication currently <strong>inactive</strong>, click to de-activate!',
        'twofactor_totp_head'   => 'User An App',
        'twofactor_totp_body'   => 'Retrieve codes from an authentication app on your device, like Google Authenticator, Duo Mobile, Authy, or Windows Phone Authenticator.',
        'twofactor_phone_head'  => 'SMS Text Message / Automated Phone Call',
        'twofactor_phone_body'  => 'Receive a text message, or an automated phone call to your mobile device when signing in.',
    ],

    'twofactor' => [
        'heading'                => 'Configure Two-Factor',
        'submit'                 => 'Configure Two-Factor',
        'totp_apps'              => 'Once configured, you will be required to enter a code created by the <a target="_blank" href="https://m.google.com/authenticator">Google Authenticator</a>, <a target="_blank" href="http://guide.duosecurity.com/">Duo Mobile</a>, <a target="_blank" href="https://www.authy.com/">Authy</a>, or<a href=" https://www.windowsphone.com/en-us/store/app/authenticator/e7994dbc-2336-4950-91ba-ca22d653759b">Windows Phone Authenticator</a> apps in order to sign into your account.',
        'totp_apps_step1'        => '<p>Step 1</p><p><strong>Get the App</strong></p><p>Download and install the<a target="_blank" href="https://m.google.com/authenticator">Google Authenticator</a>,<a target="_blank" href="http://guide.duosecurity.com/third-party-accounts">Duo Mobile</a>,<a target="_blank" href="https://www.authy.com/">Authy</a>, or<a href=" https://www.windowsphone.com/en-us/store/app/authenticator/e7994dbc-2336-4950-91ba-ca22d653759b">Windows Phone Authenticator</a> app for your phone or tablet.</p>',
        'totp_apps_step2'        => '<p>Step 2</p><p><strong>Scan this Barcode</strong></p><p>Open the authentication app and:</p><ul><li>Tap the "+" icon in the top-right of the app</li><li>Scan the image to the left, using your phone\'s camera</li></ul>',
        'totp_apps_step2_button' => "Can't scan this barcode?",
        'totp_apps_step2_1'      => 'Instead of scanning, use your authentication app\'s "Manual entry" or equivalent option and provide the following time-based key. (Lower-case letters will work, too.)',
        'totp_apps_step2_2'      => 'Your app will then generate a 6-digit authentication code, which you use below.',
        'totp_apps_step3'        => '<p>Step 3</p><p><strong>Enter Authentication Code</strong></p><p>Once the barcode above is scanned, enter the 6-digit authentication code generated by the app.</p>',
        'totp_backup_button'     => 'You have :count unused backup codes',
        'totp_backup_head'       => 'Two-Factor Authentication Backup Codes',
        'totp_backup_body'       => 'If you lose access to your authentication device, you can use one of these backup codes to login to your account. Each code may be used only once. Make a copy of these codes, and store it somewhere safe.',
        'totp_backup_notice'     => '<ul><li>These codes were generated on: <date>:backup_at</date>.</li><li>You can only use each backup code once.</li></ul>',
        'totp_backup_none'       => 'No unused backup codes! Generate some.',
        'totp_backup_generate'   => 'Re-generate Codes',
    ],

];
