@extends('layout')

@section('title')
    Admin home page
@endsection

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-offset-4 col-md-4 login-form">
            <h3> admin home page </h3>

            {{ Sentry::getUser()->username }}

        </div>
    </div>
</div>

@endsection

