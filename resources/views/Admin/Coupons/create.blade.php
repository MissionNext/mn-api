<div class="row">
    <div class="col-md-4 col-md-offset-3 custom-form">
        <h3 class="text-center"> Creating new coupon </h3>
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all(':message') as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <form class="custom-form ng-pristine ng-valid" role="form" enctype="multipart/form-data" method="post"
              action="{{ route('dashboards.coupons.store.post') }}">
            @csrf
            <div class="form-group">
                <label for="code" class="col-sm-2 control-label">Code</label>
                <div class="col-sm-10">
                    <input value="{{$randomString}}" class="form-control" placeholder="code" autofocus="true"
                           name="code" type="text" id="code">
                </div>
            </div>
            <div class="form-group">
                <label for="value" class="col-sm-2 control-label">Value</label>
                <div class="col-sm-10">
                    <input class="form-control" placeholder="value" name="value" type="number" id="value">
                </div>
            </div>
            <div class="form-group">
                <a style="margin-right: 12px;" id="generate-coupon" href="{{ route('dashboards.coupons.create','generate') }}"
                   class="btn btn-sm btn-danger pull-left cancel_btm"> Generate </a>
                <button style="margin-right: 12px;" type="submit" class="btn btn-sm btn-info cancel_btm">{{__('Submit')}}</button>
            </div>
        </form>
    </div>
</div>


