{{-- Master Layout --}}
@extends('rinvex/fort::frontend/common.layout')

{{-- Page Title --}}
@section('title')
    @parent
    » {{ trans('rinvex/fort::frontend/forms.verification.phone.verify.heading') }}
@stop

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <section class="panel panel-default">

                    <header class="panel-heading">
                        {{ trans('rinvex/fort::frontend/forms.verification.phone.verify.heading') }}
                    </header>

                    <div class="panel-body">
                        {{ Form::open(['route' => 'rinvex.fort.frontend.verification.phone.process', 'class' => 'form-horizontal']) }}

                            @include('rinvex/fort::frontend/alerts.success')
                            @include('rinvex/fort::frontend/alerts.warning')
                            @include('rinvex/fort::frontend/alerts.error')

                            <div class="form-group{{ $errors->has('token') ? ' has-error' : '' }}">
                                {{ Form::label('token', trans('rinvex/fort::frontend/forms.verification.phone.verify.token'), ['class' => 'col-md-4 control-label']) }}

                                <div class="col-md-6">
                                    {{ Form::text('token', old('token'), ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::frontend/forms.verification.phone.verify.token'), 'required' => 'required', 'autofocus' => 'autofocus']) }}
                                    {{ trans('rinvex/fort::frontend/forms.verification.phone.verify.backup_notice') }}<br />

                                    @if ($methods['phone'])
                                        <strong>{!! trans('rinvex/fort::frontend/forms.verification.phone.verify.backup_sms', ['href' => route('rinvex.fort.frontend.verification.phone.request')]) !!}</strong>
                                    @else
                                        <strong>{{ trans('rinvex/fort::frontend/forms.verification.phone.verify.backup') }}</strong>
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
                                    {{ Form::button('<i class="fa fa-check"></i> '.trans('rinvex/fort::frontend/forms.verification.phone.verify.submit'), ['class' => 'btn btn-primary', 'type' => 'submit']) }}
                                    {{ Form::reset(trans('rinvex/fort::frontend/forms.common.reset'), ['class' => 'btn btn-default']) }}
                                </div>
                            </div>

                        {{ Form::close() }}
                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection
