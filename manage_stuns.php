<?php 
session_start();
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/basic.php");
require_once("require/rstun.php");
Secure( true );

if( isset( $_REQUEST["action"] ) )
{
	$action = $_REQUEST["action"];
	if( $action == "Ratify Stuns" )
	{
		ratify_stuns();
        set_alert("SUCCESS", "Stuns have been ratified.");
	}
    if( $action == "Recalculate Stuns" )
    {
        $result = recalculate_stuns();
        if($result === TRUE)
        {
            set_alert("SUCCESS", "Stuns have been recalculated");
        }
        else
        {
            set_alert("ERROR", $result);
        }
    }
	if( $action == "Delete" )
	{
		$id = $_REQUEST["id"];
		delete_stun($id);
        set_alert("SUCCESS", "Stun deleted.");
	}
	if( $action == "Add 12 Hours" )
	{
		$id = $_REQUEST["id"];
		add_half_day($id);
        set_alert("SUCCESS", "Time updated.");
	}
}

page_head();
?>
<h2>Stun Management</h2>
<h3>Recalculate Stuns</h3>
<p>Pressing this button will go through every stun in the database and re-calculate everyone's scores.</p>
<form method="post" action="">
    <input class="btn btn-default" type="submit" name="action" value="Recalculate Stuns" />
</form>
<p>To see debug information on the last recalculation, click <a href="log/recalculate.log">here</a>.</p>
<h3>Ratify Stuns</h3>
<p>Clicking the button below will take all un-ratified stuns (see below), and mark them as ratified. It will then trigger a score recalculation.</p>
<form method="post" action="">
    <input class="btn btn-default" type="submit" name="action" value="Ratify Stuns" />
</form>
<h3>Un-Ratified Stun List</h3>
<p>Below are the stuns that have not yet been ratified.</p>
<a id="showall" href='#'>Show All Comments</a>&nbsp;<a id="hideall" href='#'>Hide All Comments</a>
<div class="row">
<div class="col-sm-12 col-md-12">
<table class="stuns table table-striped table-bordered table-condensed">
<tr><th>Human Stunner</th><th>Stunned Zombie</th><th>Reported Time</th><th>Giving a point to...</th><th>Comment</th><th>Modify/Delete</th></tr>
<?php
$stuns = get_active_stuns( );
foreach( $stuns as $stun )
{
	echo "<tr><td>{$stun['killer_first']} {$stun['killer_last']}</td><td>{$stun['victim_first']} {$stun['victim_last']}</td><td>{$stun['time']}</td><td style='text-align: center;'>";
	if( $stun['helper_first'] != NULL )
	{
		echo "{$stun['helper_first']} {$stun['helper_last']}";
	}
	else
	{
		echo "--";
	}
	echo "</td><td class='comment'><a href='#'>Show/Hide</a><p class='comment'>{$stun['comment']}</p></td>";
	echo "<td>";
	echo "<form method='post' action=''>";
    echo "<input type='hidden' name='id' value='{$stun['id']}' />";
    echo "<input class='btn btn-danger' type='submit' name='action' value='Delete' />";
    echo "<input class='btn btn-primary' type='submit' name='action' value='Add 12 Hours' /></form>";
	echo "</td></tr>";
}
?>
</table>
</div>
</div>
<script type="text/javascript">
$("td.comment a").click( function() {
	$("~p.comment", this).toggle();
	return false;
});
$("p.comment").hide();
$("a#showall").click( function() {
	$("p.comment").show();
	return false;
});
$("a#hideall").click( function() {
	$("p.comment").hide();
	return false;
});
</script>
<?php
page_foot();
?>