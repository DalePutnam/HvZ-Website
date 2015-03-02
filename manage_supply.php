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
		if( count($codes) == 0 ) reload_self("&You must enter a valid number greater than zero");
		reload_self( array( "codes" => implode(",", $codes) ) );
	}
    elseif($action == "Give Points")
    {
        $points = trim($_REQUEST["points"]);
        $team = trim($_REQUEST["team"]);
        $result = give_team_points($team, $points);

        if($result !== TRUE)
        {
            reload_self("&" . $result);
        }
        else reload_self("Gave $points points to team $team");
    }
}

page_head();
?>
<h1>Manage Supply Codes</h1>
<h2>Give Team Score</h2>
Want to give a team some cool scores? Just click here!
<form method="post" action="">
    Points To Give:&nbsp;<input name="points" /><br/>
    Team:&nbsp;<?php team_select("team", "NONE");?><br/>
    <input type="submit" name="action" value="Give Points" />
</form>
<h2>Generate</h2>
<p>Use this form to generate batches of new supply codes. New codes will be shown here in a separate list, but be warned: Once you leave the page the new codes will be interspersed with the existing ones in the list below. Be sure to write them down right after generating them.</p>
<form method="post" action="">
Number of Codes:&nbsp;<input name="num" value="0"/><br/>
<input type="submit" name="action" value="Generate"/>
</form>
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
<h2>List</h2>
<table class="supply"><tr><th>Supply Code</th><th>Captured By</th></tr>
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
<?php
page_foot();
?>