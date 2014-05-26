@extends('layout')

@section('title')
    Login form
@endsection

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-offset-4 col-md-4 login-form">

                {{ Form::open(array(
                    'action' => 'MissionNext\Controllers\Admin\AdminController@login',
                    'class' => 'form-signin',
                )) }}

                <?php
                    var_dump($errors);
                ?>
                @if (!$errors->isEmpty())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
                @endif

                @if (Session::has('info'))
                    <div class="alert alert-danger">
                        {{ Session::get('info') }}
                    </div>
                @endif

                {{ Form::text('username', null, array('class' => 'form-control', 'placeholder' => 'username')) }}
                {{-- Form::text('email', null, array('class' => 'form-control', 'placeholder' => 'email')) --}}
                {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'password')) }}

                <br/>
                {{ Form::submit('Login', array('class' => 'btn btn-lg btn-primary btn-block login-form-submit')) }}

                {{ Form::close() }}
            </div>
        </div>
    </div>

@endsection

