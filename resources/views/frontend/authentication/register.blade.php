{{-- Master Layout --}}
@extends('rinvex/fort::frontend/common.layout')

{{-- Page Title --}}
@section('title')
    {{ config('app.name') }} » {{ trans('rinvex/fort::forms.common.register') }}
@stop

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <section class="panel panel-default">
                    <header class="panel-heading">{{ trans('rinvex/fort::forms.common.register') }}</header>
                    <div class="panel-body">
                        {{ Form::open(['route' => 'rinvex.fort.frontend.auth.register.process', 'class' => 'form-horizontal']) }}

                            @include('rinvex/fort::frontend/alerts.success')
                            @include('rinvex/fort::frontend/alerts.warning')
                            @include('rinvex/fort::frontend/alerts.error')

                            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                {{ Form::label('username', trans('rinvex/fort::forms.common.username'), ['class' => 'col-md-4 control-label']) }}

                                <div class="col-md-6">
                                    {{ Form::text('username', old('username'), ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.username'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                                    @if ($errors->has('username'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                {{ Form::label('email', trans('rinvex/fort::forms.common.email'), ['class' => 'col-md-4 control-label']) }}

                                <div class="col-md-6">
                                    {{ Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.email'), 'required' => 'required']) }}

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                {{ Form::label('password', trans('rinvex/fort::forms.common.password'), ['class' => 'col-md-4 control-label']) }}

                                <div class="col-md-6">
                                    {{ Form::password('password', ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.password'), 'required' => 'required']) }}

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                {{ Form::label('password_confirmation', trans('rinvex/fort::forms.common.password_confirmation'), ['class' => 'col-md-4 control-label']) }}

                                <div class="col-md-6">
                                    {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.password_confirmation'), 'required' => 'required']) }}

                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                    {{ Form::button(trans('rinvex/fort::forms.common.register'), ['class' => 'btn btn-primary', 'type' => 'submit']) }}
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
