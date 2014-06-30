@extends('layout')

@section('title')
Dashboard. Subscription. Config
@endsection

@section('content')

<div class="row">
    <div class="col-md-11">
        <h3>
            Subscription Management
        </h3>
        <h4>
            {{$application->name}}
        </h4>
        <h4 style="margin: 40px 0;">
            Current subscription plans:
        </h4>
    </div>
</div>
<style>
  .ng-invalid.ng-dirty {
      border-color: red;
  }
   .ng-valid.ng-dirty{
       border-color: green;
   }
</style>
<table class="table table-hover" ng-controller="SubscriptionController as subCtl">
    <thead>
    <tr>
        <th>User Role</th>
        <th>Partnership Level</th>
        <th>Price Month</th>
        <th>Price Year</th>
    </tr>
    </thead>

    <!--  BEGIN candidate  -->
    <tbody ng-repeat="config in subCtl.configs">
    <tr class="active">
        <td><% config . role . label %></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr ng-repeat="info in config.partnership">
        <td></td>
        <td><% info . level || '-' %></td>
        <td><span ng-hide="subCtl.editingMonth[$parent.$index][$index]" ng-click="subCtl.editPrice($parent.$index, $index, 'month')"><% info.price_month  | currency  %></span>
            <input class="p-price"
                   ng-show="subCtl.editingMonth[$parent.$index][$index]" type="number" min="0"
                   ng-model="info.price_month" value=""  ng-model-options="{ updateOn: 'blur' }" />
        </td>
        <td><span ng-hide="subCtl.editingYear[$parent.$index][$index]" ng-click="subCtl.editPrice($parent.$index, $index, 'year')"><% info.price_year  | currency  %></span>
            <input class="p-price"
                   ng-show="subCtl.editingYear[$parent.$index][$index]" type="number" min="0"
                   ng-model="info.price_year" value=""  ng-model-options="{ updateOn: 'blur' }" />
        </td>
    </tr>
    </tbody>
    <!-- END candidate    ?-->

</table>


@endsection
@section('javascripts')
@parent
<script>
    window.CurrentApplication = JSON.parse('{{ $application->toJson() }}');
</script>
{{ HTML::script(URL::asset('project/js/controllers/subscription/config.js')) }}


@endsection

