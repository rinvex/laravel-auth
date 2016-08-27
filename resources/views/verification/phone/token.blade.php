@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <section class="panel panel-default">

                    <header class="panel-heading">
                        {{ trans('rinvex.fort::form.verification.phone.verify.heading') }}
                    </header>

                    <div class="panel-body">

                        {{-- Form --}}
                        <form id="rinvex-fort-user-account-form" class="form-horizontal" role="form" method="POST" action="{{ route('rinvex.fort.verification.phone.verify.post') }}">

                            {{-- Form: CSRF Token --}}
                            {{ csrf_field() }}

                            @include('rinvex.fort::alerts.success')
                            @include('rinvex.fort::alerts.warning')
                            @include('rinvex.fort::alerts.error')

                            <div class="form-group{{ $errors->has('token') ? ' has-error' : '' }}">
                                <label for="token" class="col-md-4 control-label">{{ trans('rinvex.fort::form.verification.phone.verify.token') }}</label>

                                <div class="col-md-6">
                                    <input id="token" name="token" type="text" value="{{ old('token') }}" class="form-control" placeholder="Authentication Code" required autofocus>
                                    {{ trans('rinvex.fort::form.verification.phone.verify.backup_notice') }}<br />

                                    @if ($methods['phone'])
                                        <strong>{!! trans('rinvex.fort::form.verification.phone.verify.backup_sms', ['href' => route('rinvex.fort.verification.phone')]) !!}</strong>
                                    @else
                                        <strong>{{ trans('rinvex.fort::form.verification.phone.verify.backup') }}</strong>
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

                                    <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-check"></i> {{ trans('rinvex.fort::form.verification.phone.verify.submit') }}</button>

                                </div>
                            </div>

                        </form>

                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection
