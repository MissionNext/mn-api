<div class="row">
    <div class="col-md-offset-4 col-md-4">
        <h3> Administrative Home</h3>
        <p>Welcome&nbsp;to&nbsp;the&nbsp;MissionNext&nbsp;Application&nbsp;Programming&nbsp;Interface&nbsp;(API)</p>
        <p>The MissionNext Server Time is <?= date("M j, Y H:i:s") ?></p>
        
        <?php
        if (isset($_POST['username'])) {
		$username = $_POST['username'];
		}
		$username = "webmaster";
        print ("<p>Current user is $username </p><br/>");
        print ("<p align='center'>Current PHP version: ". phpversion(). "</p>");
        print ("<p align='center'>Server IP Address: $_SERVER[SERVER_ADDR]</p>");
        // print ("<p align='center'>PostgreSQL Database: $_SERVER[DB_DATABASE]</p>");
        // print_r($_SERVER);
        ?>
        <p><font color="#FFFFFF">/api/resources/views/admin/adminHomepage.blade.php</font></p><br/>
    </div>
</div>


