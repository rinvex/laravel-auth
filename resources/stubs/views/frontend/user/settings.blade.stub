{{-- Master Layout --}}
@extends('rinvex/fort::frontend/common.layout')

{{-- Page Title --}}
@section('title')
    {{ config('app.name') }} » {{ trans('rinvex/fort::forms.common.account') }}
@stop

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <section class="panel panel-default">

                    <header class="panel-heading">
                        {{ trans('rinvex/fort::forms.common.account') }}
                    </header>

                    <div class="panel-body">
                        {{ Form::model($currentUser, ['route' => 'rinvex.fort.frontend.user.settings.update', 'class' => 'form-horizontal']) }}

                            @include('rinvex/fort::frontend/alerts.success')
                            @include('rinvex/fort::frontend/alerts.warning')
                            @include('rinvex/fort::frontend/alerts.error')

                            <div class="row">
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    {{ Form::label('email', trans('rinvex/fort::forms.common.email'), ['class' => 'col-md-3 control-label']) }}

                                    <div class="col-md-8">
                                        {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.email'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                                        @if ($currentUser->email_verified)
                                            <small class="text-success">{!! trans('rinvex/fort::forms.common.email_verified', ['date' => $currentUser->email_verified_at]) !!}</small>
                                        @else
                                            <small class="text-danger">{!! trans('rinvex/fort::forms.common.email_unverified', ['href' => route('rinvex.fort.frontend.verification.email.request')]) !!}</small>
                                        @endif

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                    {{ Form::label('username', trans('rinvex/fort::forms.common.username'), ['class' => 'col-md-3 control-label']) }}

                                    <div class="col-md-8">
                                        {{ Form::text('username', null, ['class' => 'form-control', 'placeholder' => $currentUser->username, 'required' => 'required']) }}

                                        @if ($errors->has('username'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('username') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            <hr />


                            <div class="row">
                                <div class="form-group{{ $errors->has('prefix') ? ' has-error' : '' }}">
                                    {{ Form::label('prefix', trans('rinvex/fort::forms.common.prefix'), ['class' => 'col-md-3 control-label']) }}

                                    <div class="col-md-8">
                                        {{ Form::text('prefix', null, ['class' => 'form-control', 'placeholder' => $currentUser->prefix ?: trans('rinvex/fort::forms.common.prefix')]) }}

                                        @if ($errors->has('prefix'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('prefix') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                    {{ Form::label('first_name', trans('rinvex/fort::forms.common.first_name'), ['class' => 'col-md-3 control-label']) }}

                                    <div class="col-md-8">
                                        {{ Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => $currentUser->first_name ?: trans('rinvex/fort::forms.common.first_name')]) }}

                                        @if ($errors->has('first_name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('first_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group{{ $errors->has('middle_name') ? ' has-error' : '' }}">
                                    {{ Form::label('middle_name', trans('rinvex/fort::forms.common.middle_name'), ['class' => 'col-md-3 control-label']) }}

                                    <div class="col-md-8">
                                        {{ Form::text('middle_name', null, ['class' => 'form-control', 'placeholder' => $currentUser->middle_name ?: trans('rinvex/fort::forms.common.middle_name')]) }}

                                        @if ($errors->has('middle_name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('middle_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                    {{ Form::label('last_name', trans('rinvex/fort::forms.common.last_name'), ['class' => 'col-md-3 control-label']) }}

                                    <div class="col-md-8">
                                        {{ Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => $currentUser->last_name ?: trans('rinvex/fort::forms.common.last_name')]) }}

                                        @if ($errors->has('last_name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('last_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group{{ $errors->has('suffix') ? ' has-error' : '' }}">
                                    {{ Form::label('suffix', trans('rinvex/fort::forms.common.suffix'), ['class' => 'col-md-3 control-label']) }}

                                    <div class="col-md-8">
                                        {{ Form::text('suffix', null, ['class' => 'form-control', 'placeholder' => $currentUser->suffix ?: trans('rinvex/fort::forms.common.suffix')]) }}

                                        @if ($errors->has('suffix'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('suffix') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr />

                            <div class="row">
                                <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
                                    {{ Form::label('gender', trans('rinvex/fort::forms.common.gender'), ['class' => 'col-md-3 control-label']) }}

                                    <div class="col-md-8">
                                        <select id="gender" name="gender" class="form-control">
                                            <option value="" disabled selected>{{ trans('rinvex/fort::forms.common.select') }}</option>
                                            <option value="male" @if(old('gender', $currentUser->gender) === 'male') selected @endif>{{ trans('rinvex/fort::forms.common.male') }}</option>
                                            <option value="female" @if(old('gender', $currentUser->gender) === 'female') selected @endif>{{ trans('rinvex/fort::forms.common.female') }}</option>
                                            <option value="undisclosed" @if(old('gender', $currentUser->gender) === 'undisclosed') selected @endif>{{ trans('rinvex/fort::forms.common.undisclosed') }}</option>
                                        </select>

                                        @if ($errors->has('gender'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('gender') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                                    {{ Form::label('country', trans('rinvex/fort::forms.common.country'), ['class' => 'col-md-3 control-label']) }}

                                    <div class="col-md-8">
                                        <select id="country" name="country" class="form-control">
                                            <option value="" disabled selected>{{ trans('rinvex/fort::forms.common.select') }}</option>
                                            @foreach($countries as $code => $country)
                                                <option value="{{ $code }}" @if(old('country', $currentUser->country) === $code) selected="selected" @endif>{{ $country }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('country'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('country') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    {{ Form::label('phone', trans('rinvex/fort::forms.common.phone'), ['class' => 'col-md-3 control-label']) }}

                                    <div class="col-md-8">
                                        {{ Form::text('phone', null, ['class' => 'form-control', 'placeholder' => $currentUser->phone ?: trans('rinvex/fort::forms.common.phone')]) }}

                                        @if ($currentUser->phone_verified)
                                            <small class="text-success">{!! trans('rinvex/fort::forms.common.phone_verified', ['date' => $currentUser->phone_verified_at]) !!}</small>
                                        @else
                                            <small class="text-danger">{!! trans('rinvex/fort::forms.common.phone_unverified', ['href' => route('rinvex.fort.frontend.verification.phone.request')]) !!}</small>
                                        @endif

                                        @if ($errors->has('phone'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr />


                            <div class="row">
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    {{ Form::label('password', trans('rinvex/fort::forms.common.password'), ['class' => 'col-md-3 control-label']) }}

                                    <div class="col-md-8">
                                        {{ Form::password('password', ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.password')]) }}

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                    {{ Form::label('password_confirmation', trans('rinvex/fort::forms.common.password_confirmation'), ['class' => 'col-md-3 control-label']) }}

                                    <div class="col-md-8">
                                        {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.password_confirmation')]) }}

                                        @if ($errors->has('password_confirmation'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if(! empty(config('rinvex.fort.twofactor.providers')))

                                <hr />

                                <div class="row">
                                    <div class="form-group">

                                        <div class="col-md-12">

                                            <div class="text-center">
                                                <a class="btn btn-default text-center" role="button" data-toggle="collapse" href="#collapseTwoFactor" aria-expanded="false" aria-controls="collapseTwoFactor">
                                                    @if(array_get($twoFactor, 'totp.enabled') || array_get($twoFactor, 'phone.enabled'))
                                                        {!! trans('rinvex/fort::forms.twofactor.active') !!}
                                                    @else
                                                        {!! trans('rinvex/fort::forms.twofactor.inactive') !!}
                                                    @endif
                                                </a>
                                            </div>

                                            <div class="collapse col-md-10 col-md-offset-1" id="collapseTwoFactor">

                                                <hr />
                                                <p class="text-justify">{{ trans('rinvex/fort::forms.twofactor.notice') }}</p>
                                                <hr />

                                                @if(in_array('totp', config('rinvex.fort.twofactor.providers')))

                                                    <div class="panel panel-primary">
                                                        <header class="panel-heading">
                                                            <a class="btn btn-default btn-xs pull-right" style="margin-left: 10px" href="{{ route('rinvex.fort.frontend.user.twofactor.totp.enable') }}">@if(array_get($twoFactor, 'totp.enabled')) {{ trans('rinvex/fort::forms.common.settings') }} @else {{ trans('rinvex/fort::forms.common.enable') }} @endif</a>
                                                            @if(array_get($twoFactor, 'totp.enabled'))
                                                                <a class="btn btn-default btn-xs pull-right" href="{{ route('rinvex.fort.frontend.user.twofactor.totp.disable') }}">{{ trans('rinvex/fort::forms.common.disable') }}</a>
                                                            @endif
                                                            <h3 class="panel-title">
                                                                {{ trans('rinvex/fort::forms.twofactor.totp_head') }}
                                                            </h3>
                                                        </header>
                                                        <div class="panel-body">
                                                            {!! trans('rinvex/fort::forms.twofactor.totp_body') !!}
                                                        </div>
                                                    </div>

                                                @endif

                                                @if(in_array('phone', config('rinvex.fort.twofactor.providers')))

                                                    <div class="panel panel-primary">
                                                        <header class="panel-heading">
                                                            @if(array_get($twoFactor, 'phone.enabled'))
                                                                <a class="btn btn-default btn-xs pull-right" href="{{ route('rinvex.fort.frontend.user.twofactor.phone.disable') }}">{{ trans('rinvex/fort::forms.common.disable') }}</a>
                                                            @else
                                                                <a class="btn btn-default btn-xs pull-right" href="{{ route('rinvex.fort.frontend.user.twofactor.phone.enable') }}">{{ trans('rinvex/fort::forms.common.enable') }}</a>
                                                            @endif
                                                            <h3 class="panel-title">
                                                                {{ trans('rinvex/fort::forms.twofactor.phone_head') }}
                                                            </h3>
                                                        </header>
                                                        <div class="panel-body">
                                                            {{ trans('rinvex/fort::forms.twofactor.phone_body') }}
                                                        </div>
                                                    </div>

                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endif

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                    {{ Form::button('<i class="fa fa-user"></i> '.trans('rinvex/fort::forms.common.account'), ['class' => 'btn btn-primary', 'type' => 'submit']) }}
                                    {{ Form::reset(trans('rinvex/fort::forms.common.reset'), ['class' => 'btn btn-default']) }}
                                </div>
                            </div>

                        {{ Form::close() }}
                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection
