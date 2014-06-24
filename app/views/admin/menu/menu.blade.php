<!-- Static navbar -->
<div class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
                {{ link_to_route('adminHomepage', 'Mission Next dashboard', null, array('class' => 'navbar-brand')) }}
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-user"> </span> Users <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ URL::route('users')}}">
                                <span class="glyphicon glyphicon-list"> </span> List users
                            </a>
                        </li>
                        <li>
                            <a href="{{ URL::route('userCreate')}}">
                                <span class="glyphicon glyphicon-plus"> </span> New user
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-globe"> </span> Applications <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ URL::route('applications')}}">
                                <span class="glyphicon glyphicon-list"> </span> List apps
                            </a>
                        </li>
                        <li>
                            <a href="{{ URL::route('applicationCreate')}}">
                                <span class="glyphicon glyphicon-plus"> </span> New app
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-flag"> </span> Languages <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ URL::route('languages')}}">
                                <span class="glyphicon glyphicon-list"> </span> List lang
                            </a>
                        </li>
                        <li>
                            <a href="{{ URL::route('languageCreate')}}">
                                <span class="glyphicon glyphicon-plus"> </span> New lang
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-asterisk"> </span> Subscriptions <b class="caret"></b></a>
                    <ul class="dropdown-menu">

                        <li class="dropdown-submenu">
                            <a tabindex="-1" href="#">Config</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ URL::route('sub.config.list')}}">
                                        <span class="glyphicon glyphicon-list"> </span> List configs
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ URL::route('sub.config.create')}}">
                                        <span class="glyphicon glyphicon-plus"> </span> New config
                                    </a>
                                </li>

                            </ul>
                        </li>
                        <li class="divider"></li>
                        <li class="dropdown-submenu">
                            <a tabindex="-1" href="#">Coupon</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ URL::route('sub.coupon.list')}}">
                                        <span class="glyphicon glyphicon-list"> </span> List coupons
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ URL::route('sub.coupon.create')}}">
                                        <span class="glyphicon glyphicon-plus"> </span> New coupon
                                    </a>
                                </li>

                            </ul>
                        </li>
                    </ul>
                </li>
<!--                <li><a href="#">Link</a></li>-->
<!--                <li class="dropdown">-->
<!--                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown 2<b class="caret"></b></a>-->
<!--                    <ul class="dropdown-menu">-->
<!--                        <li><a href="#">Action 1</a></li>-->
<!--                        <li><a href="#">Action 2</a></li>-->
<!--                        <li><a href="#">Action 3</a></li>-->
<!--                        <li class="divider"></li>-->
<!--                        <li class="dropdown-header">Header into dropdown</li>-->
<!--                        <li><a href="#">Link 1</a></li>-->
<!--                        <li><a href="#">Link 2</a></li>-->
<!--                    </ul>-->
<!--                </li>-->
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <div class="navbar-form">
                        <a href="{{ URL::route('logout')}}" class="btn btn-sm btn-default">
                            <span class="glyphicon glyphicon-log-out"> </span> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div><!--/.container-fluid -->
</div>