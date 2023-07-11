<div class="row ">
    <div class="col-md-2 pull-right">
        <a class="btn btn-success btn-sm" href="{{ URL::route('dashboards.languages.create')}}"><span class="glyphicon glyphicon-plus"> </span> Add App</a>
    </div>
</div>

<div class="row">
    <div class="col-md-11">
        <h3>
            Languages list
        </h3>
    </div>
    <div class="col-md-1">
{{--        <span class="pull-right"> {{ $applications->getTo() }} / {{ $applications->getTotal() }} </span>--}}
    </div>
</div>

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

<table class="table table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Language key</th>
            <th>Language</th>
            <th></th>
        </tr>
    </thead>
    @foreach($languages as $language)
        <tr>
            <td>{{ $language->id }}</td>
            <td>{{ $language->key }}</td>
            <td>{{ $language->name }}</td>
            <td class="text-center">
                <a href="{{ URL::route('dashboards.languages.edit', ['language' => $language->id]) }}" class="btn-warning btn btn-xs">
                    <span class="glyphicon glyphicon-edit"> </span> Edit
                </a>
                <form class="pull-right ng-pristine ng-valid" role="form" enctype="multipart/form-data" method="post"
                      action="{{ route('dashboards.languages.delete.delete', ['language' => $language->id]) }}">
                    @csrf
                    @method('DELETE')
                    <input type="submit" class="btn btn-xs btn-danger" value=" Delete" onclick=' return confirm("confirm delete language {{ $language->name }}?")' >
                </form>
            </td>
        </tr>
    @endforeach
</table>

<div class="text-center">
{{--    {{ $applications->links() }}--}}
</div>

