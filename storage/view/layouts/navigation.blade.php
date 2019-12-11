
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold">{{ $_private_info['info']['name']  }}</strong>
                            </span>
                            <span class="text-muted text-xs block"> {{ $_private_info['roleName']  }} <b class="caret"></b></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="/auth/logout">登出</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    IN+
                </div>
            </li>
            @foreach($_private_info['menus'] as $menu)
                @if($menu['C']["Cdisplay"] === true)
                    @if($menu['C']['active'] === true)
                        <li class="active">
                    @else
                        <li class="">
                            @endif
                        <a href="#">
                            <i class="fa {{ $menu['C']['Cstyle'] }}"></i>
                            <span>{{ $menu['C']['Cname']}}</span>
                            <span class="pull-right-container">
                                 <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="nav nav-second-level collapse ">
                            @foreach($menu['F'] as $fk=>$fv)
                                @if($fv['display'] === true)
                                    @if($fv['active'] === true)
                                        <li class="active">
                                        @else
                                        <li class="">
                                    @endif
                                        <a href="{{ $fv['route'] }}">
                                            <i class="fa {{$fv['style']}}"></i>
                                            {{$fk}}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach


        </ul>

    </div>
</nav>
