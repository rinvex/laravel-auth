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
        @if($ability->exists)
            @include('rinvex/fort::backend/common.confirm-modal', ['type' => 'ability'])
        @endif

        @if ($action === 'update')
            {{ Form::model($ability, ['route' => ['rinvex.fort.backend.abilities.update', $ability], 'method' => 'put']) }}
            {{ Form::hidden('id') }}
        @else
            {{ Form::model($ability, ['route' => ['rinvex.fort.backend.abilities.store']]) }}
        @endif

            <div class="panel panel-default">

                {{-- Heading --}}
                <div class="panel-heading">
                    <h4>
                        <a href="{{ route('rinvex.fort.backend.abilities.index') }}">{{ trans('rinvex/fort::backend/abilities.heading') }}</a> / {{ trans('rinvex/fort::backend/abilities.'.$mode) }} @if($ability->exists) » {{ $ability->slug }} @endif
                        @if($ability->exists && $mode !== 'copy')
                            <span class="pull-right" style="margin-top: -7px">
                                <a href="{{ route('rinvex.fort.backend.abilities.show', ['ability' => $ability]) }}" class="btn btn-default"><i class="fa fa-eye text-primary"></i></a>
                                <a href="{{ route('rinvex.fort.backend.abilities.copy', ['ability' => $ability]) }}" class="btn btn-default"><i class="fa fa-copy text-success"></i></a>
                                <a href="#" class="btn btn-default" data-toggle="modal" data-target="#delete-confirmation" data-item-href="{{ route('rinvex.fort.backend.abilities.delete', ['ability' => $ability]) }}" data-item-name="{{ $ability->slug }}"><i class="fa fa-trash-o text-danger"></i></a>
                                <a href="{{ route('rinvex.fort.backend.abilities.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a>
                            </span>
                        @endif
                    </h4>
                </div>

                {{-- Data --}}
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-6">

                            {{-- Title --}}
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                {{ Form::label('title', trans('rinvex/fort::backend/abilities.title'), ['class' => 'control-label']) }}
                                {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/abilities.title'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-6">

                            {{-- Policy --}}
                            <div class="form-group{{ $errors->has('policy') ? ' has-error' : '' }}">
                                {{ Form::label('policy', trans('rinvex/fort::backend/abilities.policy'), ['class' => 'control-label']) }}
                                {{ Form::text('policy', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/abilities.policy')]) }}

                                @if ($errors->has('policy'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('policy') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">

                            {{-- Action --}}
                            <div class="form-group{{ $errors->has('action') ? ' has-error' : '' }}">
                                {{ Form::label('action', trans('rinvex/fort::backend/abilities.action'), ['class' => 'control-label']) }}
                                {{ Form::text('action', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/abilities.action'), 'required' => 'required']) }}

                                @if ($errors->has('action'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('action') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-6">

                            {{-- Resource --}}
                            <div class="form-group{{ $errors->has('resource') ? ' has-error' : '' }}">
                                {{ Form::label('resource', trans('rinvex/fort::backend/abilities.resource'), ['class' => 'control-label']) }}
                                {{ Form::text('resource', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/abilities.resource'), 'required' => 'required']) }}

                                @if ($errors->has('resource'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('resource') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">

                            {{-- Description --}}
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                {{ Form::label('description', trans('rinvex/fort::backend/abilities.description'), ['class' => 'control-label']) }}
                                {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/abilities.description'), 'rows' => 3]) }}

                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                    </div>

                </div>

                <div class="panel-footer">
                    <div class="row">
                        <div class="col-md-12">

                            @if($mode !== 'copy')
                                @if($ability->created_at)
                                    <small><strong>{{ trans('rinvex/fort::backend/abilities.created_at') }}:</strong>
                                        <time datetime="{{ $ability->created_at }}">{{ $ability->created_at->format('Y-m-d') }}</time>
                                    </small>
                                @endif
                                @if($ability->updated_at)
                                    <small><strong>{{ trans('rinvex/fort::backend/abilities.updated_at') }}:</strong>
                                        <time datetime="{{ $ability->updated_at }}">{{ $ability->updated_at->format('Y-m-d') }}</time>
                                    </small>
                                @endif
                            @endif

                            <div class="pull-right">
                                {{ Form::reset(trans('rinvex/fort::backend/common.reset'), ['class' => 'btn btn-default']) }}
                                {{ Form::submit(trans('rinvex/fort::backend/common.submit'), ['class' => 'btn btn-primary']) }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        {{ Form::close() }}

    </div>

@endsection
