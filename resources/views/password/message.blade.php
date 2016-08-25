Hi {{ $user->first_name || $user->last_name ? $user->first_name . ' ' . $user->last_name : 'Dear' }}

We received the following password reset request:

At: {{ $tokenData['created_at'] }}

IP Address: {{ $tokenData['ip'] }}

Agent: {{ $tokenData['agent'] }}

If this wasn't you, please disregard this email.

If this was you, please <a href="{!! $link = route('rinvex.fort.password.reset').'?token='.$token.'&email='.urlencode($user->getEmailForPasswordReset()) !!}">click here</a> or copy and paste the following URL into your browser:
{!! $link !!}
This link will allow you change your password to something you'll remember, and it will expire within {{ $expiration }} minutes.
