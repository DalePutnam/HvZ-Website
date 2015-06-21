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
		reload_self("Stuns have been ratified.");
	}
    if( $action == "Recalculate Stuns" )
    {
        $result = recalculate_stuns();
        if($result === TRUE)
        {
            reload_self("Stuns have been recalculated");
        }
        else
        {
            reload_self("&" . $result);
        }
    }
	if( $action == "Delete" )
	{
		$id = $_REQUEST["id"];
		delete_stun($id);
		reload_self("Stun deleted.");
	}
	if( $action == "Add 12 Hours" )
	{
		$id = $_REQUEST["id"];
		add_half_day($id);
		reload_self("Time updated.");
	}
}

page_head();
?>
<h1>Stun Management</h1>
<h2>Recalculate Stuns</h2>
<p>Pressing this button will go through every stun in the database and re-calculate everyone's scores.</p>
<form method="post" action=""><input type="submit" name="action" value="Recalculate Stuns" /></form>
<p>To see debug information on the last recalculation, click <a href="log/recalculate.log">here</a>.</p>
<h2>Ratify Stuns</h2>
<p>Clicking the button below will take all un-ratified stuns (see below), and mark them as ratified. It will then trigger a score recalculation.</p>
<form method="post" action=""><input type="submit" name="action" value="Ratify Stuns" /></form>
<h2>Un-Ratified Stun List</h2>
<p>Below are the stuns that have not yet been ratified.</p>
<a id="showall" href='#'>Show All Comments</a>&nbsp;<a id="hideall" href='#'>Hide All Comments</a>
<table class="stuns">
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
	echo "<form method='post' action=''><input type='hidden' name='id' value='{$stun['id']}' /><input type='submit' name='action' value='Delete' /><input type='submit' name='action' value='Add 12 Hours' /></form>";
	echo "</td></tr>";
}
?>
</table>
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