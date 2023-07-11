<div class="row">
    <div class="col-md-offset-4 col-md-4">
        <h3> Administrative Home</h3>
        <p>Welcome&nbsp;to&nbsp;the&nbsp;MissionNext&nbsp;Application&nbsp;Programming&nbsp;Interface&nbsp;(API)</p>
        <p>The MissionNext Server Time is <?= date("M j, Y H:i:s") ?></p>
        <p>Current user is <font color="#000000">{{ $username }}</font></p><br/>
        <p align="center">
            <a href="{{ URL::route('dashboards.coupons.index') }}">Manage Coupons</a>
        </p>
        <p><font color="#FFFFFF">/api/app/views/admin/adminHomepage.blade.php</font></p><br/>
    </div>
</div>


