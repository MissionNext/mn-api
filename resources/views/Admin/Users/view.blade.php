<button onclick="window.history.back();" type="button" class="btn btn-primary btn-sm">Back</button>
<h3>Profile Data:</h3>
<table class="table table-hover profile-table">
    <tbody>
    </tbody>
    <thead>
    <tr>
        <th>Username</th>
        <td>{{$data->username}}</td>
    </tr>
    <tr>
        <th>Registered</th>
        <td>{{$data->created_at}}</td>
    </tr>
    </thead>
    <!-- str_replace commands added by Nelson Dec-16-2016 -->
    <tbody>
    <tr>
        <th>First Name</th>
        <td>{{$data->profileData->first_name}}</td>
    </tr>
    <tr>
        <th>Last Name</th>
        <td>{{$data->profileData->last_name}}</td>
    </tr>
    <tr>
        <th>Email</th>
        <td>{{$data->profileData->email}}</td>
    </tr>
    <tr>
        <th>Country</th>
        <td>{{$data->profileData->country}}</td>
    </tr>
    <tr>
        <th>Best Phone Number</th>
        <td>{{$data->profileData->cell_phone}}</td>
    </tr>
    <tr>
        <th>Terms and Conditions</th>
        <td>{{$data->profileData->terms_and_conditions}}</td>
    </tr>
    <tr>
        <th>Marital status</th>
        <td>{{$data->profileData->marital_status}}</td>
    </tr>

    </tbody>
</table>
