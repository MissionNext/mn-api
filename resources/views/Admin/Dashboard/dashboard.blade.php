@extends('layouts.layout')

@section('title', $title)

@section('header')
    @include('layouts.header',$menu)
@endsection

@section('content')
    {!! $content !!}
@endsection

@section('footer')
{{--    @include('layouts.footer')--}}
@endsection
