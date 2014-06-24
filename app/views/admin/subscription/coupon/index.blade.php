@extends('layout')

@section('title')
Dashboard. Subscription. Coupon
@endsection

@section('content')

<div class="row">
    <div class="col-md-11">
        <h3 class="text-center">
            Config list
        </h3>
    </div>
    <div class="col-md-1">
        <span class="pull-right"> {{ $models->getTo() }} / {{ $models->getTotal() }} </span>
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

<table class="table table-hover">
    <thead>
    <tr>
        <th>ID</th>
        <th>Code</th>
        <th>Is Active</th>

        <th></th>
    </tr>
    </thead>
    @foreach($models as $model)
    <tr>
        <td>{{ $model->id }}</td>
        <td>{{ $model->code }}</td>
        <td>{{ $model->is_active }}</td>
        <td class="text-center">
            <a href="{{ URL::route('sub.coupon.edit', array(  $model->id) ) }}" class="btn-warning btn btn-xs">
                <span class="glyphicon glyphicon-edit"> </span> Edit </a>

            {{ Form::open(array(
            'route' => array('sub.config.delete', $model->id),
            'class' => 'pull-right',
            'method' => 'delete',
            )) }}

            <input type="submit" class="btn btn-xs btn-danger" value=" Delete" onclick=' return confirm("confirm delete {{ $model->name }} language?")' >
            {{ Form::close() }}
        </td>
    </tr>
    @endforeach
</table>

<div class="text-center">
    {{ $models->links() }}
</div>

@endsection
