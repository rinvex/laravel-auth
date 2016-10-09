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
        @if($ability->exists)
            @include('rinvex.fort::backend.common.confirm-modal', ['type' => 'ability'])
        @endif

        <form id="backend-content-form" action="{{ ($action === 'update' ? route('rinvex.fort.backend.abilities.store') : route('rinvex.fort.backend.abilities.update', ['abilityId' => $ability->id])) }}" ability="form" method="post">
            {{ csrf_field() }}

            <div class="panel panel-default">

                {{-- Heading --}}
                <div class="panel-heading">
                    <h4>
                        <a href="{{ route('rinvex.fort.backend.abilities.index') }}">{{ trans('rinvex.fort::backend/abilities.heading') }}</a> / {{ trans('rinvex.fort::backend/abilities.'.$mode) }} @if($ability->exists) » {{ $ability->slug }} @endif
                        @if($ability->exists && $mode !== 'copy')
                            <span class="pull-right" style="margin-top: -7px">
                                <a href="{{ route('rinvex.fort.backend.abilities.show', ['abilityId' => $ability->id]) }}" class="btn btn-default"><i class="fa fa-eye text-primary"></i></a>
                                <a href="{{ route('rinvex.fort.backend.abilities.copy', ['abilityId' => $ability->id]) }}" class="btn btn-default"><i class="fa fa-copy text-success"></i></a>
                                <a href="#" class="btn btn-default" data-toggle="modal" data-target="#delete-confirmation" data-item-href="{{ route('rinvex.fort.backend.abilities.delete', ['abilityId' => $ability->id]) }}" data-item-name="{{ $ability->slug }}"><i class="fa fa-trash-o text-danger"></i></a>
                                <a href="{{ route('rinvex.fort.backend.abilities.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a>
                            </span>
                        @endif
                    </h4>
                </div>

                {{-- Data --}}
                <div class="panel-body">

                    <div class="row">
                        {{-- Title --}}
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">

                                <label for="title" class="control-label">{{ trans('rinvex.fort::backend/abilities.title') }}</label>

                                <input type="text" class="form-control" name="title" id="title" placeholder="{{ trans('rinvex.fort::backend/abilities.title') }}" value="{{ old('title', $ability->title) }}" required autofocus>

                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Policy --}}
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('policy') ? ' has-error' : '' }}">

                                <label for="policy" class="control-label">{{ trans('rinvex.fort::backend/abilities.policy') }}</label>

                                <input type="text" class="form-control" name="policy" id="policy" placeholder="{{ trans('rinvex.fort::backend/abilities.policy') }}" value="{{ old('policy', $ability->policy) }}" required>

                                @if ($errors->has('policy'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('policy') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Action --}}
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('action') ? ' has-error' : '' }}">

                                <label for="action" class="control-label">{{ trans('rinvex.fort::backend/abilities.action') }}</label>

                                <input type="text" class="form-control" name="action" id="action" placeholder="{{ trans('rinvex.fort::backend/abilities.action') }}" value="{{ old('action', $ability->action) }}" required autofocus>

                                @if ($errors->has('action'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('action') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Resource --}}
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('resource') ? ' has-error' : '' }}">

                                <label for="resource" class="control-label">{{ trans('rinvex.fort::backend/abilities.resource') }}</label>

                                <input type="text" class="form-control" name="resource" id="resource" placeholder="{{ trans('rinvex.fort::backend/abilities.resource') }}" value="{{ old('resource', $ability->resource) }}" required>

                                @if ($errors->has('resource'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('resource') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Description --}}
                        <div class="col-md-12">
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">

                                <label for="description" class="control-label">{{ trans('rinvex.fort::backend/abilities.description') }}</label>

                                <textarea class="form-control" name="description" id="description" placeholder="{{ trans('rinvex.fort::backend/abilities.description') }}" rows="3">{{ old('description', $ability->description)  }}</textarea>

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
                                    <small><strong>{{ trans('rinvex.fort::backend/abilities.created_at') }}:</strong>
                                        <time datetime="{{ $ability->created_at }}">{{ $ability->created_at->format('Y-m-d') }}</time>
                                    </small>
                                @endif
                                @if($ability->updated_at)
                                    <small><strong>{{ trans('rinvex.fort::backend/abilities.updated_at') }}:</strong>
                                        <time datetime="{{ $ability->updated_at }}">{{ $ability->updated_at->format('Y-m-d') }}</time>
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
