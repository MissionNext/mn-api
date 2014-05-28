@extends('admin.adminHomepage')

@section('title')
    Admin home page adm2
@endsection

@section('dashboardcontent')

<div class="container">
    <div class="row">
        <div class="col-md-offset-4 col-md-4 login-form">
            <h3> admin home page  adm2 !!!</h3>

            {{ Sentry::getUser()->username }}


        </div>
    </div>
</div>

@endsection

