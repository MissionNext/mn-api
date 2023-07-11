<div class="row">
    <div class="col-md-6 col-md-offset-3 custom-form">
        <h3 class="text-center">Editing language</h3>
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all(':message') as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        <form class="custom-form ng-pristine ng-valid" role="form" enctype="multipart/form-data" method="post"
              action="{{ route('dashboards.administrators.update.post', ['administrator' => $item->id]) }}">
            @csrf
            <div  style="padding: 15px;" class="form-group">
                <label for="username" class="col-sm-2 control-label">Username</label>
                <div class="col-sm-10">
                    <input value="{{$item->username}}" class="form-control" placeholder="Username" autofocus="true" name="username" type="text" id="username">
                </div>
            </div>
            <div style="padding: 15px;" class="form-group">
                <label for="email" class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10">
                    <input value="{{$item->email}}" class="form-control" placeholder="Email"  name="email" type="text" id="email">
                </div>
            </div>
            <div style="padding: 15px;" class="form-group">
                <label for="password" class="col-sm-2 control-label">New Password</label>
                <div class="col-sm-10">
                    <input class="form-control" placeholder="New Password" name="password" type="password" value="" id="password">
                </div>
            </div>
            <div style="padding: 15px;" class="form-group">
                <label for="password_confirmation" class="col-sm-2 control-label">Repeat Password</label>
                <div class="col-sm-10">
                    <input class="form-control" placeholder="Repeat Password" name="password_confirmation" type="password" value="" id="password_confirmation">
                </div>
            </div>
            <div  style="padding: 15px;" class="form-group">
            <a href="{{ route('dashboards.administrators.index') }}" class="btn btn-sm btn-warning pull-left cancel_btm">
                Cancel </a>
            <button type="submit" class="btn btn-sm btn-info pull-right cancel_btm">{{__('Submit')}}</button>
            </div>
        </form>
    </div>
</div>



