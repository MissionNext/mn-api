<div class="row">
    <div class="col-md-11">
        <h3>
            Subscription Management : {{$application->name}}
        </h3>
    </div>
</div>

<style>
    .ng-invalid.ng-dirty {
        border-color: red;
    }

    .ng-valid.ng-dirty {
        border-color: green;
    }

    td {
        width: 250px;
        height: 40px;
    }
</style>

<div class="sub-config ng-scope">
    <form class="custom-form ng-pristine ng-valid" role="form" enctype="multipart/form-data" method="post"
          action="{{ route('dashboards.subscriptions.update.post',['application' => $application->id]) }}">
        @csrf
        <div class="col-sm-3">
            <p>
                <label for="subscription_discount">Discount % </label>
                <input name="subscriptionDiscount" type="number" min="0" id="subscription_discount" value="{{$globalConfig[0]->value}}">
            </p>
        </div>
        <div class="col-sm-3">
            <p>
                <label for="con-fee">Convinience fee $ </label>
                <input name="conFee" type="number" min="0" id="con-fee" value="{{$globalConfig[2]->value}}">
            </p>
        </div>
        <div class="col-sm-3">
            <p>
                <label for="grace-period">Grace Period (days) </label>
                <input name="gracePeriod" type="number" min="0" id="grace-period" value="{{$globalConfig[1]->value}}">
            </p>
        </div>

        <div class="col-sm-3">
            <button style="    margin-top: 25px;" type="submit" class="btn btn-sm btn-info">{{__('Update')}}</button>
        </div>
        <div class="col-sm-12">
        <h4 style="margin: 40px 0;">
            Current subscription plans:
        </h4>
        </div>
        <table class="table table-hover col-sm-12">
            <thead>
            <tr>
                <th>User Role</th>
                <th>Status</th>
                <th>Partnership Level</th>
                <th>Monthly price</th>
                <th>Annual price</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Receiving Organization</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @foreach($organizations as $organization)
                <tr>
                    <td></td>
                    <td>
                        <input name="organization_partnership_status[{{$organization->partnership}}]"
                               {{ $organization->partnership ==='basic'? 'disabled': null }}
                               {{ $organization->partnership_status? 'checked': null }}
                               type="checkbox">
                    </td>
                    <td>{{$organization->partnership}}</td>
                    <td>
                        <span>$</span>
                        <input class="p-price" type="number"
                               name="organization_price_month[{{$organization->partnership}}]" min="0"
                               value="{{$organization->price_month}}">
                    </td>
                    <td>
                        <span>$</span>
                        <input class="p-price" type="number"
                               name="organization_price_year[{{$organization->partnership}}]" min="0"
                               value="{{$organization->price_year}}">
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tbody>
            <tr>
                <td>Service Organization</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            <tr>
                <td></td>
                <td></td>
                <td>-</td>
                <td>
                    <span>$</span>
                    <input class="p-price" type="number" name="agency_price_month" min="0"
                           value="{{$agency->price_month}}">
                </td>
                <td>
                    <span>$</span>
                    <input class="p-price" type="number" name="agency_price_year" min="0"
                           value="{{$agency->price_year}}">
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td>Candidate</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>-</td>
                <td>
                    <span>$</span>
                    <input class="p-price" type="number" name="candidate_price_month" min="0"
                           value="{{$candidate->price_month}}">
                </td>
                <td>
                    <span>$</span>
                    <input class="p-price" type="number" name="candidate_price_year" min="0"
                           value="{{$candidate->price_year}}">
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
