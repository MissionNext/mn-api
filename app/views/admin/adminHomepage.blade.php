@extends('layout')

@section('title')
    Dashboard home page
@endsection

@section('content')

<div class="row">
    <div class="col-md-offset-4 col-md-4 login-form">
        <h3> admin home page </h3>

            current user is {{ Sentry::getUser()->username }} <br/>

    </div>
</div>

@endsection

