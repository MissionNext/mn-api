<div class="row">
    <div class="col-md-4 col-md-offset-3 custom-form">
        <h3 class="text-center">Editing language</h3>
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all(':message') as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <form class="custom-form ng-pristine ng-valid" role="form" enctype="multipart/form-data" method="post"
              action="{{ route('dashboards.coupons.update.post', ['coupon' => $item->id]) }}">
            @csrf
            <div class="form-group">
                <label for="code" class="col-sm-2 control-label">Code</label>
                <div class="col-sm-10">
                    <input value="{{$randomString??$item->code}}" class="form-control" placeholder="code" autofocus="true"
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
                <a style="margin-right: 2px;margin-top: 15px;    width: 32%;" href="{{ route('dashboards.coupons.index') }}" class="btn btn-sm btn-warning pull-left">
                    Cancel </a>
                <a style="margin-right: 2px;margin-top: 15px;    width: 32%;" id="generate-coupon" href="{{ route('dashboards.coupons.edit',['coupon' => $item->id,'generate']) }}"
                   class="btn btn-sm btn-danger pull-left"> Generate </a>
                <button style="margin-right: 2px;margin-top: 15px;    width: 32%;" type="submit" class="btn btn-sm btn-info">{{__('Submit')}}</button>
            </div>
        </form>
    </div>
</div>



