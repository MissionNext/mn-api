<div class="row">
    <div class="col-md-4 col-md-offset-3 custom-form">
        <h3 class="text-center"> Creating new application </h3>
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all(':message') as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <form class="custom-form ng-pristine ng-valid" role="form" enctype="multipart/form-data" method="post"
              action="{{ route('dashboards.application.store.post') }}">
            @csrf
            <div class="form-group">
                <input type="text" name="name" required class="form-control" autofocus="true"
                       placeholder="application name">
            </div>
            <div class="form-group">
                <input type="text" name="private_key" required class="form-control"
                       placeholder="your app id">
            </div>
            <div class="form-group">
                <input type="text" name="public_key" required class="form-control"
                       placeholder="your private key">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-sm btn-info cancel_btm">{{__('Submit')}}</button>
            </div>
        </form>
    </div>
</div>
<p align="center">Caution: Any new application must be added to both the production AND stage site. </p>
<p align="center">Needed to be sure applicaiton IDs are identical. (Note added by Nelson Aug 2018)</p>

