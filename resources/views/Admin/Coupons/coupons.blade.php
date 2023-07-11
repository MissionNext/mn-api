<div class="row ">
    <div class="col-md-2 pull-right">
        <a class="btn btn-success btn-sm" href="{{ URL::route('dashboards.coupons.create')}}"><span class="glyphicon glyphicon-plus"> </span> Add App</a>
    </div>
</div>

<div class="row">
    <div class="col-md-11">
        <h3>
            Coupon list
        </h3>
    </div>
    <div class="col-md-1">
{{--        <span class="pull-right"> {{ $coupons->getTo() }} / {{ $coupons->getTotal() }} </span>--}}
    </div>
</div>

@if (Session::has('message'))
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ Session::get('message') }}
    </div>
@endif

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

@if (Session::has('alert'))
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ Session::get('alert') }}
    </div>
@endif

<table class="table table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Value</th>
            <th>Is Active</th>
            <th></th>
        </tr>
    </thead>
    @foreach($coupons as $coupon)
        <tr>
            <td>{{ $coupon->id }}</td>
            <td>{{ $coupon->code }}</td>
            <td>$ {{ $coupon->value }}</td>
            <td>
                @if ($coupon->is_active)
                    <span class="label label-success">Active</span>
                @else
                    <span class="label label-default">Used</span>
                @endif
            </td>
            <td class="text-center">
                <a href="{{ URL::route('dashboards.coupons.edit', ['coupon' => $coupon->id]) }}" class="btn-warning btn btn-xs">
                    <span class="glyphicon glyphicon-edit"> </span> Edit
                </a>
                <form class="pull-right ng-pristine ng-valid" role="form" enctype="multipart/form-data" method="post"
                      action="{{ route('dashboards.coupons.delete.delete', ['coupon' => $coupon->id]) }}">
                    @csrf
                    @method('DELETE')
                    <input type="submit" class="btn btn-xs btn-danger" value=" Delete" onclick=' return confirm("confirm delete language {{ $coupon->code }}?")' >
                </form>
            </td>
        </tr>
    @endforeach
</table>

<div class="text-center">
    {{ $coupons->onEachSide(5)->links() }}
</div>

