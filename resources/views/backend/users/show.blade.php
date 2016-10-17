@extends('rinvex/fort::backend/common.layout')

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
                    <a href="{{ route('rinvex.fort.backend.users.index') }}">{{ trans('rinvex/fort::backend/users.heading') }}</a> / {{ trans('rinvex/fort::backend/users.view') }} » {{ $user->username }}
                    <span class="pull-right" style="margin-top: -7px">
                        <a href="{{ route('rinvex.fort.backend.users.edit', ['user' => $user]) }}" class="btn btn-default"><i class="fa fa-pencil text-primary"></i></a>
                        <a href="{{ route('rinvex.fort.backend.users.copy', ['user' => $user]) }}" class="btn btn-default"><i class="fa fa-copy text-primary"></i></a>
                        <a href="#" class="btn btn-default" data-toggle="modal" data-target="#delete-confirmation" data-item-href="{{ route('rinvex.fort.backend.users.delete', ['user' => $user]) }}" data-item-name="{{ $user->username }}"><i class="fa fa-trash-o text-danger"></i></a>
                        <a href="{{ route('rinvex.fort.backend.users.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a>
                    </span>
                </h4>
            </div>

            {{-- Data --}}
            <div class="panel-body">

                <div class="row">
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex/fort::backend/users.name') }}</strong>: @if($user->name) {{ $user->name }} @else N/A @endif
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex/fort::backend/users.job_title') }}</strong>: @if($user->job_title) {{ $user->job_title }} @else N/A @endif
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex/fort::backend/users.status.title') }}</strong>:
                        @if($user->active)
                            <span class="label label-success">{{ trans('rinvex/fort::backend/users.status.active') }}</span>
                        @else
                            <span class="label label-warning">{{ trans('rinvex/fort::backend/users.status.inactive') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex/fort::backend/users.username') }}</strong>: {{ $user->username }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex/fort::backend/users.email') }}</strong>: {{ $user->email }} @if($user->email_verified) <span title="{{ $user->email_verified_at }}"><i class="fa text-success fa-check"></i></span> @endif
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex/fort::backend/users.phone') }}</strong>: @if($phone) +{{ $phone }} @if($user->phone_verified) <span title="{{ $user->phone_verified_at }}"><i class="fa text-success fa-check"></i></span> @endif @else N/A @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex/fort::backend/users.country.title') }}</strong>: @if($country) {{ $country }} @else N/A @endif
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex/fort::backend/users.gender.title') }}</strong>: {{ ucfirst($user->gender) }} @if(in_array($user->gender, ['male', 'female'])) <i class="fa fa-{{ $user->gender }}"></i> @endif
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex/fort::backend/users.birthdate') }}</strong>: @if($user->birthdate) {{ $user->birthdate->toDateString() }} ({{ $user->birthdate->age }} years old) @else N/A @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <strong>{{ trans('rinvex/fort::backend/users.roles.title') }}</strong>:
                        @forelse($user->roles->pluck('title', 'id') as $roleId => $role)
                            <a href="{{ route('rinvex.fort.backend.roles.show', ['role' => $roleId]) }}" class="label btn-xs label-info">{{ $role }}</a>
                        @empty
                            <span>N/A</span>
                        @endforelse
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">

                            <table class="table table-hover" style="margin-bottom: 0">

                                <thead>
                                    <tr>
                                        @foreach($columns as $column)
                                            <th class="text-center">{{ ucfirst($column) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($resources as $resource => $abilities)

                                        <tr @if(in_array($resource, ['global', 'dashboard'])) class="active" @endif>

                                            <td>
                                                <strong>{{ ucfirst($resource) }}</strong>
                                            </td>

                                            @foreach($actions as $action)
                                                <td class="text-center">{!! $user->allAbilities->where('resource', $resource)->contains('action', $action) ? '<i class="text-success fa fa-check"></i>' : (! $abilities->where('resource', $resource)->where('action', $action)->isEmpty() ? '<i class="text-danger fa fa-times"></i>' : '') !!}</td>
                                            @endforeach

                                            <td>
                                                @foreach($abilities->diff($abilities->whereIn('action', $actions)) as $special)
                                                    <strong>{{ ucfirst($special->action) }}</strong> {!! $user->allAbilities->where('resource', $resource)->contains('action', $special->action) ? '<i class="text-success fa fa-check"></i>' : '<i class="text-danger fa fa-times"></i>' !!}
                                                    <br />
                                                @endforeach
                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>
                </div>

            </div>

            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-12">
                        @if($user->created_at)
                            <small><strong>{{ trans('rinvex/fort::backend/users.created_at') }}:</strong>
                                <time datetime="{{ $user->created_at }}">{{ $user->created_at->format('Y-m-d') }}</time>
                            </small>
                        @endif
                        @if($user->updated_at)
                            <small><strong>{{ trans('rinvex/fort::backend/users.updated_at') }}:</strong>
                                <time datetime="{{ $user->updated_at }}">{{ $user->updated_at->format('Y-m-d') }}</time>
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
