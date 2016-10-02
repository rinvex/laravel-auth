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
        @if($role->exists)
            @include('rinvex.fort::backend.common.confirm-modal', ['type' => 'role'])
        @endif

        <form id="backend-content-form" action="{{ ($action === 'update' ? route('rinvex.fort.backend.roles.store') : route('rinvex.fort.backend.roles.update', ['roleId' => $role->id])) }}" role="form" method="post">
            {{ csrf_field() }}

            <div class="panel panel-default">

                {{-- Heading --}}
                <div class="panel-heading">
                    <h4>
                        <a href="{{ route('rinvex.fort.backend.roles.index') }}">{{ trans('rinvex.fort::backend/roles.heading') }}</a> / {{ trans('rinvex.fort::backend/roles.'.$mode) }} @if($role->exists) » {{ $role->slug }} @endif
                        @if($role->exists && $mode !== 'copy')
                            <span class="pull-right" style="margin-top: -7px">
                                <a href="{{ route('rinvex.fort.backend.roles.show', ['roleId' => $role->id]) }}" class="btn btn-default"><i class="fa fa-eye text-primary"></i></a>
                                <a href="{{ route('rinvex.fort.backend.roles.copy', ['roleId' => $role->id]) }}" class="btn btn-default"><i class="fa fa-copy text-success"></i></a>
                                <a href="#" class="btn btn-default" data-toggle="modal" data-target="#delete-confirmation" data-item-href="{{ route('rinvex.fort.backend.roles.delete', ['roleId' => $role->id]) }}" data-item-name="{{ $role->slug }}"><i class="fa fa-trash-o text-danger"></i></a>
                                <a href="{{ route('rinvex.fort.backend.roles.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a>
                            </span>
                        @endif
                    </h4>
                </div>

                {{-- Data --}}
                <div class="panel-body">

                    <div class="row">
                        {{-- Title --}}
                        <div class="col-md-8">
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">

                                <label for="title" class="control-label">{{ trans('rinvex.fort::backend/roles.title') }}</label>

                                <input type="text" class="form-control" name="title" id="title" placeholder="{{ trans('rinvex.fort::backend/roles.title') }}" value="{{ old('title', $role->title) }}" required autofocus>

                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Slug --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('slug') ? ' has-error' : '' }}">

                                <label for="slug" class="control-label">{{ trans('rinvex.fort::backend/roles.slug') }}</label>

                                <input type="text" class="form-control" name="slug" id="slug" placeholder="{{ trans('rinvex.fort::backend/roles.slug') }}" value="{{ old('slug', $role->slug) }}" required>

                                @if ($errors->has('slug'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('slug') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Description --}}
                        <div class="col-md-8">
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">

                                <label for="description" class="control-label">{{ trans('rinvex.fort::backend/roles.description') }}</label>

                                <textarea class="form-control" name="description" id="description" placeholder="{{ trans('rinvex.fort::backend/roles.description') }}" rows="3">{{ old('description', $role->description)  }}</textarea>

                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Abilities --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('abilities') ? ' has-error' : '' }}">

                                <label for="abilities" class="control-label">{{ trans('rinvex.fort::backend/roles.abilities') }}</label>

                                <select class="form-control" name="abilities[]" id="abilities" size="4" multiple>
                                    @foreach($resources as $group => $abilities)
                                        <optgroup label="{{ $group }}">
                                            @foreach($abilities as $ability)
                                                <option value="{{ $ability->id }}" @if(in_array($ability->id, $role->abilities()->getRelatedIds()->toArray())) selected @endif>{{ $ability->title }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>

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
                                    <small><strong>{{ trans('rinvex.fort::backend/roles.created_at') }}:</strong>
                                        <time datetime="{{ $role->created_at }}">{{ $role->created_at->format('Y-m-d') }}</time>
                                    </small>
                                @endif
                                @if($role->updated_at)
                                    <small><strong>{{ trans('rinvex.fort::backend/roles.updated_at') }}:</strong>
                                        <time datetime="{{ $role->updated_at }}">{{ $role->updated_at->format('Y-m-d') }}</time>
                                    </small>
                                @endif
                            @endif
                            <div class="pull-right">
                                <button type="reset" class="btn btn-default">Reset</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>

@endsection
