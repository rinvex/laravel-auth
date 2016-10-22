{{-- Master Layout --}}
@extends('rinvex/fort::backend/common.layout')

{{-- Page Title --}}
@section('title')
    @parent
    » {{ trans('rinvex/fort::backend/users.heading') }}
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
        @include('rinvex/fort::backend/common.confirm-modal', ['type' => 'user'])

        <div class="panel panel-default">

            {{-- Heading --}}
            <div class="panel-heading">
                <h4>
                    {{ trans('rinvex/fort::backend/users.heading') }}
                    @can('create-user')
                        <span class="pull-right" style="margin-top: -7px">
                            <a href="{{ route('rinvex.fort.backend.users.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a>
                        </span>
                    @endcan
                </h4>
            </div>

            {{-- Data --}}
            <div class="panel-body">

                <div class="table-responsive">

                    <table class="table table-hover" style="margin-bottom: 0">

                        <thead>
                            <tr>
                                <th style="width: 20%">{{ trans('rinvex/fort::backend/users.name') }}</th>
                                <th style="width: 20%">{{ trans('rinvex/fort::backend/users.contact') }}</th>
                                <th style="width: 20%">{{ trans('rinvex/fort::backend/users.roles.title') }}</th>
                                <th style="width: 15%">{{ trans('rinvex/fort::backend/users.status.title') }}</th>
                                <th style="width: 15%">{{ trans('rinvex/fort::backend/users.dates') }}</th>
                                <th style="width: 10%" class="text-right">{{ trans('rinvex/fort::backend/users.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($users as $user)

                                <tr>
                                    <td>
                                        @can('view-user', $user) <a href="{{ route('rinvex.fort.backend.users.show', ['user' => $user]) }}"> @endcan
                                            <strong>
                                                @if($user->first_name)
                                                    {{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}
                                                @else
                                                    {{ $user->username }}
                                                @endif
                                            </strong>
                                            <div class="small ">{{ $user->job_title }}</div>
                                        @can('view-user', $user) </a> @endcan
                                    </td>

                                    <td>
                                        <div>
                                            {{ $user->email }} @if($user->email_verified) <span title="{{ $user->email_verified_at }}"><i class="fa text-success fa-check"></i></span> @endif
                                        </div>
                                        <div>
                                            {{ $user->phone }} @if($user->phone_verified) <span title="{{ $user->phone_verified_at }}"><i class="fa text-success fa-check"></i></span> @endif
                                        </div>
                                    </td>

                                    <td>
                                        @foreach($user->roles->pluck('title', 'id') as $roleId => $role)
                                            @can('view-role', $role) <a href="{{ route('rinvex.fort.backend.roles.show', ['role' => $roleId]) }}" class="label btn-xs label-info">{{ $role }}</a> @else {{ $role }} @endcan
                                        @endforeach
                                    </td>

                                    <td>
                                        @if($user->active)
                                            <span class="label label-success">{{ trans('rinvex/fort::backend/users.status.active') }}</span>
                                        @else
                                            <span class="label label-warning">{{ trans('rinvex/fort::backend/users.status.inactive') }}</span>
                                        @endif
                                    </td>

                                    <td class="small">
                                        @if($user->created_at)
                                            <div>
                                                {{ trans('rinvex/fort::backend/users.created_at') }}: <time datetime="{{ $user->created_at }}">{{ $user->created_at->format('Y-m-d') }}</time>
                                            </div>
                                        @endif
                                        @if($user->updated_at)
                                            <div>
                                                {{ trans('rinvex/fort::backend/users.updated_at') }}: <time datetime="{{ $user->updated_at }}">{{ $user->updated_at->format('Y-m-d') }}</time>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="text-right">
                                        @can('update-user', $user) <a href="{{ route('rinvex.fort.backend.users.edit', ['user' => $user]) }}" class="btn btn-xs btn-default"><i class="fa fa-pencil text-primary"></i></a> @endcan
                                        @can('delete-user', $user) <a href="#" class="btn btn-xs btn-default" data-toggle="modal" data-target="#delete-confirmation" data-item-href="{{ route('rinvex.fort.backend.users.delete', ['user' => $user]) }}" data-item-name="{{ $user->username }}"><i class="fa fa-trash-o text-danger"></i></a> @endcan
                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

            {{-- Pagination --}}
            @include('rinvex/fort::backend/common.pagination', ['resource' => $users])

        </div>

    </div>

@endsection
