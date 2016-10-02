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
        @include('rinvex.fort::backend.common.confirm-modal', ['type' => 'ability'])

        <div class="panel panel-default">

            {{-- Heading --}}
            <div class="panel-heading">
                <h4>
                    <a href="{{ route('rinvex.fort.backend.abilities.index') }}">{{ trans('rinvex.fort::backend/abilities.heading') }}</a> / {{ trans('rinvex.fort::backend/abilities.view') }} » {{ $ability->slug }}
                    <span class="pull-right" style="margin-top: -7px">
                        <a href="{{ route('rinvex.fort.backend.abilities.edit', ['abilityId' => $ability->id]) }}" class="btn btn-default"><i class="fa fa-pencil text-primary"></i></a>
                        <a href="{{ route('rinvex.fort.backend.abilities.copy', ['ability' => $ability->id]) }}" class="btn btn-default"><i class="fa fa-copy text-success"></i></a>
                        <a href="#" class="btn btn-default" data-toggle="modal" data-target="#delete-confirmation" data-item-href="{{ route('rinvex.fort.backend.abilities.delete', ['abilityId' => $ability->id]) }}" data-item-name="{{ $ability->slug }}"><i class="fa fa-trash-o text-danger"></i></a>
                        <a href="{{ route('rinvex.fort.backend.abilities.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a>
                    </span>
                </h4>
            </div>

            {{-- Data --}}

            <div class="panel-body">

                <div class="row">
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex.fort::backend/abilities.title') }}</strong>: @if($ability->title) {{ $ability->title }} @else N/A @endif
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex.fort::backend/abilities.slug') }}</strong>: @if($ability->slug) {{ $ability->slug }} @else N/A @endif
                    </div>
                    <div class="col-md-4">
                        <strong>{{ trans('rinvex.fort::backend/abilities.policy') }}</strong>: @if($ability->policy) {{ $ability->policy }} @else N/A @endif
                    </div>
                </div>

                @if($ability->description)

                    <div class="row">
                        <div class="col-md-12">
                            <strong>{{ trans('rinvex.fort::backend/abilities.description') }}</strong>: {{ $ability->description }}
                        </div>
                    </div>

                @endif

                <div class="row">
                    <div class="col-md-12">
                        <strong>{{ trans('rinvex.fort::backend/abilities.roles') }}</strong>:
                        @forelse($ability->roles->pluck('title', 'id') as $roleId => $role)
                            <a href="{{ route('rinvex.fort.backend.roles.show', ['role' => $roleId]) }}" class="label btn-xs label-info">{{ $role }}</a>
                        @empty
                            <span>N/A</span>
                        @endforelse
                    </div>
                </div>

            </div>


            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-12">
                        @if($ability->created_at)
                            <small><strong>{{ trans('rinvex.fort::backend/abilities.created_at') }}:</strong>
                                <time datetime="{{ $ability->created_at }}">{{ $ability->created_at->format('Y-m-d') }}</time>
                            </small>
                        @endif
                        @if($ability->updated_at)
                            <small><strong>{{ trans('rinvex.fort::backend/abilities.updated_at') }}:</strong>
                                <time datetime="{{ $ability->updated_at }}">{{ $ability->updated_at->format('Y-m-d') }}</time>
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
