{{-- Master Layout --}}
@extends('rinvex/fort::backend/common.layout')

{{-- Page Title --}}
@section('title')
    @parent
    » {{ trans('rinvex/fort::backend/users.heading') }} » {{ trans('rinvex/fort::backend/users.'.$mode) }} @if($user->exists) » {{ $user->username }} @endif
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
        @if($user->exists)
            @include('rinvex/fort::backend/common.confirm-modal', ['type' => 'user'])
        @endif

        @if ($action === 'update')
            {{ Form::model($user, ['route' => ['rinvex.fort.backend.users.update', $user], 'method' => 'put']) }}
            {{ Form::hidden('id') }}
        @else
            {{ Form::model($user, ['route' => ['rinvex.fort.backend.users.store']]) }}
        @endif

            <section class="panel panel-default">

                {{-- Heading --}}
                <header class="panel-heading">
                    <h4>
                        <a href="{{ route('rinvex.fort.backend.users.index') }}">{{ trans('rinvex/fort::backend/users.heading') }}</a> » {{ trans('rinvex/fort::backend/users.'.$mode) }} @if($user->exists) » {{ $user->username }} @endif
                        @if($user->exists && $mode !== 'copy')
                            <span class="pull-right" style="margin-top: -7px">
                                @can('view-users', $user) <a href="{{ route('rinvex.fort.backend.users.show', ['user' => $user]) }}" class="btn btn-default"><i class="fa fa-eye text-primary"></i></a> @endcan
                                @can('create-users') <a href="{{ route('rinvex.fort.backend.users.copy', ['user' => $user]) }}" class="btn btn-default"><i class="fa fa-copy text-success"></i></a> @endcan
                                @can('delete-users', $user) <a href="#" class="btn btn-default" data-toggle="modal" data-target="#delete-confirmation" data-item-href="{{ route('rinvex.fort.backend.users.delete', ['user' => $user]) }}" data-item-name="{{ $user->username }}"><i class="fa fa-trash-o text-danger"></i></a> @endcan
                                @can('create-users') <a href="{{ route('rinvex.fort.backend.users.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a> @endcan
                            </span>
                        @endif
                    </h4>
                </header>

                {{-- Data --}}
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-4">

                            {{-- First Name --}}
                            <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                {{ Form::label('first_name', trans('rinvex/fort::backend/users.first_name'), ['class' => 'control-label']) }}
                                {{ Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.first_name'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                                @if ($errors->has('first_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">

                            {{-- Middle Name --}}
                            <div class="form-group{{ $errors->has('middle_name') ? ' has-error' : '' }}">
                                {{ Form::label('middle_name', trans('rinvex/fort::backend/users.middle_name'), ['class' => 'control-label']) }}
                                {{ Form::text('middle_name', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.middle_name')]) }}

                                @if ($errors->has('middle_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('middle_name') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">

                            {{-- Last Name --}}
                            <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                {{ Form::label('last_name', trans('rinvex/fort::backend/users.last_name'), ['class' => 'control-label']) }}
                                {{ Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.last_name')]) }}

                                @if ($errors->has('last_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">

                            {{-- Username --}}
                            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                {{ Form::label('username', trans('rinvex/fort::backend/users.username'), ['class' => 'control-label']) }}
                                {{ Form::text('username', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.username'), 'required' => 'required']) }}

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">

                            {{-- Job Title --}}
                            <div class="form-group{{ $errors->has('job_title') ? ' has-error' : '' }}">
                                {{ Form::label('job_title', trans('rinvex/fort::backend/users.job_title'), ['class' => 'control-label']) }}
                                {{ Form::text('job_title', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.job_title')]) }}

                                @if ($errors->has('job_title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('job_title') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-2">

                            {{-- Prefix --}}
                            <div class="form-group{{ $errors->has('prefix') ? ' has-error' : '' }}">
                                {{ Form::label('prefix', trans('rinvex/fort::backend/users.prefix'), ['class' => 'control-label']) }}
                                {{ Form::text('prefix', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.prefix')]) }}

                                @if ($errors->has('prefix'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('prefix') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-2">

                            {{-- email --}}
                            <div class="form-group{{ $errors->has('suffix') ? ' has-error' : '' }}">
                                {{ Form::label('suffix', trans('rinvex/fort::backend/users.suffix'), ['class' => 'control-label']) }}
                                {{ Form::text('suffix', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.suffix')]) }}

                                @if ($errors->has('suffix'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('suffix') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">

                            {{-- Email --}}
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                {{ Form::label('email', trans('rinvex/fort::backend/users.email'), ['class' => 'control-label']) }}
                                {{ Form::label('email_verified', trans('rinvex/fort::backend/users.verified'), ['class' => 'control-label pull-right']) }}

                                <div class="input-group">
                                    {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.email'), 'required' => 'required']) }}
                                    <span class="input-group-addon">
                                        {{ Form::checkbox('email_verified') }}
                                    </span>
                                </div>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">

                            {{-- Country --}}
                            <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                                {{ Form::label('country', trans('rinvex/fort::backend/users.country.title'), ['class' => 'control-label']) }}
                                {{ Form::select('country', $countries, null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.country.select')]) }}

                                @if ($errors->has('country'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">

                            {{-- Phone --}}
                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                {{ Form::label('phone', trans('rinvex/fort::backend/users.phone'), ['class' => 'control-label']) }}
                                {{ Form::label('phone_verified', trans('rinvex/fort::backend/users.verified'), ['class' => 'control-label pull-right']) }}

                                <div class="input-group">
                                    {{ Form::text('phone', null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.phone')]) }}
                                    <span class="input-group-addon">
                                        {{ Form::checkbox('phone_verified') }}
                                    </span>
                                </div>

                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">

                            {{-- Gender --}}
                            <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
                                {{ Form::label('gender', trans('rinvex/fort::backend/users.gender.title'), ['class' => 'control-label']) }}
                                {{ Form::select('gender', ['male' => trans('rinvex/fort::backend/users.gender.male'), 'female' => trans('rinvex/fort::backend/users.gender.female'), 'undisclosed' => trans('rinvex/fort::backend/users.gender.undisclosed')], null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.gender.select')]) }}

                                @if ($errors->has('gender'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">

                            {{-- Active --}}
                            <div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                                {{ Form::label('active', trans('rinvex/fort::backend/users.status.title'), ['class' => 'control-label']) }}
                                {{ Form::select('active', [1 => trans('rinvex/fort::backend/users.status.active'), 0 => trans('rinvex/fort::backend/users.status.inactive')], null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.status.select')]) }}

                                @if ($errors->has('active'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('active') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">

                            {{-- Password --}}
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                {{ Form::label('password', trans('rinvex/fort::backend/users.password'), ['class' => 'control-label']) }}
                                {{ Form::password('password', ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.password'), 'required' => 'required']) }}

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        @can('assign-roles')
                        <div class="col-md-4">

                            {{-- Roles --}}
                            <div class="form-group{{ $errors->has('roles') ? ' has-error' : '' }}">
                                {{ Form::label('roleList[]', trans('rinvex/fort::backend/users.roles.name'), ['class' => 'control-label']) }}
                                {{ Form::select('roleList[]', $roleList, null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.roles.select'), 'multiple' => 'multiple', 'size' => 4]) }}

                                @if ($errors->has('roles'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('roles') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        @endcan
                        @can('grant-abilities')
                        <div class="col-md-4">

                            {{-- Abilities --}}
                            <div class="form-group{{ $errors->has('abilityList[]') ? ' has-error' : '' }}">
                                {{ Form::label('abilityList[]', trans('rinvex/fort::backend/users.abilities.name'), ['class' => 'control-label']) }}
                                {{ Form::select('abilityList[]', $abilityList, null, ['class' => 'form-control', 'placeholder' => trans('rinvex/fort::backend/users.abilities.select'), 'multiple' => 'multiple', 'size' => 4]) }}

                                @if ($errors->has('abilities'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('abilityList[]') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        @endcan
                    </div>

                </div>

                <div class="panel-footer">
                    <div class="row">
                        <div class="col-md-12">

                            @if($mode !== 'copy')
                                @if($user->created_at)
                                    <small><strong>{{ trans('rinvex/fort::backend/users.created_at') }}:</strong>
                                        <time datetime="{{ $user->created_at }}">{{ $user->created_at->format('Y-m-d') }}</time>
                                    </small>
                                @endif
                                @if($user->updated_at)
                                    <small><strong>{{ trans('rinvex/fort::backend/users.updated_at') }}:</strong>
                                        <time datetime="{{ $user->updated_at }}">{{ $user->updated_at->format('Y-m-d') }}</time>
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
