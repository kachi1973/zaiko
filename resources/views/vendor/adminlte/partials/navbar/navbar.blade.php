<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
    </ul>

    @if(Gate::allows('TestMode'))
        <h3><span class="badge badge-danger">試験モードで動作しています</span></h3>
    @endif

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="" data-toggle="dropdown">
                マニュアル
            </a>
            <ul class="dropdown-menu border-0 shadow">
                <li>
                    <a class="dropdown-item" href="{{url('doc/man00-00.pdf')}}" target="_blank"">
                        <i class="far fa-file-pdf" style="color:red"></i>
                        個別在庫対応マニュアル
                    </a>
                    <a class="dropdown-item" href="{{url('doc/man01-01.pdf')}}" target="_blank"">
                        <i class="far fa-file-pdf" style="color:red"></i>
                        部品出庫マニュアル
                    </a>
                    <a class="dropdown-item" href="{{url('doc/man02-01.pdf')}}" target="_blank"">
                        <i class="far fa-file-pdf" style="color:red"></i>
                        情報連絡票マニュアル
                    </a>
                    <a class="dropdown-item" href="{{url('doc/man02-02.pdf')}}" target="_blank"">
                        <i class="far fa-file-pdf" style="color:red"></i>
                        部品購入依頼マニュアル
                    </a>
                    <a class="dropdown-item" href="{{url('doc/man02-03.pdf')}}" target="_blank"">
                        <i class="far fa-file-pdf" style="color:red"></i>
                        発生品入庫マニュアル
                    </a>
                    <a class="dropdown-item" href="{{url('doc/man02-04.pdf')}}" target="_blank"">
                        <i class="far fa-file-pdf" style="color:red"></i>
                        倉庫移動伝票マニュアル
                    </a>
                    <a class="dropdown-item" href="{{url('doc/man03-01.pdf')}}" target="_blank"">
                        <i class="far fa-file-pdf" style="color:red"></i>
                        品目マスタマニュアル
                    </a>
                    <a class="dropdown-item" href="{{url('doc/man03-02.pdf')}}" target="_blank"">
                        <i class="far fa-file-pdf" style="color:red"></i>
                        在庫マスタマニュアル
                    </a>
                    <a class="dropdown-item" href="{{url('doc/man03-03.pdf')}}" target="_blank"">
                        <i class="far fa-file-pdf" style="color:red"></i>
                        個別在庫マスタマニュアル
                    </a>
                </li>
            </ul>
        </li>

        {{-- User menu link --}}
        @if(Auth::user())
            @if(config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if(config('adminlte.right_sidebar'))
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>

</nav>
