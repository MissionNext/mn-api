@extends('layout')

@section('title')
Dashboard. Users
@endsection

@section('content')

<div class="row">
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-10">
                <h3 class="text-center">
                    User list
                </h3>
            </div>
            <div class="col-md-2">
                <span class="pull-right"> {{ $users->getTo() }} / {{ $users->getTotal() }} </span>
            </div>
        </div>
        @if (Session::has('info'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('info') }}
        </div>
        @endif
        @if (Session::has('warning'))
        <div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('warning') }}
        </div>
        @endif


    <div id="firter-rezult">

    </div>
    <div id="default-rezult">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>E-mail</th>
                <th>Created at</th>
                <th>Last login</th>
                <th></th>
            </tr>
            </thead>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ date("d.m.Y H:i", strtotime($user->created_at)) }}</td>
                <td>{{ date("d.m.Y H:i", strtotime($user->last_login)) }}</td>
                <td>
                    <a href="{{ URL::route('userEdit', array('id' => $user->id)) }}" class="btn-warning btn btn-xs">
                        <span class="glyphicon glyphicon-edit"> </span> Edit </a>

                    {{ Form::open(array(
                        'action' => array('MissionNext\Controllers\Admin\UserController@delete', $user->id),
                        'class' => 'pull-right',
                        'method' => 'delete',
                    )) }}

                    <input type="submit" class="btn btn-xs btn-danger" value=' Delete' onclick=' return confirm("confirm delete user {{ $user->username }} ?")' >
                    {{ Form::close() }}
                </td>
            </tr>
            @endforeach
        </table>

        <div class="text-center">
            {{ $users->links() }}
        </div>

    </div>

    </div>
    <div class="col-md-3">
        <h3 class="text-center">Filters:</h3>

        <div class="user-filters pull-right">
            <label for="apps-select-id">By applications:</label>
            <select class="form-control" name="app" id="apps-select-id">
                    <option value="all">All applications</option>
                @foreach($apps as $app)
                    <option value="{{ $app['id'] }}">{{ $app['name'] }}</option>
                @endforeach
            </select>
        </div>








<?php
/*
        {{ Form::open(array(
            'action' => 'MissionNext\Controllers\Admin\AjaxController@filterBy',
            'class' => 'custom-form',
            'role' => 'form'
        )) }}
        {{ Form::selectMonth('month') }}

        {{ Form::select('size', array('L' => 'Large', 'S' => 'Small')) }}

        {{ Form::text('app') }}
        {{ Form::submit('Create ', array('class' => 'btn btn-sm btn-info')) }}

        {{ Form::close() }}
*/
?>
    </div>
</div>

@endsection

@section('javascripts')
@parent
<script>
    $('#apps-select-id').change(function() {
        var selectValue = $(this).val();
        $.post("{{ URL::route('userFilters') }}", {appId: selectValue } )
            .done(function(msg){

                $('#firter-rezult').html(msg);
                $('#default-rezult').hide();
//                console.log(msg);
            })
            .error(function(msg){
                console.log(msg);
            });
//        console.log(selectValue);
    })

</script>
@endsection
