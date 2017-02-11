{{-- Master Layout --}}
@extends('rinvex/fort::backend/common.layout')

{{-- Page Title --}}
@section('title')
    {{ config('app.name') }} » {{ trans('rinvex/fort::forms.common.abilities') }} » {{ trans('rinvex/fort::forms.common.'.$mode) }} @if($ability->exists) {{ $ability->slug }} @endif
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
        @if($ability->exists)
            @include('rinvex/fort::backend/common.confirm-modal', ['type' => 'ability'])
        @endif

        @if ($action === 'update')
            {{ Form::model($ability, ['route' => ['rinvex.fort.backend.abilities.update', $ability], 'method' => 'put']) }}
            {{ Form::hidden('id') }}
        @else
            {{ Form::model($ability, ['route' => ['rinvex.fort.backend.abilities.store']]) }}
        @endif

            <section class="panel panel-default">

                {{-- Heading --}}
                <header class="panel-heading">
                    <h4>
                        <a href="{{ route('rinvex.fort.backend.abilities.index') }}">{{ trans('rinvex/fort::forms.common.abilities') }}</a> » {{ trans('rinvex/fort::forms.common.'.$mode) }} @if($ability->exists) <strong>{{ $ability->slug }}</strong> @endif
                        @if($ability->exists)
                            <span class="pull-right" style="margin-top: -7px">
                                @can('delete-abilities', $ability) <a href="#" class="btn btn-default" data-toggle="modal" data-target="#delete-confirmation" data-item-href="{{ route('rinvex.fort.backend.abilities.delete', ['ability' => $ability]) }}" data-item-name="{{ $ability->slug }}"><i class="fa fa-trash-o text-danger"></i></a> @endcan
                                @can('create-abilities') <a href="{{ route('rinvex.fort.backend.abilities.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a> @endcan
                            </span>
                        @endif
                    </h4>
                </header>

                {{-- Data --}}
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-6">

                            {{-- Name --}}
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                {{ Form::label('name', trans('rinvex/fort::forms.common.name'), ['class' => 'control-label']) }}
                                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.name'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-6">

                            {{-- Policy --}}
                            <div class="form-group{{ $errors->has('policy') ? ' has-error' : '' }}">
                                {{ Form::label('policy', trans('rinvex/fort::forms.common.policy'), ['class' => 'control-label']) }}
                                {{ Form::text('policy', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.policy')]) }}

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
                                {{ Form::label('action', trans('rinvex/fort::forms.common.action'), ['class' => 'control-label']) }}
                                {{ Form::text('action', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.action'), 'required' => 'required']) }}

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
                                {{ Form::label('resource', trans('rinvex/fort::forms.common.resource'), ['class' => 'control-label']) }}
                                {{ Form::text('resource', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.resource'), 'required' => 'required']) }}

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
                                {{ Form::label('description', trans('rinvex/fort::forms.common.description'), ['class' => 'control-label']) }}
                                {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::forms.common.description'), 'rows' => 3]) }}

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

                            @if($ability->exists)
                                @if($ability->created_at)
                                    <small><strong>{{ trans('rinvex/fort::forms.common.created_at') }}:</strong>
                                        <time datetime="{{ $ability->created_at }}">{{ $ability->created_at->format('Y-m-d') }}</time>
                                    </small>
                                @endif

                                @if($ability->created_at && $ability->updated_at) | @endif

                                @if($ability->updated_at)
                                    <small><strong>{{ trans('rinvex/fort::forms.common.updated_at') }}:</strong>
                                        <time datetime="{{ $ability->updated_at }}">{{ $ability->updated_at->format('Y-m-d') }}</time>
                                    </small>
                                @endif
                            @endif

                            <div class="pull-right">
                                {{ Form::reset(trans('rinvex/fort::forms.common.reset'), ['class' => 'btn btn-default']) }}
                                {{ Form::submit(trans('rinvex/fort::forms.common.submit'), ['class' => 'btn btn-primary']) }}
                            </div>

                        </div>
                    </div>
                </div>
            </section>

        {{ Form::close() }}

    </div>

@endsection
