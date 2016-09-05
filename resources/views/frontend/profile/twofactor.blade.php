@extends('layouts.app')

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <section class="panel panel-default">

                    <header class="panel-heading">
                        {{ trans('rinvex.fort::form.twofactor.heading') }}
                    </header>

                    <div class="panel-body">

                        {{-- Form --}}
                        <form id="rinvex-fort-user-account-form" class="form-horizontal" role="form" method="POST" action="{{ route('rinvex.fort.frontend.account.twofactor.totp.enable.post') }}">

                            {{-- Form: CSRF Token --}}
                            {{ csrf_field() }}

                            @include('rinvex.fort::frontend.alerts.success')
                            @include('rinvex.fort::frontend.alerts.warning')
                            @include('rinvex.fort::frontend.alerts.error')

                            <p class="text-justify">
                                {!! trans('rinvex.fort::form.twofactor.totp_apps') !!}
                            </p>

                            <hr />

                            <div class="row">

                                <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                                    <span class="fa fa-mobile" style="font-size: 8em"></span>
                                </div>

                                <div class="col-md-8 col-sm-8 col-xs-8">
                                    {!! trans('rinvex.fort::form.twofactor.totp_apps_step1') !!}
                                </div>

                            </div>

                            <hr />

                            <div class="row">

                                <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                                    <img src="{{ $qrCode }}" />
                                </div>

                                <div class="col-md-8 col-sm-8 col-xs-8">
                                    {!! trans('rinvex.fort::form.twofactor.totp_apps_step2') !!}

                                    <a class="btn btn-default text-center" role="button" data-toggle="collapse" href="#collapseSecretKey" aria-expanded="false" aria-controls="collapseSecretKey">
                                        {{ trans('rinvex.fort::form.twofactor.totp_apps_step2_button') }}
                                    </a>

                                    <div class="collapse" id="collapseSecretKey">
                                        <hr />
                                        <div class="well">

                                            <p class="small">{{ trans('rinvex.fort::form.twofactor.totp_apps_step2_1') }}</p>
                                            <code>{{ $secret }}</code>
                                            <p class="small">{{ trans('rinvex.fort::form.twofactor.totp_apps_step2_2') }}</p>

                                        </div>
                                    </div>
                                </div>

                            </div>


                            <hr />

                            <div class="row">

                                <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                                    <span class="fa fa-lock fa-5x" style="font-size: 8em"></span>
                                </div>

                                <div class="col-md-8 col-sm-8 col-xs-8">
                                    {!! trans('rinvex.fort::form.twofactor.totp_apps_step3') !!}
                                    <p>
                                        <input id="token" name="token" type="text" class="form-control" value="" placeholder="{{ trans('rinvex.fort::form.account.code') }}" required autofocus>
                                    </p>
                                </div>

                            </div>

                            <hr />

                            @if(array_get($settings, 'totp.enabled'))
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">

                                        <div class="form-group">

                                            <div class="col-md-12">

                                                <div class="text-center">
                                                    <a class="btn btn-default text-center" role="button" data-toggle="collapse" href="#collapse2Example" aria-expanded="false" aria-controls="collapseSecretKey">
                                                        {{ trans('rinvex.fort::form.twofactor.totp_backup_button', ['count' => count(array_get($settings, 'totp.backup'))]) }}
                                                    </a>
                                                </div>

                                                <div class="collapse" id="collapse2Example">

                                                    <hr />

                                                    @if(array_get($settings, 'totp.backup'))
                                                        <div class="panel panel-primary">
                                                            <div class="panel-heading">
                                                                <a class="btn btn-default btn-xs pull-right" style="margin-left: 10px" href="{{ route('rinvex.fort.frontend.account.twofactor.totp.backup') }}">{{ trans('rinvex.fort::form.twofactor.totp_backup_generate') }}</a>
                                                                <h3 class="panel-title">{{ trans('rinvex.fort::form.twofactor.totp_backup_head') }}</h3>
                                                            </div>
                                                            <div class="panel-body">
                                                                {{ trans('rinvex.fort::form.twofactor.totp_backup_body') }}
                                                                <div>

                                                                    {!! trans('rinvex.fort::form.twofactor.totp_backup_notice', ['backup_at' => array_get($settings, 'totp.backup_at')]) !!}

                                                                    <ul class="list-group">
                                                                        @foreach(array_get($settings, 'totp.backup') as $backup)
                                                                            <li class="list-group-item col-xs-6">{{ $backup }}</li>
                                                                        @endforeach
                                                                    </ul>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{ trans('rinvex.fort::form.twofactor.totp_backup_none') }}
                                                    @endif

                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <hr />

                            @endif

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-cog"></i> {{ trans('rinvex.fort::form.twofactor.submit') }}</button>
                                        <button type="reset" class="btn btn-default">{{ trans('rinvex.fort::common.reset') }}</button>
                                    </div>

                                </div>
                            </div>

                        </form>

                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection
