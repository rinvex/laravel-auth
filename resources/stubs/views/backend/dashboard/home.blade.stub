{{-- Master Layout --}}
@extends('rinvex/fort::backend/common.layout')

{{-- Page Title --}}
@section('title')
    {{ config('app.name') }} » {{ trans('rinvex/fort::forms.common.dashboard') }}
@stop

{{-- Main Content --}}
@section('content')

    <style>
        td {
            vertical-align: middle !important;
        }
    </style>

    <div class="container">

        @include('rinvex/fort::frontend/alerts.success')
        @include('rinvex/fort::frontend/alerts.warning')
        @include('rinvex/fort::frontend/alerts.error')

        <div class="row">
            <div class="col-md-12">
                <div class="jumbotron">
                    <h1><i class="fa fa-dashboard"></i> {{ trans('rinvex/fort::forms.common.dashboard') }}</h1>

                    <div class="row">
                        <div class="col-md-8">
                            <section class="panel panel-default">
                                <header class="panel-heading">
                                    <a class="btn btn-xs btn-default" href="{{ route('rinvex.fort.backend.users.index') }}" role="button">{{ trans('rinvex/fort::forms.common.recent_registered') }}</a>
                                </header>
                                <div class="panel-body">

                                    <div class="table-responsive">

                                        <table class="table table-hover" style="margin-bottom: 0">

                                            <thead>
                                                <tr>
                                                    <th style="width: 20%">{{ trans('rinvex/fort::forms.common.name') }}</th>
                                                    <th style="width: 20%">{{ trans('rinvex/fort::forms.common.contact') }}</th>
                                                    <th style="width: 15%">{{ trans('rinvex/fort::forms.common.status') }}</th>
                                                    <th style="width: 15%">{{ trans('rinvex/fort::forms.common.created_at') }}</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @foreach($users as $user)

                                                    <tr>
                                                        <td>
                                                            @can('update-users', $user) <a href="{{ route('rinvex.fort.backend.users.edit', ['user' => $user]) }}"> @endcan
                                                                <strong>
                                                                    @if($user->first_name)
                                                                        {{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}
                                                                    @else
                                                                        {{ $user->username }}
                                                                    @endif
                                                                </strong>
                                                                <div class="small ">{{ $user->job_title }}</div>
                                                            @can('update-users', $user) </a> @endcan
                                                        </td>

                                                        <td>
                                                            <div>
                                                                {{ $user->email }}
                                                                @if($user->email_verified)
                                                                    <span title="{{ $user->email_verified_at }}"><i class="fa text-success fa-check"></i></span>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                {{ $user->phone }}
                                                                @if($user->phone_verified)
                                                                    <span title="{{ $user->phone_verified_at }}"><i class="fa text-success fa-check"></i></span>
                                                                @endif
                                                            </div>
                                                        </td>

                                                        <td>
                                                            @if($user->active)
                                                                <span class="label label-success">{{ trans('rinvex/fort::forms.common.active') }}</span>
                                                            @else
                                                                <span class="label label-warning">{{ trans('rinvex/fort::forms.common.inactive') }}</span>
                                                            @endif
                                                        </td>

                                                        <td class="small">
                                                            @if($user->created_at)
                                                                <div><time datetime="{{ $user->created_at }}">{{ $user->created_at->format('Y-m-d') }}</time></div>
                                                            @endif
                                                        </td>

                                                    </tr>

                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                            </section>
                        </div>

                        <div class="col-md-4">

                            <div class="row">

                                <section class="panel panel-default">
                                    <header class="panel-heading">
                                        <strong>{{ trans('rinvex/fort::forms.common.statistics') }}</strong>
                                    </header>

                                    <ul class="list-group" style="vertical-align: middle">

                                        @foreach($stats as $key => $num)

                                            <li class="list-group-item" style="vertical-align: middle">
                                                {{ Form::label('stats_number', $num, ['class' => 'pull-right badge']) }}
                                                <a href="{{ route('rinvex.fort.backend.'.$key.'.index') }}">
                                                    <strong>
                                                        {{ ucfirst($key) }}
                                                    </strong>
                                                </a>
                                            </li>

                                        @endforeach
                                    </ul>

                                </section>

                            </div>

                            <div class="row">

                                <section class="panel panel-default">
                                    <header class="panel-heading">
                                        <h4>
                                            {{ trans('rinvex/fort::forms.common.online_users', ['mins' => config('rinvex.fort.online.interval')]) }}
                                            <span class="pull-right">{{ $persistences->count() }}</span>
                                        </h4>
                                    </header>

                                    <ul class="list-group" style="vertical-align: middle">

                                        @foreach($persistences as $persistence)

                                            <li class="list-group-item" style="vertical-align: middle">
                                                <span class="pull-right">
                                                    @if($persistence->user_id == $currentUser->id)<span class="label label-info">{{ trans('rinvex/fort::forms.common.you') }}</span> @endif
                                                    <span class="badge">{{ $persistence->updated_at->diffForHumans() }}</span>
                                                </span>
                                                @can('update-users', $user) <a href="{{ route('rinvex.fort.backend.users.edit', ['user' => $persistence->user_id]) }}"> @endcan
                                                    <strong>
                                                        @if($persistence->user->first_name)
                                                            {{ $persistence->user->first_name }} {{ $persistence->user->middle_name }} {{ $persistence->user->last_name }}
                                                        @else
                                                            {{ $persistence->user->username }}
                                                        @endif
                                                    </strong>
                                                    <div class="small ">{{ $persistence->user->job_title }}</div>
                                                @can('update-users', $user) </a> @endcan
                                            </li>

                                        @endforeach
                                    </ul>

                                </section>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
