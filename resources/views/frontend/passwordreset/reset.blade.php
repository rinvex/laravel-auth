@extends('rinvex/fort::frontend/common.layout')

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('rinvex/fort::frontend/forms.passwordreset.reset.heading') }}</div>

                    <div class="panel-body">
                        {{ Form::open(['route' => 'rinvex.fort.frontend.passwordreset.process', 'class' => 'form-horizontal']) }}
                            {{ Form::hidden('token', old('token', $token)) }}

                            @include('rinvex/fort::frontend/alerts.success')
                            @include('rinvex/fort::frontend/alerts.warning')
                            @include('rinvex/fort::frontend/alerts.error')

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                {{ Form::label('email', trans('rinvex/fort::frontend/forms.passwordreset.email'), ['class' => 'col-md-4 control-label']) }}

                                <div class="col-md-6">
                                    {{ Form::email('email', old('email', $email), ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::frontend/forms.passwordreset.email'), 'required' => 'required', 'readonly' => 'readonly']) }}

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                {{ Form::label('password', trans('rinvex/fort::frontend/forms.passwordreset.password'), ['class' => 'col-md-4 control-label']) }}

                                <div class="col-md-6">
                                    {{ Form::password('password', ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::frontend/forms.passwordreset.password'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                {{ Form::label('password_confirmation', trans('rinvex/fort::frontend/forms.passwordreset.password_confirmation'), ['class' => 'col-md-4 control-label']) }}

                                <div class="col-md-6">
                                    {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::frontend/forms.passwordreset.password_confirmation'), 'required' => 'required']) }}

                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                    {{ Form::button('<i class="fa fa-refresh"></i> '.trans('rinvex/fort::frontend/forms.passwordreset.reset.submit'), ['class' => 'btn btn-primary', 'type' => 'submit']) }}
                                    {{ Form::button(trans('rinvex/fort::frontend/forms.common.reset'), ['class' => 'btn btn-default']) }}
                                </div>
                            </div>

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
