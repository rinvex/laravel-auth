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

        <div class="panel panel-default">

            {{-- Heading --}}
            <div class="panel-heading">
                <h4>
                    {{ trans('rinvex.fort::backend/roles.heading') }}
                    <span class="pull-right" style="margin-top: -7px">
                        <a href="{{ route('rinvex.fort.backend.roles.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a>
                    </span>
                </h4>
            </div>

            {{-- Data --}}
            <div class="panel-body">

                <div class="table-responsive">

                    <table class="table table-hover" style="margin-bottom: 0">

                        <thead>
                            <tr>
                                <th style="width: 30%">{{ trans('rinvex.fort::backend/roles.title') }}</th>
                                <th style="width: 40%">{{ trans('rinvex.fort::backend/roles.description') }}</th>
                                <th style="width: 20%">{{ trans('rinvex.fort::backend/roles.dates') }}</th>
                                <th style="width: 10%" class="text-right">{{ trans('rinvex.fort::backend/roles.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($roles as $role)

                                <tr>
                                    <td>
                                        <a href="{{ route('rinvex.fort.backend.roles.show', ['role' => $role->id]) }}">
                                            <strong>{{ $role->title }}</strong>
                                            <div class="small ">{{ $role->slug }}</div>
                                        </a>
                                    </td>

                                    <td>
                                        {{ $role->description }}
                                    </td>

                                    <td class="small">
                                        @if($role->created_at)
                                            <div>{{ trans('rinvex.fort::backend/roles.created') }}:
                                                <time datetime="{{ $role->created_at }}">{{ $role->created_at->format('Y-m-d') }}</time>
                                            </div>
                                        @endif
                                        @if($role->updated_at)
                                            <div>{{ trans('rinvex.fort::backend/roles.updated') }}:
                                                <time datetime="{{ $role->updated_at }}">{{ $role->updated_at->format('Y-m-d') }}</time>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="text-right">
                                        <a href="{{ route('rinvex.fort.backend.roles.edit', ['role' => $role->id]) }}" class="btn btn-xs btn-default" title="{{ trans('rinvex.fort::backend/roles.edit', ['role' => $role->slug]) }}"><i class="fa fa-pencil text-primary"></i></a>
                                        <a href="{{ route('rinvex.fort.backend.roles.delete', ['role' => $role->id]) }}" class="btn btn-xs btn-default" title="{{ trans('rinvex.fort::backend/roles.delete', ['role' => $role->slug]) }}" onclick="event.preventDefault(); document.getElementById('role-delete-form-{{ $role->id }}').submit();"><i class="fa fa-trash-o text-danger"></i></a>
                                        <form id="role-delete-form-{{ $role->id }}" action="{{ route('rinvex.fort.backend.roles.delete', ['role' => $role->id]) }}" method="POST" style="display: none;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            {{ csrf_field() }}
                                        </form>
                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

            {{-- Pagination --}}
            @include('rinvex.fort::backend.common.pagination', ['resource' => $roles])

        </div>

    </div>

@endsection
