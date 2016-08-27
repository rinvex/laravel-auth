@extends('layouts.app')

<!-- Main Content -->
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('rinvex.fort::form.verification.email.heading') }}</div>
                    <div class="panel-body">

                        <form class="form-horizontal" role="form" method="POST" action="{{ route('rinvex.fort.verification.email.post') }}">
                            {{ csrf_field() }}

                            @include('rinvex.fort::alerts.success')
                            @include('rinvex.fort::alerts.warning')
                            @include('rinvex.fort::alerts.error')

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">{{ trans('rinvex.fort::form.verification.email.field') }}</label>

                                <div class="col-md-6">
                                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email', auth()->guest() ? '' : $currentUser->email) }}" placeholder="{{ trans('rinvex.fort::form.verification.email.field') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 text-center">

                                    <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-envelope"></i> {{ trans('rinvex.fort::form.verification.email.submit') }}</button>

                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
