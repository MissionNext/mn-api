<style class="ng-scope">
    .sub-grace {
        background-color: #ffff00;
    }

    .sub-expired {
        background-color: #c30100;
    }

    .dashed {

    }

    .sortable {
        border-bottom: 1px dashed #000000;
        text-decoration: none;
        cursor: pointer;
    }

    .sortable-glyph {
        color: blue;
        cursor: pointer;
        font-size: small;
    }
</style>

<div class="user-list ng-scope">

    <div class="row">
        <div class="col-md-11">
            <h3>
                User list
            </h3>
        </div>
        <div class="col-md-1">
            {{--        <span class="pull-right"> {{ $applications->getTo() }} / {{ $applications->getTotal() }} </span>--}}
        </div>
    </div>
    <form class="ng-pristine ng-valid" role="form" enctype="multipart/form-data" method="get"
          action="{{ route('dashboards.users.index') }}">
        @method('get')
        @csrf
        <div class="row">
            <div class="col-md-12">
                <input
                    class="form-control ng-valid ng-dirty ng-valid-parse ng-touched"
                    placeholder="Search by username or email" name="search"
                    value="{{$search}}"
                    type="text">

            </div>
            <div class="col-md-12 pull-right">
                <h3>Filters:</h3>
            </div>
            <div class="col-md-3 pull-left">
                <label for="apps-select-id">By applications:</label>
                <select id="apps-select-id"
                        class="select2"
                        multiple="multiple"
                        data-placeholder="By applications"
                        name="apps_select[]"
                        style="width: 100%;">
                    @if (!empty($applications))
                        @foreach($applications as $key=>$val)
                            @if (!empty($apps_select) && isset($apps_select[$key]))
                                <option selected value="{{$key}}">{{$val}}</option>
                            @else
                                <option value="{{$key}}">{{$val}}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3 pull-left">
                <label for="role-select-id">By roles:</label>
                <select id="role-select-id"
                        class="select2"
                        multiple="multiple"
                        data-placeholder="By roles"
                        name="role_select[]"
                        style="width: 100%;">
                    <option @if (!empty($role_select) && isset($role_select[1]))  selected @endif value="1">Candidate
                    </option>
                    <option @if (!empty($role_select) && isset($role_select[2]))  selected @endif value="2">Receiving
                        Organization
                    </option>
                    <option @if (!empty($role_select) && isset($role_select[3]))  selected @endif value="3">Service
                        Organization
                    </option>
                </select>
            </div>
            <div class="col-md-3 pull-left">
                <label for="status-select-id">By status:</label>
                <select id="status-select-id"
                        class="select2"
                        multiple="multiple"
                        data-placeholder="By status"
                        name="status_select[]"
                        style="width: 100%;">
                    <option value="1" @if (!empty($status_select) && isset($status_select[1]))  selected @endif>Pending Approval</option>
                    <option value="2" @if (!empty($status_select) && isset($status_select[2]))  selected @endif>Active</option>
                    <option value="3" @if (!empty($status_select) && isset($status_select[3]))  selected @endif>Disabled</option>
                </select>
            </div>
            <div class="col-md-3 pull-left">
                <label for="sub-status-select-id">Subscription Status:</label>
                <select id="sub-status-select-id"
                        class="select2"
                        multiple="multiple"
                        data-placeholder="By Subscription Status"
                        name="sub_status_select[]"
                        style="width: 100%;">
                    <option @if (!empty($sub_status_select) && isset($sub_status_select['active']))  selected @endif value="active">Active</option>
                    <option @if (!empty($sub_status_select) && isset($sub_status_select['grace']))  selected @endif value="grace">Grace</option>
                    <option @if (!empty($sub_status_select) && isset($sub_status_select['expired']))  selected @endif value="expired">Expired</option>
                </select>
            </div>
            <div class="col-md-12 pull-right">
                <button style="margin: 15px;" type="submit"
                        class="btn btn-sm btn-info pull-left">{{__('Search')}}</button>
            </div>
        </div>
    </form>

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
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>
                        <a href="{{ URL::route('dashboards.users.index', ['username' => $username??'ASC']) }}">
                            <span style=" border-bottom: 1px dashed #000000;" class="sortable">Username</span>
                            @if (isset($active) && $active === 'username')
                                @if ($username === 'ASC')
                                    <span class="sortable-glyph glyphicon glyphicon-arrow-down"></span>
                                @elseif($username === 'DESC')
                                    <span class="sortable-glyph glyphicon glyphicon-arrow-up"></span>
                                @endif
                            @endif
                        </a>
                    </th>
                    <th>E-mail</th>
                    <th>Status</th>
                    <th>
                        <a href="{{ URL::route('dashboards.users.index', ['created_at' => $created_at??'ASC']) }}">
                            <span style=" border-bottom: 1px dashed #000000;" class="sortable">Created at</span>
                            @if (isset($active) && $active === 'created_at')
                                @if ($created_at === 'ASC')
                                    <span class="sortable-glyph glyphicon glyphicon-arrow-down"></span>
                                @elseif($created_at === 'DESC')
                                    <span class="sortable-glyph glyphicon glyphicon-arrow-up"></span>
                                @endif
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ URL::route('dashboards.users.index', ['updated_at' => $updated_at??'ASC']) }}">
                            <span style=" border-bottom: 1px dashed #000000;" class="sortable">Last login</span>
                            @if (isset($active) && $active === 'updated_at')
                                @if ($updated_at === 'ASC')
                                    <span class="sortable-glyph glyphicon glyphicon-arrow-down"></span>
                                @elseif($updated_at === 'DESC')
                                    <span class="sortable-glyph glyphicon glyphicon-arrow-up"></span>
                                @endif
                            @endif
                        </a>
                    </th>
                </thead>
                @if (isset($users))
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <a href="{{ URL::route('dashboards.users.edit', ['user' => $user->id]) }}">
                                    {{ $user->username }}
                                </a>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->status === 1 && $user->is_active === false)
                                    <span class="glyphicon glyphicon-warning-sign glyphicon-remove"></span>
                                @elseif($user->status === 0 && $user->is_active === true)
                                    <span class="glyphicon false glyphicon-ok"></span>
                                @elseif($user->status === 0 && $user->is_active === false)
                                    <span class="glyphicon false glyphicon-remove"></span>
                                @endif
                            </td>
                            <td>{{ $user->created_at }}</td>
                            <td>{{ $user->updated_at }}</td>
                        </tr>
                    @endforeach
                @endif
            </table>
            <div class="text-center">
                @if (isset($users))
                    {{ $users->onEachSide(5)->links() }}
                @endif
            </div>
        </div>
    </div>
</div>


