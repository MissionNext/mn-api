@extends('layout')

@section('title')
    Dashboard. Creating new application
@endsection

@section('content')
        <div class="row">
            <div class="col-md-4 col-md-offset-3 custom-form">
                <h3 class="text-center"> Creating new application </h3>
                @if (!$errors->isEmpty())
                <div class="alert alert-danger">
                    @foreach ($errors->all('<p>:message</p>') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif

                {{ Form::open(array(
                'action' => 'MissionNext\Controllers\Admin\ApplicationController@create',
                'class' => 'custom-form',
                'role' => 'form'
                )) }}

                {{ Form::text('app_name', null, array('class' => 'form-control', 'placeholder' => 'application name', 'autofocus' => 'true')) }}
                {{ Form::text('public_key', null, array('class' => 'form-control', 'placeholder' => 'your app id')) }}
                {{ Form::password('private_key', array('class' => 'form-control', 'placeholder' => 'your private key')) }}

                {{ Form::submit('Create ', array('class' => 'btn btn-sm btn-info')) }}

                {{ Form::close() }}
            </div>
        </div>
<p align="center">Caution: Any new application must be added to both the production AND stage site. </p>
<p align="center">Needed to be sure applicaiton IDs are identical. (Note added by Nelson Aug 2018)</p>

@endsection

