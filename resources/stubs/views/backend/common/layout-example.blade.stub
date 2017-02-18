<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name'))</title>

        <!-- Fonts -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
        <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

        <!-- Styles -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
        {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

        @yield('styles')

        <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>
    </head>
    <body id="app-layout">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Rinvex Fort') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li>
                                <a href="{{ route('rinvex.fort.frontend.auth.login') }}"> {{ trans('rinvex/fort::forms.common.login') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('rinvex.fort.frontend.auth.register') }}"> {{ trans('rinvex/fort::forms.common.register') }}</a>
                            </li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->username }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li class="disabled"><a href="#"><i class="fa fa-user"></i> {{ trans('rinvex/fort::menus.profile.account') }}</a></li>
                                    <li><a href="{{ route('rinvex.fort.frontend.user.settings') }}"><i class="fa fa-user"></i> {{ trans('rinvex/fort::menus.profile.page') }}</a></li>
                                    <li><a href="{{ route('rinvex.fort.frontend.user.sessions') }}"><i class="fa fa-check-square-o"></i> {{ trans('rinvex/fort::menus.profile.sessions') }}</a></li>
                                    <li role="separator" class="divider"></li>
                                    @can('access-dashboard') <li class="disabled"><a href="{{ route('rinvex.fort.backend.dashboard.home') }}"><i class="fa fa-dashboard"></i> {{ trans('rinvex/fort::menus.dashboard.home') }}</a></li> @endcan
                                    @can('list-abilities') <li><a href="{{ route('rinvex.fort.backend.abilities.index') }}"><i class="fa fa-arrow-right"></i> {{ trans('rinvex/fort::forms.common.abilities') }}</a></li> @endcan
                                    @can('list-roles') <li><a href="{{ route('rinvex.fort.backend.roles.index') }}"><i class="fa fa-arrow-right"></i> {{ trans('rinvex/fort::forms.common.roles') }}</a></li> @endcan
                                    @can('list-users') <li><a href="{{ route('rinvex.fort.backend.users.index') }}"><i class="fa fa-arrow-right"></i> {{ trans('rinvex/fort::forms.common.users') }}</a></li> @endcan
                                    @if(Auth::user()->can('access-dashboard') || Auth::user()->can('list-abilities') || Auth::user()->can('list-roles') || Auth::user()->can('list-users'))
                                        <li role="separator" class="divider"></li>
                                    @endif
                                    <li>
                                        <a href="{{ route('rinvex.fort.frontend.auth.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> {{ trans('rinvex/fort::forms.common.logout') }}</a>
                                        {{ Form::open(['route' => 'rinvex.fort.frontend.auth.logout', 'id' => 'logout-form', 'style' => 'display: none;']) }}
                                        {{ Form::close() }}
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')

        <hr />

        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <section class="panel panel-default">
                        <header class="panel-heading">Example Layout</header>
                        <div class="panel-body">
                            <h3>This view is using an example blade layout view.</h3>
                            <div class="alert alert-warning">
                                <strong>Rinvex Fort default layout view!</strong> This view:
                                <code>rinvex/fort::backend/common.layout</code> is extending a default stub layout
                                <code>rinvex/fort::backend/common.layout-example</code> which is a default layout to show
                                <highlight>Rinvex Fort</highlight> features, and that view needs overriding to your own custom layout.
                            </div>

                            <h4>Publish Resources</h4>
                            <p>
                                To correctly display this view in your own blade layout, you need to publish this package's resources as follows (if you haven't already):
                            </p>
                            <hr />
                            <code>
                                php artisan vendor:publish --provider="Rinvex\Fort\Providers\FortServiceProvider"
                            </code>
                            <hr />
                            <p>
                                Then open
                                <code>"resources/views/vendor/rinvex/fort/backend/common/layout.blade.php"</code> and change the extended layout view to the layout view you are using in your app eg:
                                <code>"layouts.app"</code>. See
                                <a href="https://laravel.com/docs/5.3/blade#extending-a-layout" target="_blank">Laravel Docs on Extending A Layout</a> for more info.
                            </p>
                        </div>

                    </section>
                </div>
            </div>
        </div>


        <!-- JavaScripts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}

        @yield('scripts')

    </body>
</html>
