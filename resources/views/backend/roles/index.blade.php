@extends('rinvex.fort::backend.common.layout')

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
        @include('rinvex.fort::backend.common.confirm-modal', ['type' => 'role'])

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
                                        <a href="{{ route('rinvex.fort.backend.roles.show', ['roleId' => $role->id]) }}">
                                            <strong>{{ $role->title }}</strong>
                                            <div class="small ">{{ $role->slug }}</div>
                                        </a>
                                    </td>

                                    <td>
                                        {{ $role->description }}
                                    </td>

                                    <td class="small">
                                        @if($role->created_at)
                                            <div>
                                                {{ trans('rinvex.fort::backend/roles.created_at') }}: <time datetime="{{ $role->created_at }}">{{ $role->created_at->format('Y-m-d') }}</time>
                                            </div>
                                        @endif
                                        @if($role->updated_at)
                                            <div>
                                                {{ trans('rinvex.fort::backend/roles.updated_at') }}: <time datetime="{{ $role->updated_at }}">{{ $role->updated_at->format('Y-m-d') }}</time>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="text-right">
                                        <a href="{{ route('rinvex.fort.backend.roles.edit', ['roleId' => $role->id]) }}" class="btn btn-xs btn-default"><i class="fa fa-pencil text-primary"></i></a>
                                        <a href="#" class="btn btn-xs btn-default" data-toggle="modal" data-target="#delete-confirmation" data-href="{{ route('rinvex.fort.backend.roles.delete', ['roleId' => $role->id]) }}" data-item-name="{{ $role->slug }}"><i class="fa fa-trash-o text-danger"></i></a>
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
