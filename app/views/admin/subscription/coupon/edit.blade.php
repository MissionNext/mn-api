@extends('layout')

@section('title')
Dashboard. Editing coupon
@endsection

@section('content')

<div class="row">
    <div class="col-md-6 col-md-offset-3 custom-form">

        <h3 class="text-center"> Editing coupon </h3>
        @if (!$errors->isEmpty())
        <div class="alert alert-danger">
            @foreach ($errors->all('<p>:message</p>') as $error)
            {{ $error }}
            @endforeach
        </div>
        @endif

        {{ Form::model($model, array('route' => array('sub.coupon.edit', $model->id))) }}

        <div class="form-group">
            {{ Form::label('code', 'Code', ['class' => 'col-sm-2 control-label']) }}
            <div class="col-sm-10">
                {{ Form::text('code', null, array('class' => 'form-control', 'placeholder' => 'code', 'autofocus' => 'true')) }}
            </div>
        </div>

        <a href="{{ URL::route('sub.coupon.list') }}" class="btn btn-sm btn-warning pull-left cancel_btm"> Cancel </a>
        <input type="submit" value="Edit" class="btn btn-sm btn-info pull-right">

        {{ Form::close() }}

    </div>
</div>
@endsection

