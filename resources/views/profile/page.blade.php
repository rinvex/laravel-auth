@extends('layouts.app')

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <section class="panel panel-default">

                    <header class="panel-heading">
                        {{ trans('rinvex.fort::form.account.heading') }}
                    </header>

                    <div class="panel-body">

                        {{-- Form --}}
                        <form id="rinvex-fort-user-account-form" class="form-horizontal" role="form" method="POST" action="{{ route('rinvex.fort.account.page.post') }}">

                            {{-- Form: CSRF Token --}}
                            {{ csrf_field() }}

                            <input id="id" name="id" type="hidden" class="form-control" value="{{ $currentUser->id }}">

                            @include('rinvex.fort::alerts.success')
                            @include('rinvex.fort::alerts.warning')
                            @include('rinvex.fort::alerts.error')

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">

                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label for="email" class="col-md-3 control-label">{{ trans('rinvex.fort::form.account.email') }}</label>

                                        <div class="col-md-8">
                                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $currentUser->email) }}" placeholder="{{ old('email', $currentUser->email) }}" required>

                                            @if ($currentUser->email_verified && $currentUser->email_verified_at)
                                                <small class="text-success">{!! trans('rinvex.fort::form.account.email_verified', ['date' => $currentUser->email_verified_at]) !!}</small>
                                            @else
                                                <small class="text-danger">{!! trans('rinvex.fort::form.account.email_unverified', ['href' => route('rinvex.fort.verification.email')]) !!}</small>
                                            @endif

                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">

                                    <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                        <label for="username" class="col-md-3 control-label">{{ trans('rinvex.fort::form.account.username') }}</label>

                                        <div class="col-md-8">
                                            <input id="username" name="username" type="text" class="form-control" value="{{ old('username', $currentUser->username) }}" placeholder="{{ old('username', $currentUser->username) }}" required>

                                            @if ($errors->has('username'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('username') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <hr />


                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">

                                    <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                        <label for="first_name" class="col-md-3 control-label">{{ trans('rinvex.fort::form.account.first_name') }}</label>

                                        <div class="col-md-8">
                                            <input id="first_name" name="first_name" type="text" class="form-control" value="{{ old('first_name', $currentUser->first_name) }}" placeholder="{{ old('first_name', $currentUser->first_name ?: trans('rinvex.fort::form.account.first_name')) }}">

                                            @if ($errors->has('first_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('first_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">

                                    <div class="form-group{{ $errors->has('middle_name') ? ' has-error' : '' }}">
                                        <label for="middle_name" class="col-md-3 control-label">{{ trans('rinvex.fort::form.account.middle_name') }}</label>

                                        <div class="col-md-8">
                                            <input id="middle_name" name="middle_name" type="text" class="form-control" value="{{ old('middle_name', $currentUser->middle_name) }}" placeholder="{{ old('middle_name', $currentUser->middle_name ?: trans('rinvex.fort::form.account.middle_name')) }}">

                                            @if ($errors->has('middle_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('middle_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">

                                    <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                        <label for="last_name" class="col-md-3 control-label">{{ trans('rinvex.fort::form.account.last_name') }}</label>

                                        <div class="col-md-8">
                                            <input id="last_name" name="last_name" type="text" class="form-control" value="{{ old('last_name', $currentUser->last_name) }}" placeholder="{{ old('last_name', $currentUser->last_name ?: trans('rinvex.fort::form.account.last_name')) }}">

                                            @if ($errors->has('last_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('last_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <hr />

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">

                                    <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                                        <label class="col-md-3 control-label">
                                            {{ trans('rinvex.fort::form.account.country') }}
                                        </label>

                                        <div class="col-md-8">
                                            <select id="country" name="country" class="form-control">
                                                <option value="" disabled selected>{{ trans('rinvex.fort::form.account.country_select') }}</option>
                                                @foreach($countries as $countryKey => $countryName)
                                                    <option value="{{ $countryKey }}" @if(old('country', $currentUser->country) === $countryKey) selected="selected" @endif>{{ $countryName }}</option>
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
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">

                                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <label for="phone" class="col-md-3 control-label">{{ trans('rinvex.fort::form.account.phone') }}</label>

                                        <div class="col-md-8">
                                            <input id="phone" name="phone" type="phone" class="form-control" value="{{ old('phone', $currentUser->phone) }}" placeholder="{{ old('phone', $currentUser->phone) }}">

                                            @if ($currentUser->phone_verified && $currentUser->phone_verified_at)
                                                <small class="text-success">{!! trans('rinvex.fort::form.account.phone_verified', ['date' => $currentUser->phone_verified_at]) !!}</small>
                                            @else
                                                <small class="text-danger">{!! trans('rinvex.fort::form.account.phone_unverified', ['href' => route('rinvex.fort.verification.phone')]) !!}</small>
                                            @endif

                                            @if ($errors->has('phone'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('phone') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <hr />


                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">

                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <label for="password" class="col-md-3 control-label">{{ trans('rinvex.fort::form.account.password') }}</label>

                                        <div class="col-md-8">
                                            <input id="password" name="password" type="password" class="form-control" value="" placeholder="{{ trans('rinvex.fort::form.account.password') }}">

                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">

                                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                        <label for="password_confirmation" class="col-md-3 control-label">{{ trans('rinvex.fort::form.account.password_confirmation') }}</label>

                                        <div class="col-md-8">
                                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" value="" placeholder="{{ trans('rinvex.fort::form.account.password_confirmation') }}">

                                            @if ($errors->has('password_confirmation'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>

                            @if(! empty(config('rinvex.fort.twofactor.providers')))

                                <hr />

                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">

                                        <div class="form-group">

                                            <div class="col-md-12">

                                                <div class="text-center">
                                                    <a class="btn btn-default text-center" role="button" data-toggle="collapse" href="#collapseTwoFactor" aria-expanded="false" aria-controls="collapseTwoFactor">
                                                        @if(array_get($twoFactor, 'totp.enabled') || array_get($twoFactor, 'phone.enabled'))
                                                            {!! trans('rinvex.fort::form.account.two_factor_active') !!}
                                                        @else
                                                            {!! trans('rinvex.fort::form.account.two_factor_inactive') !!}
                                                        @endif
                                                    </a>
                                                </div>

                                                <div class="collapse" id="collapseTwoFactor">

                                                    <hr />
                                                    <p class="text-justify">{{ trans('rinvex.fort::form.account.two_factor_notice') }}</p>
                                                    <hr />

                                                    @if(in_array('totp', config('rinvex.fort.twofactor.providers')))

                                                        <div class="panel panel-primary">
                                                            <div class="panel-heading">
                                                                <a class="btn btn-default btn-xs pull-right" style="margin-left: 10px" href="{{ route('rinvex.fort.account.twofactor.totp.enable') }}">@if(array_get($twoFactor, 'totp.enabled')) {{ trans('rinvex.fort::form.account.settings') }} @else {{ trans('rinvex.fort::form.account.enable') }} @endif</a>
                                                                @if(array_get($twoFactor, 'totp.enabled'))
                                                                    <a class="btn btn-default btn-xs pull-right" href="{{ route('rinvex.fort.account.twofactor.totp.disable') }}">{{ trans('rinvex.fort::form.account.disable') }}</a>
                                                                @endif
                                                                <h3 class="panel-title">
                                                                    {{ trans('rinvex.fort::form.account.twofactor_totp_head') }}
                                                                </h3>
                                                            </div>
                                                            <div class="panel-body">
                                                                {{ trans('rinvex.fort::form.account.twofactor_totp_body') }}
                                                            </div>
                                                        </div>

                                                    @endif

                                                    @if(in_array('phone', config('rinvex.fort.twofactor.providers')))

                                                        <div class="panel panel-primary">
                                                            <div class="panel-heading">
                                                                @if(array_get($twoFactor, 'phone.enabled'))
                                                                    <a class="btn btn-default btn-xs pull-right" href="{{ route('rinvex.fort.account.twofactor.phone.disable') }}">{{ trans('rinvex.fort::form.account.disable') }}</a>
                                                                @else
                                                                    <a class="btn btn-default btn-xs pull-right" href="{{ route('rinvex.fort.account.twofactor.phone.enable') }}">{{ trans('rinvex.fort::form.account.enable') }}</a>
                                                                @endif
                                                                <h3 class="panel-title">
                                                                    {{ trans('rinvex.fort::form.account.twofactor_phone_head') }}
                                                                </h3>
                                                            </div>
                                                            <div class="panel-body">
                                                                {{ trans('rinvex.fort::form.account.twofactor_phone_body') }}
                                                            </div>
                                                        </div>

                                                    @endif

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            @endif

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">

                                    <button type="submit" class="btn btn-primary"><i class="fa fa-user"></i> {{ trans('rinvex.fort::form.account.submit') }}</button>
                                    <button type="reset" class="btn btn-default">{{ trans('rinvex.fort::common.reset') }}</button>

                                </div>
                            </div>

                        </form>

                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection
