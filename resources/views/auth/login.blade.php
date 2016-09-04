@extends('layouts.app')

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('rinvex.fort::form.login.heading') }}</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('rinvex.fort.auth.login.post') }}">
                            {{ csrf_field() }}

                            @include('rinvex.fort::alerts.success')
                            @include('rinvex.fort::alerts.warning')
                            @include('rinvex.fort::alerts.error')

                            <div class="form-group{{ $errors->has('loginfield') ? ' has-error' : '' }}">
                                <label for="loginfield" class="col-md-4 control-label">{{ trans('rinvex.fort::form.login.loginfield') }}</label>

                                <div class="col-md-6">
                                    <input id="loginfield" type="text" class="form-control" name="loginfield" value="{{ old('loginfield') }}" placeholder="{{ trans('rinvex.fort::form.login.loginfield') }}" required autofocus>

                                    @if ($errors->has('loginfield'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('loginfield') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">{{ trans('rinvex.fort::form.login.password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" placeholder="{{ trans('rinvex.fort::form.login.password') }}" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <div class="checkbox">
                                        <label for="remember">
                                            <input id="remember" name="remember" type="checkbox" value="1" @if(old('remember')) checked @endif> {{ trans('rinvex.fort::form.login.remember') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">

                                    <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-sign-in"></i>{{ trans('rinvex.fort::form.login.submit') }}</button>
                                    <a class="btn btn-link" href="{{ route('rinvex.fort.password.forgot') }}">{{ trans('rinvex.fort::form.login.forgot_password') }}</a>

                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
