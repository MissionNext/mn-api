@extends('layout')

@section('title')
    Dashboard home page
@endsection

@section('content')

<div class="row">
    <div class="col-md-offset-4 col-md-4 login-form">
        <h3> admin home page </h3>

            current user is {{ Sentry::getUser()->username }} <br/>
            {{ link_to_route('adminHomepage', 'adm2') }}

    </div>
</div>

@endsection

