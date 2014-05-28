@extends('layout')

@section('title')
    Admin home page
@endsection

@yield('dashboardcontent')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-offset-4 col-md-4 login-form">
            <h3> admin home page </h3>

            {{ Sentry::getUser()->username }}

            {{ link_to_route('adm2', 'adm2') }}

        </div>
    </div>
</div>

@endsection

