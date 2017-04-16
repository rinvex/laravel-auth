<?php

declare(strict_types=1);

return [

    'register' => [
        'welcome' => [
            'subject' => 'Account Registered Successfully!',
            'intro_default' => 'Your account has been successfully registered, you can login now and start using our services.',
            'intro_moderation' => 'Your account has been successfully registered, you have to wait until staff approve your registration before being able to login and start using our services.',
            'intro_verification' => 'Your account has been successfully registered, you will receive another email message for account verification before being able to login and start using our services.',
            'intro_verification_moderation' => 'Your account has been successfully registered, you will receive another email message for account verification, then you have to wait until staff approve your registration before being able to login and start using our services.',
        ],
    ],

    'auth' => [
        'lockout' => [
            'subject' => 'Your Account Locked',
            'intro' => 'Your account has been locked out due to too multiple failed login attempts. Failed login attempt reference: Time: :created_at, IP Address: :ip, Agent: :agent.',
            'outro' => "If this wasn't you, please make sure to harden your acount security, and feel free to contact us regarding this issue.",
        ],
    ],

    'passwordreset' => [
        'request' => [
            'action' => 'Reset Password',
            'subject' => 'Your Password Reset Link',
            'intro' => 'You are receiving this email because we received a password reset request for your account. Click the button below to reset your password (link expires in :expire seconds):',
            'outro' => 'If you did not request a password reset, no further action is required. Password reset request reference: Time: :created_at, IP Address: :ip, Agent: :agent.',
        ],
    ],

    'verification' => [
        'email' => [
            'action' => 'Verify Email',
            'subject' => 'Your Account Verification Link',
            'intro' => "You are receiving this email because account's email requires verification. Click the button below to verify your email address (link expires in :expire seconds):",
            'outro' => 'If you believe this is sent by mistake, no further action is required. Email verification request reference: Time: :created_at, IP Address: :ip, Agent: :agent.',

            'success' => [
                'subject' => 'Account Verified Successfully!',
                'intro_default' => 'Your account has been successfully verified, you can login now and start using our services.',
                'intro_moderation' => 'Your account has been successfully verified, but you still have to wait until staff approve your account before being able to login and start using our services.',
            ],
        ],
    ],

];
