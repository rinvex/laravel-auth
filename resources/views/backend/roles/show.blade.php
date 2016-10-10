@extends('rinvex/fort::backend.common.layout')

{{-- Main Content --}}

@section('content')

    <style>
        td {
            vertical-align: middle !important;
        }
    </style>

    <div class="container">

        @include('rinvex/fort::frontend.alerts.success')
        @include('rinvex/fort::frontend.alerts.warning')
        @include('rinvex/fort::frontend.alerts.error')
        @include('rinvex/fort::backend.common.confirm-modal', ['type' => 'role'])

        <div class="panel panel-default">

            {{-- Heading --}}
            <div class="panel-heading">
                <h4>
                    <a href="{{ route('rinvex.fort.backend.roles.index') }}">{{ trans('rinvex/fort::backend/roles.heading') }}</a> / {{ trans('rinvex/fort::backend/roles.view') }} » {{ $role->slug }}
                    <span class="pull-right" style="margin-top: -7px">
                        <a href="{{ route('rinvex.fort.backend.roles.edit', ['role' => $role]) }}" class="btn btn-default"><i class="fa fa-pencil text-primary"></i></a>
                        <a href="{{ route('rinvex.fort.backend.roles.copy', ['role' => $role]) }}" class="btn btn-default"><i class="fa fa-copy text-success"></i></a>
                        <a href="#" class="btn btn-default" data-toggle="modal" data-target="#delete-confirmation" data-item-href="{{ route('rinvex.fort.backend.roles.delete', ['role' => $role]) }}" data-item-name="{{ $role->slug }}"><i class="fa fa-trash-o text-danger"></i></a>
                        <a href="{{ route('rinvex.fort.backend.roles.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a>
                    </span>
                </h4>
            </div>

            {{-- Data --}}
            <div class="panel-body">

                <div class="row">
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex/fort::backend/roles.title') }}</strong>: @if($role->title) {{ $role->title }} @else N/A @endif
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex/fort::backend/roles.slug') }}</strong>: @if($role->slug) {{ $role->slug }} @else N/A @endif
                    </div>
                </div>

               @if($role->description)
                    <div class="row">
                        <div class="col-md-12">
                            <strong>{{ trans('rinvex/fort::backend/roles.description') }}</strong>: {{ $role->description }}
                        </div>
                    </div>
                @endif

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
                                                <td class="text-center">{!! $role->abilities->where('resource', $resource)->contains('action', $action) ? '<i class="text-success fa fa-check"></i>' : (! $abilities->where('resource', $resource)->where('action', $action)->isEmpty() ? '<i class="text-danger fa fa-times"></i>' : '') !!}</td>
                                            @endforeach

                                            <td>
                                                @foreach($abilities->diff($abilities->whereIn('action', $actions)) as $special)
                                                    <strong>{{ ucfirst($special->action) }}</strong> {!! $role->abilities->where('resource', $resource)->contains('action', $special->action) ? '<i class="text-success fa fa-check"></i>' : '<i class="text-danger fa fa-times"></i>' !!}
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
                        @if($role->created_at)
                            <small><strong>{{ trans('rinvex/fort::backend/roles.created_at') }}:</strong>
                                <time datetime="{{ $role->created_at }}">{{ $role->created_at->format('Y-m-d') }}</time>
                            </small>
                        @endif
                        @if($role->updated_at)
                            <small><strong>{{ trans('rinvex/fort::backend/roles.updated_at') }}:</strong>
                                <time datetime="{{ $role->updated_at }}">{{ $role->updated_at->format('Y-m-d') }}</time>
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
