@extends('layouts.app')

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <section class="panel panel-default">

                    <header class="panel-heading">
                        {{ trans('rinvex.fort::form.account.active_sessions') }}
                    </header>

                    <div class="panel-body">

                        @include('rinvex.fort::frontend.alerts.success')
                        @include('rinvex.fort::frontend.alerts.warning')
                        @include('rinvex.fort::frontend.alerts.error')

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">

                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                                    @foreach($currentUser->persistences as $persistence)

                                        <div class="panel panel-default">

                                            <div class="panel-heading" role="tab" id="heading-{{ $persistence->token }}">

                                                <div class="row">

                                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-{{ $persistence->token }}" aria-expanded="false" aria-controls="collapse-{{ $persistence->token }}">

                                                        <div class="col-md-11 col-sm-11 col-xs-11">

                                                            <span class="label label-info">{{ $persistence->created_at->format('F d, Y - h:ia') }} <span style="background-color: #428bca; border-radius: 0 3px 3px 0; margin-right: -6px; padding: 2px 4px 3px;">{{ $persistence->created_at->diffForHumans() }}</span></span>
                                                            @if ($persistence->token === request()->session()->getId())<span class="label label-success">{{ trans('rinvex.fort::form.account.you') }}</span>@endif
                                                            <span class="badge pull-right">{{ $persistence->ip }}</span>

                                                        </div>

                                                    </a>

                                                    <div class="col-md-1 col-sm-1 col-xs-1">
                                                        <a class="btn btn-danger btn-xs" data-toggle="modal" data-target="#confirmSingle-{{ $persistence->token }}"><i class="fa fa-remove"></i></a>
                                                    </div>

                                                </div>

                                            </div>

                                            @if($persistence->agent)

                                                <div id="collapse-{{ $persistence->token }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-{{ $persistence->token }}">
                                                    <div class="panel-body">
                                                        <pre>{{ $persistence->agent }}</pre>
                                                    </div>
                                                </div>

                                            @endif

                                        </div>

                                        <!-- Modal: Confirm Single -->
                                        <div class="modal fade" id="confirmSingle-{{ $persistence->token }}" tabindex="-1" role="dialog" aria-labelledby="confirmSingleLabel{{ $persistence->token }}">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="confirmSingleLabel{{ $persistence->token }}">{{ trans('rinvex.fort::form.sessions.flush_single') }}</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        {!! trans('rinvex.fort::form.sessions.flush_single_notice') !!}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('rinvex.fort::form.common.close') }}</button>
                                                        <a role="button" class="btn btn-danger" href="{{ route('rinvex.fort.frontend.account.sessions.flush', ['token' => $persistence->token]) }}"><i class="fa fa-remove"></i> {{ trans('rinvex.fort::form.sessions.flush_single') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach

                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">

                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmAll"><i class="fa fa-remove"></i> {{ trans('rinvex.fort::form.sessions.flush_all') }}</button>

                            </div>
                        </div>

                        <!-- Modal: Confirm All -->
                        <div class="modal fade" id="confirmAll" tabindex="-1" role="dialog" aria-labelledby="confirmAllLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="confirmAllLabel">{{ trans('rinvex.fort::form.sessions.flush_all') }}</h4>
                                    </div>
                                    <div class="modal-body">
                                        {!! trans('rinvex.fort::form.sessions.flush_all_notice') !!}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('rinvex.fort::form.common.close') }}</button>
                                        <a role="button" class="btn btn-danger" href="{{ route('rinvex.fort.frontend.account.sessions.flushall', ['confirm' => true]) }}"><i class="fa fa-remove"></i> {{ trans('rinvex.fort::form.sessions.flush_all') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </section>

            </div>
        </div>
    </div>

@endsection
