
<div class="navbar navbar-default" style="margin-bottom: 50px;" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="{{ URL::route('dashboards.index') }}" class="navbar-brand">Mission Next dashboard</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li>
                    <a href="{{ URL::route('dashboards.application.index') }}"> <span class="glyphicon glyphicon-globe"> </span> Applications</a>
                </li>
                <li>
                    <a href="{{ URL::route('dashboards.languages.index') }}"> <span class="glyphicon glyphicon-flag"> </span> Languages</a>
                </li>
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-asterisk"> </span> Subscriptions <b class="caret"></b></a>
                    <ul class="dropdown-menu">

                        <li class="dropdown-submenu">
                            <a tabindex="-1" href="javascript:void(0);">Config</a>
                            <ul class="dropdown-menu">
                                @foreach ($menu as $key=>$val)
                                    <li>
                                        <a href="{{ URL::route('dashboards.subscriptions.index',['subscription'=>$key]) }}">
                                            <span class="glyphicon glyphicon-globe"> </span> {{$val}}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a tabindex="-1" href="{{ URL::route('dashboards.coupons.index') }}">Coupon</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{ URL::route('dashboards.users.index') }}"> <span class="glyphicon glyphicon-user"> </span> Users </a>
                </li>
                <li>
                    <a href="{{ URL::route('dashboards.administrators.index') }}"> <span class="glyphicon glyphicon-user"> </span> Administrators </a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <div class="navbar-form">
                        <a href="{{ URL::route('login.logout') }}" class="btn btn-sm btn-default">
                            <span class="glyphicon glyphicon-log-out"> </span> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div><!--/.container-fluid -->
</div>
