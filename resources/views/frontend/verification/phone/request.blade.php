{{-- Master Layout --}}
@extends('rinvex/fort::frontend/common.layout')

{{-- Page Title --}}
@section('title')
    {{ config('app.name') }} » {{ trans('rinvex/fort::forms.common.verification_phone_request') }}
@stop

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <section class="panel panel-default">
                    <header class="panel-heading">{{ trans('rinvex/fort::forms.common.verification_phone_request') }}</header>

                    <div class="panel-body">
                        {{ Form::open(['route' => 'rinvex.fort.frontend.verification.phone.send', 'class' => 'form-horizontal']) }}

                            @include('rinvex/fort::frontend/alerts.success')
                            @include('rinvex/fort::frontend/alerts.warning')
                            @include('rinvex/fort::frontend/alerts.error')

                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                {{ Form::label('phone', trans('rinvex/fort::forms.common.phone'), ['class' => 'col-md-4 control-label']) }}

                                <div class="col-md-6">
                                    {{ Form::text('phone', old('phone', auth()->guest() ? '' : $currentUser->phone), ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.phone'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('method') ? ' has-error' : '' }}">
                                {{ Form::label('phone', trans('rinvex/fort::forms.common.verification_method'), ['class' => 'col-md-4 control-label']) }}

                                <div class="col-md-6">

                                    <div class="btn-group" data-toggle="buttons">
                                        <label for="sms" class="btn btn-default active">
                                            <input id="sms" name="method" type="radio" value="sms" autocomplete="off" checked> {{ trans('rinvex/fort::forms.common.sms') }}
                                        </label>
                                        <label for="call" class="btn btn-default">
                                            <input id="call" name="method" type="radio" value="call" autocomplete="off"> {{ trans('rinvex/fort::forms.common.call') }}
                                        </label>
                                    </div>

                                    @if ($errors->has('method'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('method') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                    {{ Form::button('<i class="fa fa-phone"></i> '.trans('rinvex/fort::forms.common.verification_phone_request'), ['class' => 'btn btn-primary', 'type' => 'submit']) }}
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
