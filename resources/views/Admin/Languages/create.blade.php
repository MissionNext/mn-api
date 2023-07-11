<div class="row">
    <div class="col-md-4 col-md-offset-3 custom-form">
        <h3 class="text-center"> Creating new language </h3>
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all(':message') as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <form class="custom-form ng-pristine ng-valid" role="form" enctype="multipart/form-data" method="post"
              action="{{ route('dashboards.languages.store.post') }}">
            @csrf
            <div class="form-group">
            <select class="form-control" name="key">
                @foreach ($languages as $key=>$val)
                    <option value="{{$key}}">{{$val}}</option>
                @endforeach
            </select>
            </div>
                <div class="form-group">
            <button type="submit" class="btn btn-sm btn-info">{{__('Submit')}}</button>
                </div>
        </form>
    </div>
</div>

