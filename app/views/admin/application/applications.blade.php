@extends('layout')

@section('title')
    Dashboard. Applications
@endsection

@section('content')

<h3 class="text-center">
    Application list
</h3>
<table class="table table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Application</th>
            <th>Public key</th>
            <th>Created at</th>
            <th></th>
        </tr>
    </thead>
    @foreach($applications as $application)
        <tr>
            <td>{{ $application->id }}</td>
            <td>{{ $application->name }}</td>
            <td>{{ $application->public_key }}</td>
            <td>{{ date("d.m.Y H:i", strtotime($application->created_at)) }}</td>
            <td class="text-center">
                <a href="{{ URL::route('applicationEdit', array('id' => $application->id)) }}" class="btn-warning btn btn-xs">
                    <span class="glyphicon glyphicon-edit"> </span> Edit </a>

                    {{ Form::open(array(
                        'action' => array('MissionNext\Controllers\Admin\ApplicationController@delete', $application->id),
                        'class' => 'pull-right',
                        'method' => 'delete',
                    )) }}

                    <input type="submit" class="btn btn-xs btn-danger" value=" Delete" onclick=" return confirm('confirm delete application?')" >
                    {{ Form::close() }}
            </td>
        </tr>
    @endforeach
</table>
    <div class="text-center">
        {{ $applications->links() }}
    </div>

@endsection
