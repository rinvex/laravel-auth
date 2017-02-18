@if (count($errors) > 0)

    <div class="alert alert-danger">
        @if ($errors->count() > 1)
            {!! trans('rinvex/fort::messages.error') !!}<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @else
            {{ $errors->first() }}
        @endif
    </div>

@endif
