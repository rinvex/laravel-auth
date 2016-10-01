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
                    {{ trans('rinvex.fort::backend/abilities.heading') }}
                    <span class="pull-right" style="margin-top: -7px">
                        <a href="{{ route('rinvex.fort.backend.abilities.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a>
                    </span>
                </h4>
            </div>

            {{-- Data --}}
            <div class="panel-body">

                <div class="table-responsive">

                    <table class="table table-hover" style="margin-bottom: 0">

                        <thead>
                            <tr>
                                <th style="width: 30%">{{ trans('rinvex.fort::backend/abilities.title') }}</th>
                                <th style="width: 40%">{{ trans('rinvex.fort::backend/abilities.description') }}</th>
                                <th style="width: 20%">{{ trans('rinvex.fort::backend/abilities.dates') }}</th>
                                <th style="width: 10%" class="text-right">{{ trans('rinvex.fort::backend/abilities.actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($abilities as $ability)

                                <tr>
                                    <td>
                                        <a href="{{ route('rinvex.fort.backend.abilities.show', ['abilityId' => $ability->id]) }}">
                                            <strong>{{ $ability->title }}</strong> <small>({{ $ability->action }})</small>
                                            <div class="small ">{{ $ability->policy }}</div>
                                        </a>
                                    </td>

                                    <td>
                                        {{ $ability->description }}
                                    </td>

                                    <td class="small">
                                        @if($ability->created_at)
                                            <div>
                                                {{ trans('rinvex.fort::backend/abilities.created_at') }}: <time datetime="{{ $ability->created_at }}">{{ $ability->created_at->format('Y-m-d') }}</time>
                                            </div>
                                        @endif
                                        @if($ability->updated_at)
                                            <div>
                                                {{ trans('rinvex.fort::backend/abilities.updated_at') }}: <time datetime="{{ $ability->updated_at }}">{{ $ability->updated_at->format('Y-m-d') }}</time>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="text-right">
                                        <a href="{{ route('rinvex.fort.backend.abilities.edit', ['abilityId' => $ability->id]) }}" class="btn btn-xs btn-default"><i class="fa fa-pencil text-primary"></i></a>
                                        <a href="#" class="btn btn-xs btn-default" data-toggle="modal" data-target="#delete-confirmation" data-href="{{ route('rinvex.fort.backend.abilities.delete', ['abilityId' => $ability->id]) }}" data-item-name="{{ $ability->slug }}"><i class="fa fa-trash-o text-danger"></i></a>
                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

            {{-- Pagination --}}
            @include('rinvex.fort::backend.common.pagination', ['resource' => $abilities])

        </div>

    </div>

@endsection
