@extends('layout')

@section('title')
    Dashboard. Users
@endsection

@section('content')
<div ng-view></div>
@endsection

@section('javascripts')
@parent
{{ HTML::script(URL::asset('js/project/controllers/user.js')) }}

@endsection
