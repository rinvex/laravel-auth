@extends('rinvex.fort::frontend.common.layout')

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('rinvex.fort::frontend/forms.passwordreset.reset.heading') }}</div>

                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('rinvex.fort.frontend.passwordreset.process') }}">
                            {{ csrf_field() }}

                            @include('rinvex.fort::frontend.alerts.success')
                            @include('rinvex.fort::frontend.alerts.warning')
                            @include('rinvex.fort::frontend.alerts.error')

                            <input type="hidden" id="token" name="token" value="{{ $token }}">

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">{{ trans('rinvex.fort::frontend/forms.passwordreset.email') }}</label>

                                <div class="col-md-6">
                                    <input id="email" name="email" type="email" class="form-control" value="{{ $email or old('email') }}" placeholder="{{ trans('rinvex.fort::frontend/forms.passwordreset.email') }}" required readonly>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">{{ trans('rinvex.fort::frontend/forms.passwordreset.password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" name="password" type="password" class="form-control" placeholder="{{ trans('rinvex.fort::frontend/forms.passwordreset.password') }}" required autofocus>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label for="password_confirmation" class="col-md-4 control-label">{{ trans('rinvex.fort::frontend/forms.passwordreset.password_confirmation') }}</label>

                                <div class="col-md-6">
                                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="{{ trans('rinvex.fort::frontend/forms.passwordreset.password_confirmation') }}" required>

                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">

                                    <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-refresh"></i> {{ trans('rinvex.fort::frontend/forms.passwordreset.reset.submit') }}</button>

                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
