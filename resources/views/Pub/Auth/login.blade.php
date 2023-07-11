@extends('Pub::layouts.layout')

@section('title', $title)

@section('content')
    <div class="row">
        <div class="col-md-offset-4 col-md-4 custom-form login-form">
            <h3 class="text-center"> Please sign in </h3>
            @if (!$errors->isEmpty())
                <div class="alert alert-danger">
                    @foreach ($errors->all('<p>:message</p>') as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif
            @if (Session::has('info'))
                <div class="alert alert-danger">
                    {{ Session::get('info') }}
                </div>
            @endif
            <form method="POST" class="login-form" action="{{ route('login.post') }}">
                @csrf
                <input class="form-control" placeholder="username" autofocus="true" name="username" type="text">
                <input class="form-control" placeholder="password" name="password" type="password" value="">
                <input class="btn btn-sm btn-primary" type="submit" value="Sign in">
            </form>
        </div>
    </div>

@endsection
