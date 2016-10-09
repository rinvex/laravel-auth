<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Rivnex Fort') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/cortex/css/gootle-lato.css') }}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
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
                        <li><a href="{{ route('rinvex.fort.frontend.auth.login') }}"> {{ trans('rinvex.fort::frontend/forms.login.heading') }}</a></li>
                        <li><a href="{{ route('rinvex.fort.frontend.auth.register') }}"> {{ trans('rinvex.fort::frontend/forms.register.heading') }}</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->username }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li class="disabled"><a href="#"><i class="fa fa-user"></i> {{ trans('rinvex.fort::frontend/menus.profile.account') }}</a></li>
                                <li><a href="{{ route('rinvex.fort.frontend.user.settings') }}"><i class="fa fa-user"></i> {{ trans('rinvex.fort::frontend/menus.profile.page') }}</a></li>
                                <li><a href="{{ route('rinvex.fort.frontend.user.sessions') }}"><i class="fa fa-check-square-o"></i> {{ trans('rinvex.fort::frontend/menus.profile.sessions') }}</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="disabled"><a href="{{ route('rinvex.fort.backend.dashboard.home') }}"><i class="fa fa-dashboard"></i> {{ trans('rinvex.fort::frontend/menus.dashboard.home') }}</a></li>
                                {{--Insert menu items here and check abilities for each item--}}
                                {{--<li><a href="{{ route($menu->slug) }}"><i class="{{ $menu->css }}"></i> {{ trans(str_replace('rinvex.fort.backend.', 'rinvex.fort::frontend/menus.dashboard.', $menu->slug)) }}</a></li>--}}
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="{{ route('rinvex.fort.frontend.auth.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> {{ trans('rinvex.fort::frontend/forms.common.logout') }}</a>
                                    <form id="logout-form" action="{{ route('rinvex.fort.frontend.auth.logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Scripts -->
    <script src="{{ asset('assets/jquery/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}

    @yield('scripts')

</body>
</html>
