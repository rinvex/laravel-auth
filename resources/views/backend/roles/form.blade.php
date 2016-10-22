{{-- Master Layout --}}
@extends('rinvex/fort::backend/common.layout')

{{-- Page Title --}}
@section('title')
    @parent
    » {{ trans('rinvex/fort::backend/roles.heading') }} » {{ trans('rinvex/fort::backend/roles.'.$mode) }} @if($role->exists) » {{ $role->slug }} @endif
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
        @if($role->exists)
            @include('rinvex/fort::backend/common.confirm-modal', ['type' => 'role'])
        @endif

        @if ($action === 'update')
            {{ Form::model($role, ['route' => ['rinvex.fort.backend.roles.update', $role], 'method' => 'put']) }}
            {{ Form::hidden('id') }}
        @else
            {{ Form::model($role, ['route' => ['rinvex.fort.backend.roles.store']]) }}
        @endif

            <section class="panel panel-default">

                {{-- Heading --}}
                <header class="panel-heading">
                    <h4>
                        <a href="{{ route('rinvex.fort.backend.roles.index') }}">{{ trans('rinvex/fort::backend/roles.heading') }}</a> » {{ trans('rinvex/fort::backend/roles.'.$mode) }} @if($role->exists) » {{ $role->slug }} @endif
                        @if($role->exists && $mode !== 'copy')
                            <span class="pull-right" style="margin-top: -7px">
                                @can('view-role', $role) <a href="{{ route('rinvex.fort.backend.roles.show', ['role' => $role]) }}" class="btn btn-default"><i class="fa fa-eye text-primary"></i></a> @endcan
                                @can('create-role') <a href="{{ route('rinvex.fort.backend.roles.copy', ['role' => $role]) }}" class="btn btn-default"><i class="fa fa-copy text-success"></i></a> @endcan
                                @can('delete-role', $role) <a href="#" class="btn btn-default" data-toggle="modal" data-target="#delete-confirmation" data-item-href="{{ route('rinvex.fort.backend.roles.delete', ['role' => $role]) }}" data-item-name="{{ $role->slug }}"><i class="fa fa-trash-o text-danger"></i></a> @endcan
                                @can('create-role') <a href="{{ route('rinvex.fort.backend.roles.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a> @endcan
                            </span>
                        @endif
                    </h4>
                </header>

                {{-- Data --}}
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-8">

                            {{-- Title --}}
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                {{ Form::label('title', trans('rinvex/fort::backend/roles.title'), ['class' => 'control-label']) }}
                                {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/roles.title'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">

                            {{-- Slug --}}
                            <div class="form-group{{ $errors->has('slug') ? ' has-error' : '' }}">
                                {{ Form::label('slug', trans('rinvex/fort::backend/roles.slug'), ['class' => 'control-label']) }}
                                {{ Form::text('slug', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/roles.slug'), 'required' => 'required']) }}

                                @if ($errors->has('slug'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('slug') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">

                            {{-- Description --}}
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                {{ Form::label('description', trans('rinvex/fort::backend/roles.description'), ['class' => 'control-label']) }}
                                {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/roles.description'), 'rows' => 3]) }}

                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">

                            {{-- Abilities --}}
                            <div class="form-group{{ $errors->has('abilities') ? ' has-error' : '' }}">
                                {{ Form::label('abilityList[]', trans('rinvex/fort::backend/roles.abilities'), ['class' => 'control-label']) }}
                                {{ Form::select('abilityList[]', $abilityList, null, ['class' => 'form-control', 'multiple' => 'multiple', 'size' => 4]) }}

                                @if ($errors->has('abilities'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('abilities') }}</strong>
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
                            @endif

                            <div class="pull-right">
                                {{ Form::reset(trans('rinvex/fort::backend/common.reset'), ['class' => 'btn btn-default']) }}
                                {{ Form::submit(trans('rinvex/fort::backend/common.submit'), ['class' => 'btn btn-primary']) }}
                            </div>

                        </div>
                    </div>
                </div>
            </section>

        {{ Form::close() }}

    </div>

@endsection
