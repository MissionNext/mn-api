@extends('layout')

@section('title')
Dashboard. Creating new coupon
@endsection

@section('content')
<div class="row">
    <div class="col-md-6 col-md-offset-3 custom-form">
        <h3 class="text-center"> Creating new coupon </h3>
        @if (!$errors->isEmpty())
        <div class="alert alert-danger">
            @foreach ($errors->all('<p>:message</p>') as $error)
            {{ $error }}
            @endforeach
        </div>
        @endif
        {{ Form::open(array(
        'route' => 'sub.coupon.new',
        'class' => 'form-horizontal',
        'role' => 'form'
        )) }}


        <div class="form-group">
            {{ Form::label('code', 'Code', ['class' => 'col-sm-2 control-label']) }}
            <div class="col-sm-10">
                {{ Form::text('code', null, array('class' => 'form-control', 'placeholder' => 'code', 'autofocus' => 'true')) }}
            </div>
        </div>


        {{ Form::submit('Create ', array('class' => 'btn btn-sm btn-info')) }}

        {{ Form::close() }}

    </div>
</div>

@endsection