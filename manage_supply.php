<?php 
session_start();
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/basic.php");
require_once("require/rsupply.php");
require_once("require/rstun.php");
require_once("require/forms.php");
Secure( true );

if( isset( $_REQUEST["action"] ) )
{
	$action = $_REQUEST["action"];
	if( $action == "Generate" )
	{
		$num = trim($_REQUEST["num"]);
		$codes = generate_supply( $num );
		if( count($codes) == 0 ) set_alert("ERROR", "You must enter a valid number greater than zero");
        set_alert("SUCCESS", array( "codes" => implode(",", $codes) ) );
	}
    elseif($action == "Give Points")
    {
        $points = trim($_REQUEST["points"]);
        $team = trim($_REQUEST["team"]);
        $result = give_team_points($team, $points);

        if($result !== TRUE)
        {
            set_alert("ERROR", $result);
        }
        else set_alert("SUCCESS", "Gave $points points to team $team");
    }
}

page_head();
?>
<h2>Manage Supply Codes</h2>
<h3>Give Team Score</h3>
<p>Want to give a team some cool scores? Just click here!</p>
<div class="row">
    <div class="col-md-4">
        <form method="post" action="">
            <div class="form-group">
                <label>Points To Give</label>
                <input class="form-control" name="points" />
            </div>
            <div class="form-group">
                <label>Team</label>
                <?php team_select("team", "NONE");?>
            </div>
            <input class="btn btn-default" type="submit" name="action" value="Give Points" />
        </form>
    </div>
</div>
<h3>Generate</h3>
<p>
    Use this form to generate batches of new supply codes.
    New codes will be shown here in a separate list, but be warned: Once you leave the page the new codes will be interspersed with the existing ones in the list below.
    Be sure to write them down right after generating them.
</p>
<div class="row">
    <div class="col-md-4">
        <form method="post" action="">
            <div class="form-group">
                <label>Number of Codes</label>
                <input class="form-control" name="num" value="0"/>
            </div>
            <input class="btn btn-default" type="submit" name="action" value="Generate"/>
        </form>
    </div>
</div>
<?php
if( isset( $_REQUEST["codes"] ) )
{
	echo "<h3>New Codes</h3>";
	$codes = explode( ",", $_REQUEST["codes"] );
	foreach( $codes as $code )
	{
		echo "$code<br/>";
	}
}
?>
<h3>List</h3>
<div class="row">
<div class="col-md-4">
<table class="supply table table-striped table-bordered table-condensed"><tr><th>Supply Code</th><th>Captured By</th></tr>
<?php
$codes = get_supply();
foreach( $codes as $code )
{
	echo "<tr><td>{$code['code']}</td><td>";
	if( isset( $code['first_name'] ) )
	{
		echo $code['first_name'] . " " . $code['last_name'];
	}
	echo "</td></tr>";
}
?>
</table>
</div>
</div>
<?php
page_foot();
?>