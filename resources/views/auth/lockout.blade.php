Hi Dear,

Your account has been locked out due to too multiple failed login attempts. Failed attempts details:

At: {{ new Carbon\Carbon }}

IP Address: {{ $request->ip() }}

Agent: {{ $request->server('HTTP_USER_AGENT') }}

If this wasn't you, please make sure to harden your acount security, and feel free to contact us regarding this issue.
