{{-- Master Layout --}}
@extends('rinvex/fort::frontend/common.layout')

{{-- Page Title --}}
@section('title')
    {{ config('app.name') }} » {{ trans('rinvex/fort::forms.common.verify_phone') }}
@stop

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <section class="panel panel-default">

                    <header class="panel-heading">
                        {{ trans('rinvex/fort::forms.common.verify_phone') }}
                    </header>

                    <div class="panel-body">
                        {{ Form::open(['route' => 'rinvex.fort.frontend.verification.phone.process', 'class' => 'form-horizontal']) }}

                            @include('rinvex/fort::frontend/alerts.success')
                            @include('rinvex/fort::frontend/alerts.warning')
                            @include('rinvex/fort::frontend/alerts.error')

                            <div class="form-group{{ $errors->has('token') ? ' has-error' : '' }}">
                                {{ Form::label('token', trans('rinvex/fort::forms.common.authentication_code'), ['class' => 'col-md-4 control-label']) }}

                                <div class="col-md-6">
                                    {{ Form::text('token', old('token'), ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.authentication_code'), 'required' => 'required', 'autofocus' => 'autofocus']) }}
                                    {{ trans('rinvex/fort::forms.twofactor.backup_notice') }}<br />

                                    @if ($methods['phone'])
                                        <strong>{!! trans('rinvex/fort::forms.twofactor.backup_sms', ['href' => route('rinvex.fort.frontend.verification.phone.request')]) !!}</strong>
                                    @else
                                        <strong>{{ trans('rinvex/fort::forms.twofactor.backup_code') }}</strong>
                                    @endif

                                    @if ($errors->has('token'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('token') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                    {{ Form::button('<i class="fa fa-check"></i> '.trans('rinvex/fort::forms.common.verify_phone'), ['class' => 'btn btn-primary', 'type' => 'submit']) }}
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
