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
        @if($user->exists)
            @include('rinvex.fort::backend.common.confirm-modal', ['type' => 'user'])
        @endif

        <form id="backend-content-form" action="{{ ($action === 'update' ? route('rinvex.fort.backend.users.store') : route('rinvex.fort.backend.users.update', ['user' => $user])) }}" user="form" method="post">
            {{ csrf_field() }}

            <div class="panel panel-default">

                {{-- Heading --}}
                <div class="panel-heading">
                    <h4>
                        <a href="{{ route('rinvex.fort.backend.users.index') }}">{{ trans('rinvex.fort::backend/users.heading') }}</a> / {{ trans('rinvex.fort::backend/users.'.$mode) }} @if($user->exists) » {{ $user->username }} @endif
                        @if($user->exists && $mode !== 'copy')
                            <span class="pull-right" style="margin-top: -7px">
                                <a href="{{ route('rinvex.fort.backend.users.show', ['user' => $user]) }}" class="btn btn-default"><i class="fa fa-eye text-primary"></i></a>
                                <a href="{{ route('rinvex.fort.backend.users.copy', ['user' => $user]) }}" class="btn btn-default"><i class="fa fa-copy text-success"></i></a>
                                <a href="#" class="btn btn-default" data-toggle="modal" data-target="#delete-confirmation" data-item-href="{{ route('rinvex.fort.backend.users.delete', ['user' => $user]) }}" data-item-name="{{ $user->username }}"><i class="fa fa-trash-o text-danger"></i></a>
                                <a href="{{ route('rinvex.fort.backend.users.create') }}" class="btn btn-default"><i class="fa fa-plus"></i></a>
                            </span>
                        @endif
                    </h4>
                </div>

                {{-- Data --}}
                <div class="panel-body">

                    <div class="row">
                        {{-- First Name --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">

                                <label for="first_name" class="control-label">{{ trans('rinvex.fort::backend/users.first_name') }}</label>

                                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="{{ trans('rinvex.fort::backend/users.first_name') }}" value="{{ old('first_name', $user->first_name) }}" required autofocus>

                                @if ($errors->has('first_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Middle Name --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('middle_name') ? ' has-error' : '' }}">

                                <label for="middle_name" class="control-label">{{ trans('rinvex.fort::backend/users.middle_name') }}</label>

                                <input type="text" class="form-control" name="middle_name" id="middle_name" placeholder="{{ trans('rinvex.fort::backend/users.middle_name') }}" value="{{ old('middle_name', $user->middle_name) }}" required autofocus>

                                @if ($errors->has('middle_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('middle_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Last Name --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">

                                <label for="last_name" class="control-label">{{ trans('rinvex.fort::backend/users.last_name') }}</label>

                                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="{{ trans('rinvex.fort::backend/users.last_name') }}" value="{{ old('last_name', $user->last_name) }}" required autofocus>

                                @if ($errors->has('last_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Username --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">

                                <label for="username" class="control-label">{{ trans('rinvex.fort::backend/users.username') }}</label>

                                <input type="text" class="form-control" name="username" id="username" placeholder="{{ trans('rinvex.fort::backend/users.username') }}" value="{{ old('username', $user->username) }}" required autofocus>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Job Title --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('job_title') ? ' has-error' : '' }}">

                                <label for="job_title" class="control-label">{{ trans('rinvex.fort::backend/users.job_title') }}</label>

                                <input type="text" class="form-control" name="job_title" id="job_title" placeholder="{{ trans('rinvex.fort::backend/users.job_title') }}" value="{{ old('job_title', $user->job_title) }}" required autofocus>

                                @if ($errors->has('job_title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('job_title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Prefix --}}
                        <div class="col-md-2">
                            <div class="form-group{{ $errors->has('prefix') ? ' has-error' : '' }}">

                                <label for="prefix" class="control-label">{{ trans('rinvex.fort::backend/users.prefix') }}</label>

                                <input type="text" class="form-control" name="prefix" id="prefix" placeholder="{{ trans('rinvex.fort::backend/users.prefix') }}" value="{{ old('prefix', $user->prefix) }}" required autofocus>

                                @if ($errors->has('prefix'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('prefix') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Suffix --}}
                        <div class="col-md-2">
                            <div class="form-group{{ $errors->has('suffix') ? ' has-error' : '' }}">

                                <label for="suffix" class="control-label">{{ trans('rinvex.fort::backend/users.suffix') }}</label>

                                <input type="text" class="form-control" name="suffix" id="suffix" placeholder="{{ trans('rinvex.fort::backend/users.suffix') }}" value="{{ old('suffix', $user->suffix) }}" required autofocus>

                                @if ($errors->has('suffix'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('suffix') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Email --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                                <label for="email" class="control-label">{{ trans('rinvex.fort::backend/users.email') }}</label>
                                <label for="email_verified" class="control-label pull-right">{{ trans('rinvex.fort::backend/users.verified') }}</label>

                                <div class="input-group">
                                    <input type="text" class="form-control" name="email" id="email" placeholder="{{ trans('rinvex.fort::backend/users.email') }}" value="{{ old('email', $user->email) }}" required autofocus>
                                    <span class="input-group-addon">
                                        <input type="checkbox" id="email_verified" name="email_verified">
                                    </span>
                                </div>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Country --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">

                                <label for="country" class="control-label">{{ trans('rinvex.fort::backend/users.country') }}</label>

                                <select id="country" name="country" class="form-control">
                                    <option value="" disabled selected>{{ trans('rinvex.fort::frontend/forms.account.country_select') }}</option>
                                    @foreach($countries as $code => $country)
                                        <option value="{{ $code }}" @if(old('country', $user->country) === $code) selected="selected" @endif>{{ $country->getName() }} {{ $country->getEmoji() }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('country'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">

                                <label for="phone" class="control-label">{{ trans('rinvex.fort::backend/users.phone') }}</label>
                                <label for="phone_verified" class="control-label pull-right">{{ trans('rinvex.fort::backend/users.verified') }}</label>

                                <div class="input-group">
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="{{ trans('rinvex.fort::backend/users.phone') }}" value="{{ old('phone', $user->phone) }}" required autofocus>
                                    <span class="input-group-addon">
                                        <input type="checkbox" id="phone_verified" name="phone_verified">
                                    </span>
                                </div>

                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Gender --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">

                                <label for="gender" class="control-label">{{ trans('rinvex.fort::backend/users.gender.title') }}</label>

                                <select id="gender" name="gender" class="form-control">
                                    <option value="" disabled selected>{{ trans('rinvex.fort::backend/users.gender.select') }}</option>
                                    <option value="male" @if(old('gender', $user->gender) === 'male') selected @endif>{{ trans('rinvex.fort::backend/users.gender.male') }}</option>
                                    <option value="female" @if(old('gender', $user->gender) === 'female') selected @endif>{{ trans('rinvex.fort::backend/users.gender.female') }}</option>
                                    <option value="undisclosed" @if(old('gender', $user->gender) === 'undisclosed') selected @endif>{{ trans('rinvex.fort::backend/users.gender.undisclosed') }}</option>
                                </select>

                                @if ($errors->has('gender'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Active --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">

                                <label for="active" class="control-label">{{ trans('rinvex.fort::backend/users.status.title') }}</label>

                                <select id="active" name="active" class="form-control">
                                    <option value="" disabled selected>{{ trans('rinvex.fort::backend/users.status.select') }}</option>
                                    <option value="1" @if(old('active', $user->active) === 1) selected @endif>{{ trans('rinvex.fort::backend/users.status.active') }}</option>
                                    <option value="0" @if(old('active', $user->active) === 0) selected @endif>{{ trans('rinvex.fort::backend/users.status.inactive') }}</option>
                                </select>

                                @if ($errors->has('active'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('active') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                                <label for="password" class="control-label">{{ trans('rinvex.fort::backend/users.password') }}</label>

                                <input type="text" class="form-control" name="password" id="password" placeholder="{{ trans('rinvex.fort::backend/users.password') }}" value="" required autofocus>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Abilities --}}
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('abilities') ? ' has-error' : '' }}">

                                <label for="abilities" class="control-label">{{ trans('rinvex.fort::backend/users.abilities') }}</label>

                                <select class="form-control" name="abilities[]" id="abilities" size="4" multiple>
                                    @foreach($resources as $group => $abilities)
                                        <optgroup label="{{ $group }}">
                                            @foreach($abilities as $ability)
                                                <option value="{{ $ability->id }}" @if(in_array($ability->id, $user->abilities()->getRelatedIds()->toArray())) selected @endif>{{ $ability->title }}</option>
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
                                @if($user->created_at)
                                    <small><strong>{{ trans('rinvex.fort::backend/users.created_at') }}:</strong>
                                        <time datetime="{{ $user->created_at }}">{{ $user->created_at->format('Y-m-d') }}</time>
                                    </small>
                                @endif
                                @if($user->updated_at)
                                    <small><strong>{{ trans('rinvex.fort::backend/users.updated_at') }}:</strong>
                                        <time datetime="{{ $user->updated_at }}">{{ $user->updated_at->format('Y-m-d') }}</time>
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
