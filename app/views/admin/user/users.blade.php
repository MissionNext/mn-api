@extends('layout')

@section('title')
    Dashboard. Users
@endsection

@section('content')
<div ng-view></div>
@endsection

@section('javascripts')
@parent
{{ HTML::script(URL::asset('js/project/controllers/user.js')) }}

<script>
    var pagination = 10;
    var count = 1;


    $(document).ready(function() {
        $.post("{{ URL::route('getRoles') }}")
            .done(function(msg){
                console.log(msg);
                $('#apps-select-id').selectize({
                    plugins: ['remove_button'],
                    delimiter: ',',
                    maxItems: null,
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    options: msg['apps'],
                    create: false
                });
                $('#role-select-id').selectize({
                    plugins: ['remove_button'],
                    delimiter: ',',
                    maxItems: null,
                    valueField: 'id',
                    labelField: 'role',
                    searchField: 'role',
                    options: msg['roles'],
                    create: false
                });
            })
            .error(function(msg){
                console.log(msg);
            });
    });

    $('#apps-select-id').change(function() {
        count = 1;
        var selectAppValue = $(this).val();
        var selectRoleValue = $('#role-select-id').val();

        if (selectAppValue == '' && selectRoleValue == '' ) {
            location.reload();
        }
        $.post("{{ URL::route('userFilters') }}", {appId: selectAppValue, roleId: selectRoleValue, take: pagination } )
            .done(function(msg){
                $('#default-rezult').remove();
                $('.pagination-info').hide();
                $('#firter-rezult').html(msg);

                var totalCount = $('#first-result-table').data('usercount');
                if(count * pagination >= totalCount) {
                    $('#show-more-filtered-data').remove();
                }
            })
            .error(function(msg){
                console.log(msg);
            });
    });

    $('#role-select-id').change(function() {
        count = 1;
        var selectAppValue = $('#apps-select-id').val();
        var selectRoleValue = $(this).val();

        if (selectAppValue == '' && selectRoleValue == '' ) {
            location.reload();
        }
        $.post("{{ URL::route('userFilters') }}", {appId: selectAppValue, roleId: selectRoleValue, take: pagination } )
            .done(function(msg){
                $('#default-rezult').remove();
                $('.pagination-info').hide();
                $('#firter-rezult').html(msg);

                var totalCount = $('#first-result-table').data('usercount');
                if(count * pagination >= totalCount) {
                    $('#show-more-filtered-data').remove();
                }
            })
            .error(function(msg){
                console.log(msg);
            });
    });

    $('#firter-rezult').on('click', '#show-more-filtered-data', function(event) {
        var itemObj = $(event.target);
        var appId = $('#apps-select-id').val();
        var roleId = $('#role-select-id').val();
        var totalCount = $('#first-result-table').data('usercount');

        $.post("{{ URL::route('filteredUsersByEth') }}", {appId: appId, roleId: roleId, take: pagination, skip: count * pagination } )
            .done(function(msg){
                count++;
                $('#first-result-table').append(msg);
                if(count * pagination >= totalCount) {
                    itemObj.remove();
                }
            })
            .error(function(msg){
                console.log(msg);
            });
    });

</script>
@endsection
