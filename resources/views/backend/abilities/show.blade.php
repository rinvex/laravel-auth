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
                    {!! trans('rinvex.fort::backend/abilities.show', ['ability' => $ability->title, 'slug' => $ability->slug]) !!}
                    <span class="pull-right" style="margin-top: -7px">
                        <a href="{{ route('rinvex.fort.backend.abilities.edit', ['ability' => $ability->id]) }}" class="btn btn-default" title="{{ trans('rinvex.fort::backend/abilities.edit', ['ability' => $ability->slug]) }}"><i class="fa fa-pencil"></i></a>
                        <a href="{{ route('rinvex.fort.backend.abilities.create') }}" class="btn btn-default" title="{{ trans('rinvex.fort::backend/abilities.create') }}"><i class="fa fa-plus"></i></a>
                    </span>
                </h4>
            </div>

            {{-- Data --}}
            @if($ability->description)

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-12">
                            {{ $ability->description }}
                            <hr />
                        </div>
                    </div>

                </div>

            @endif

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
