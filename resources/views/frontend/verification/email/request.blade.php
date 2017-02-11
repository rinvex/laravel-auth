{{-- Master Layout --}}
@extends('rinvex/fort::frontend/common.layout')

{{-- Page Title --}}
@section('title')
    {{ config('app.name') }} » {{ trans('rinvex/fort::forms.common.verification_email_request') }}
@stop

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <section class="panel panel-default">
                    <header class="panel-heading">{{ trans('rinvex/fort::forms.common.verification_email_request') }}</header>

                    <div class="panel-body">
                        {{ Form::open(['route' => 'rinvex.fort.frontend.verification.email.send', 'class' => 'form-horizontal']) }}

                            @include('rinvex/fort::frontend/alerts.success')
                            @include('rinvex/fort::frontend/alerts.warning')
                            @include('rinvex/fort::frontend/alerts.error')

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                {{ Form::label('email', trans('rinvex/fort::forms.common.email'), ['class' => 'col-md-4 control-label']) }}

                                <div class="col-md-6">
                                    {{ Form::email('email', old('email', auth()->guest() ? '' : $currentUser->email), ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.email'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                    {{ Form::button('<i class="fa fa-envelope"></i> '.trans('rinvex/fort::forms.common.verification_email_request'), ['class' => 'btn btn-primary', 'type' => 'submit']) }}
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
