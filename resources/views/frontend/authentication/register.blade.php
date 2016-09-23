@extends('layouts.app')

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('rinvex.fort::frontend/forms.register.heading') }}</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('rinvex.fort.frontend.auth.register.post') }}">
                            {{ csrf_field() }}

                            @include('rinvex.fort::frontend.alerts.success')
                            @include('rinvex.fort::frontend.alerts.warning')
                            @include('rinvex.fort::frontend.alerts.error')

                            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                <label for="username" class="col-md-4 control-label">{{ trans('rinvex.fort::frontend/forms.register.username') }}</label>

                                <div class="col-md-6">
                                    <input id="username" type="username" class="form-control" name="username" value="{{ old('username') }}" placeholder="{{ trans('rinvex.fort::frontend/forms.register.username') }}" required autofocus>

                                    @if ($errors->has('username'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">{{ trans('rinvex.fort::frontend/forms.register.email') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{ trans('rinvex.fort::frontend/forms.register.email') }}" required>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">{{ trans('rinvex.fort::frontend/forms.register.password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" placeholder="{{ trans('rinvex.fort::frontend/forms.register.password') }}" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label for="password_confirmation" class="col-md-4 control-label">{{ trans('rinvex.fort::frontend/forms.register.password_confirmation') }}</label>

                                <div class="col-md-6">
                                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="{{ trans('rinvex.fort::frontend/forms.register.password_confirmation') }}" required>

                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">

                                    <button type="submit" class="btn btn-primary">{{ trans('rinvex.fort::frontend/forms.register.submit') }}</button>
                                    <button type="reset" class="btn btn-default">{{ trans('rinvex.fort::frontend/forms.common.reset') }}</button>

                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
