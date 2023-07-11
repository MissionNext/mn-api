<div class="row">
    <div class="col-md-6 col-md-offset-3 custom-form">
        <h3 class="text-center"> Creating new administrator </h3>
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all(':message') as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <form class="custom-form ng-pristine ng-valid" role="form" enctype="multipart/form-data" method="post"
              action="{{ route('dashboards.administrators.store.post') }}">
            @csrf
            <div style="padding: 15px;" class="form-group">
                <label for="username" class="col-sm-2 control-label">Username</label>
                <div class="col-sm-10">
                    <input class="form-control" placeholder="Username" autofocus="true" name="username" type="text" id="username">
                </div>
            </div>
            <div style="padding: 15px;" class="form-group">
                <label for="email" class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10">
                    <input class="form-control" placeholder="Email"  name="email" type="text" id="email">
                </div>
            </div>
            <div style="padding: 15px;" class="form-group">
                <label for="password" class="col-sm-2 control-label">Password</label>
                <div class="col-sm-10">
                    <input class="form-control" placeholder="Password" name="password" type="password" value="" id="password">
                </div>
            </div>
            <div style="padding: 15px;" class="form-group">
                <label for="password_confirmation" class="col-sm-2 control-label">Password confirm</label>
                <div class="col-sm-10">
                    <input class="form-control" placeholder="Password confirm" name="password_confirmation" type="password" value="" id="password_confirmation">
                </div>
            </div>
            <div  style="padding: 15px;" class="form-group">
            <button  type="submit" class="btn btn-sm btn-info cancel_btm">{{__('Submit')}}</button>
            </div>
        </form>
    </div>
</div>

