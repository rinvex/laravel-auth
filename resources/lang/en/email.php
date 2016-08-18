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

    'register' => [
        'welcome'                             => 'Account Registered Successfully!',
        'welcome_body'                        => 'Your account has been successfully registered, you can login now and start using our services.',
        'welcome_moderated_body'              => 'Your account has been successfully registered, you have to wait until staff approve your registration before being able to login and start using our services.',
        'welcome_verification_body'           => 'Your account has been successfully registered, you will receive another email message for account verification before being able to login and start using our services.',
        'welcome_verification_moderated_body' => 'Your account has been successfully registered, you will receive another email message for account verification, then you have to wait until staff approve your registration before being able to login and start using our services.',
    ],

    'auth' => [
        'lockout' => 'Account Lockout!',
    ],

    'password' => [
        'subject' => 'Your Password Reset Link',
    ],

    'verification' => [
        'subject'                => 'Your Account Verification Link',
        'success'                => 'Account Verified Successfully!',
        'success_body'           => 'Your account has been successfully verified, you can login now and start using our services.',
        'success_moderated_body' => 'Your account has been successfully verified, but you still have to wait until staff approve your account before being able to login and start using our services.',
    ],


];
