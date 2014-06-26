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


<table class="table table-hover" ng-controller="SubscriptionController as subCtl">
    <thead>
    <tr>
        <th>User Role</th>
        <th>Partnership Level</th>
        <th>Price</th>
        <th>Term</th>
    </tr>
    </thead>

    <!--  BEGIN candidate  -->
    <tbody ng-repeat="config in subCtl.configs">
    <tr class="active">
        <td><% config.role.label  %></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr ng-repeat="info in config.partnership">
        <td></td>
        <td><% info.level || '-' %></td>
        <td><span><% info.price|currency %></span></td>
        <td><span><% info.term +' month' %></span></td>
    </tr>
    </tbody>
    <!-- END candidate    ?-->

</table>


@endsection
<script>
    window.CurrentApplication = JSON.parse('{{ $application->toJson() }}');
</script>