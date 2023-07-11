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
              action="{{ route('dashboards.languages.update.post', ['language' => $item->id]) }}">
            @csrf
            <div class="form-group">
                <select class="form-control" name="key">
                    @foreach ($languages as $key=>$val)
                        <option @if ($item->key === $key) selected @endif value="{{$key}}">{{$val}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <a href="{{ route('dashboards.languages.index') }}" class="btn btn-sm btn-warning pull-left cancel_btm">
                    Cancel </a>
                <button type="submit" class="btn btn-sm btn-info pull-right cancel_btm">{{__('Submit')}}</button>
            </div>
        </form>
    </div>
</div>



