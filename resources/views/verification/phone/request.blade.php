@extends('layouts.app')

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('rinvex.fort::form.verification.phone.request.heading') }}</div>
                    <div class="panel-body">

                        <form class="form-horizontal" role="form" method="POST" action="{{ route('rinvex.fort.verification.phone.post') }}">
                            {{ csrf_field() }}

                            @include('rinvex.fort::alerts.success')
                            @include('rinvex.fort::alerts.warning')
                            @include('rinvex.fort::alerts.error')

                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label for="phone" class="col-md-4 control-label">{{ trans('rinvex.fort::form.verification.phone.request.phone') }}</label>

                                <div class="col-md-6">
                                    <input id="phone" name="phone" type="phone" class="form-control" value="{{ old('phone', auth()->guest() ? '' : $currentUser->phone) }}" placeholder="{{ trans('rinvex.fort::form.verification.phone.request.phone') }}" required autofocus>

                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('method') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">Verification Method</label>

                                <div class="col-md-6">

                                    <div class="btn-group" data-toggle="buttons">
                                        <label for="sms" class="btn btn-default active">
                                            <input id="sms" name="method" type="radio" value="sms" autocomplete="off" checked> SMS
                                        </label>
                                        <label for="call" class="btn btn-default">
                                            <input id="call" name="method" type="radio" value="call" autocomplete="off"> Voice Call
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

                                    <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-phone"></i> {{ trans('rinvex.fort::form.verification.phone.request.submit') }}</button>

                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
