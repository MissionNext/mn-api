<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" ng-app="mission-next" class="ng-scope">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    @section('styles')
        <link href="{{asset('packages/admin/css/bootstrap.css')}}" rel="stylesheet">
        <link href="{{asset('packages/admin/css/bootstrap-theme.css')}}" rel="stylesheet">
        <link href="{{asset('packages/admin/css/selectize-bootstrap3.css')}}" rel="stylesheet">
        <link href="{{asset('packages/admin/css/base.css')}}" rel="stylesheet">
        <link href="{{asset('packages/admin/css/jquery-ui.css')}}" rel="stylesheet">
        <link href="{{asset('packages/select2/css/select2.css')}}" rel="stylesheet">
        <link href="{{asset('packages/select2-bootstrap4-theme/select2-bootstrap4.css')}}" rel="stylesheet">
    @show
    @yield('add-styles')
</head>
<body>
<div class="container">
    @yield('header')
    @yield('content')
</div>
@yield('footer')
@section('javascripts')
    <script src="{{asset('packages/admin/js/jquery-1.11.1.min.js')}}"></script>
    <script src="{{asset('packages/admin/js/jquery-ui.min.js')}}"></script>
    <script src="{{asset('packages/admin/js/selectize.min.js')}}"></script>
    <script src="{{asset('packages/admin/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('packages/admin/js/angular.min.js')}}"></script>
    <script src="{{asset('packages/admin/js/sanitize.js')}}"></script>
    <script src="{{asset('packages/admin/js/dirPagination.js')}}"></script>
    <script src="{{asset('packages/admin/js/angular-resource.min.js')}}"></script>
    <script src="{{asset('packages/admin/js/angular-route.min.js')}}"></script>
    <script src="{{asset('packages/admin/js/date.js')}}"></script>
    <script src="{{asset('project/js/app.js')}}"></script>
    <script src="{{asset('packages/select2/js/select2.full.js')}}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()
        });
    </script>
@show
@yield('add-javascripts')
</body>
</html>
