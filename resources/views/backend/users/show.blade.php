@extends('layouts.app')

{{-- Main Content --}}

@section('content')

    <style>
        td {
            vertical-align: middle !important;
        }
    </style>

    <div class="container">

        <div class="panel panel-default">

            {{-- Heading --}}
            <div class="panel-heading">
                <h4>
                    {!! trans('rinvex.fort::backend/users.show', ['user' => $user->name]) !!} @if($user->job_title) <small>({{ $user->job_title }})</small> @endif
                    <span class="pull-right" style="margin-top: -7px">
                        <a href="{{ route('rinvex.fort.backend.users.edit', ['user' => $user->id]) }}" class="btn btn-default" title="{{ trans('rinvex.fort::backend/users.edit', ['user' => $user->slug]) }}"><i class="fa fa-pencil"></i></a>
                        <a href="{{ route('rinvex.fort.backend.users.create') }}" class="btn btn-default" title="{{ trans('rinvex.fort::backend/users.create') }}"><i class="fa fa-plus"></i></a>
                    </span>
                </h4>
            </div>

            {{-- Data --}}
            <div class="panel-body">

                <div class="row">
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex.fort::backend/users.username') }}</strong>: {{ $user->username }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex.fort::backend/users.email') }}</strong>: {{ $user->email }} @if($user->email_verified) <span title="{{ $user->email_verified_at }}"><i class="fa text-success fa-check"></i></span> @endif
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex.fort::backend/users.phone') }}</strong>: {{ $user->phone }} @if($user->phone_verified) <span title="{{ $user->phone_verified_at }}"><i class="fa text-success fa-check"></i></span> @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex.fort::backend/users.country') }}</strong>: {{ $user->country }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex.fort::backend/users.gender') }}</strong>: {{ ucfirst($user->gender) }}
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex.fort::backend/users.birthdate') }}</strong>: {{ $user->birthdate->toDateString() }} ({{ $user->birthdate->age }} years old)
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <strong>{{ trans('rinvex.fort::backend/users.roles') }}</strong>:
                        @foreach($user->roles->pluck('title') as $role)
                            <span class="label btn-xs label-info">{{ $role }}</span>
                        @endforeach
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
                                                <td class="text-center">{!! $user->all_abilities->where('resource', $resource)->contains('action', $action) ? '<i class="text-success fa fa-check"></i>' : (! $abilities->where('resource', $resource)->where('action', $action)->isEmpty() ? '<i class="text-danger fa fa-times"></i>' : '') !!}</td>
                                            @endforeach

                                            <td>
                                                @foreach($abilities->diff($abilities->whereIn('action', $actions)) as $special)
                                                    <strong>{{ ucfirst($special->action) }}</strong> {!! $user->all_abilities->where('resource', $resource)->contains('action', $special->action) ? '<i class="text-success fa fa-check"></i>' : '<i class="text-danger fa fa-times"></i>' !!}
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
                            <small><strong>{{ trans('rinvex.fort::backend/users.created_at') }}:</strong>
                                <time datetime="{{ $user->created_at }}">{{ $user->created_at->format('Y-m-d') }}</time>
                            </small>
                        @endif
                        @if($user->updated_at)
                            <small><strong>{{ trans('rinvex.fort::backend/users.updated_at') }}:</strong>
                                <time datetime="{{ $user->updated_at }}">{{ $user->updated_at->format('Y-m-d') }}</time>
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
