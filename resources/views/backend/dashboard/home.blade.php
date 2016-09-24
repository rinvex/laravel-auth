@extends('layouts.app')

{{-- Main Content --}}
@section('content')

    <style>
        td {
            vertical-align: middle !important;
        }
    </style>

    <div class="container">

        @include('rinvex.fort::frontend.alerts.success')
        @include('rinvex.fort::frontend.alerts.warning')
        @include('rinvex.fort::frontend.alerts.error')

        <div class="row">
            <div class="col-md-12">
                <div class="jumbotron">
                    <h1><i class="fa fa-dashboard"></i> Dashboard</h1>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Recently Registered Users
                                    <span class="pull-right"><a class="btn btn-xs btn-default" href="{{ route('rinvex.fort.backend.users.index') }}" role="button">Manage Users</a></span>
                                </div>
                                <div class="panel-body">

                                    <div class="table-responsive">

                                        <table class="table table-hover" style="margin-bottom: 0">

                                            <thead>
                                                <tr>
                                                    <th style="width: 20%">{{ trans('rinvex.fort::backend/users.name') }}</th>
                                                    <th style="width: 20%">{{ trans('rinvex.fort::backend/users.contact') }}</th>
                                                    <th style="width: 15%">{{ trans('rinvex.fort::backend/users.status.title') }}</th>
                                                    <th style="width: 15%">{{ trans('rinvex.fort::backend/users.date') }}</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @foreach($users as $user)

                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('rinvex.fort.backend.users.show', ['userid' => $user->id]) }}">
                                                                <strong>
                                                                    @if($user->first_name)
                                                                        {{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}
                                                                    @else
                                                                        {{ $user->username }}
                                                                    @endif
                                                                </strong>
                                                                <div class="small ">{{ $user->job_title }}</div>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            <div>{{ $user->email }} @if($user->email_verified)
                                                                    <span title="{{ $user->email_verified_at }}"><i class="fa text-success fa-check"></i></span> @endif
                                                            </div>
                                                            <div>{{ $user->phone }} @if($user->phone_verified)
                                                                    <span title="{{ $user->phone_verified_at }}"><i class="fa text-success fa-check"></i></span> @endif
                                                            </div>
                                                        </td>

                                                        <td>
                                                            @if($user->moderated)
                                                                <span class="label label-success">{{ trans('rinvex.fort::backend/users.status.active') }}</span>
                                                            @else
                                                                <span class="label label-warning">{{ trans('rinvex.fort::backend/users.status.inactive') }}</span>
                                                            @endif
                                                        </td>

                                                        <td class="small">
                                                            <div>{{ trans('rinvex.fort::backend/users.created') }}:
                                                                <time datetime="{{ $user->created_at }}">{{ $user->created_at->format('Y-m-d') }}</time>
                                                            </div>
                                                            <div>{{ trans('rinvex.fort::backend/users.updated') }}:
                                                                <time datetime="{{ $user->updated_at }}">{{ $user->updated_at->format('Y-m-d') }}</time>
                                                            </div>
                                                        </td>

                                                    </tr>

                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">

                            <div class="row">

                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <strong>Statistics</strong>
                                    </div>

                                    <ul class="list-group" style="vertical-align: middle">

                                        @foreach($stats as $key => $num)

                                            <li class="list-group-item" style="vertical-align: middle">
                                                <label class="pull-right badge">{{ $num }}</label>
                                                <a href="{{ route('rinvex.fort.backend.users.index') }}">
                                                    <strong>
                                                        {{ $key }}
                                                    </strong>
                                                </a>
                                            </li>

                                        @endforeach
                                    </ul>

                                </div>

                            </div>

                            <div class="row">

                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <strong>
                                            Online Users ({{ config('rinvex.fort.online.interval') }} mins)
                                            <span class="pull-right">{{ $persistences->count() }}</span>
                                        </strong>
                                    </div>

                                    <ul class="list-group" style="vertical-align: middle">

                                        @foreach($persistences as $persistence)

                                            <li class="list-group-item" style="vertical-align: middle">
                                                <label class="pull-right badge">{{ $persistence->updated_at->diffForHumans() }}</label>
                                                <a href="{{ route('rinvex.fort.backend.users.show', ['userid' => $persistence->user_id]) }}">
                                                    <strong>
                                                        @if($persistence->user->first_name)
                                                            {{ $persistence->user->first_name }} {{ $persistence->user->middle_name }} {{ $persistence->user->last_name }}
                                                        @else
                                                            {{ $persistence->user->username }}
                                                        @endif
                                                    </strong>
                                                    <div class="small ">{{ $persistence->user->job_title }}</div>
                                                </a>
                                            </li>

                                        @endforeach
                                    </ul>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
