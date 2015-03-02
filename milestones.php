<?php 
session_start();
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/basic.php");
require_once("require/rgame.php");
Secure( true );

$nmiles = get_num_milestones();
$values = get_score_milestones();

if( isset($_REQUEST["action"]) )
{
	$action = $_REQUEST["action"];
	if( $action == "Kill Unsupplied Humans" )
	{
		if( count($values) <= 0 ) reload_self("&No milestones configured.");
		kill_humans_below_score( $values[0] );
		reload_self("Un-Supplied Humans killed.");
	}
}

page_head();
?>
<h1>Milestone Summary</h1>
<p>This page shows all <strong>humans</strong> who have surpassed the first milestone and beyond.</p>
<h2>Kill Un-Supplied</h2>
<p>Hitting this button will kill all humans <i>(<?php echo count_humans_below_score($values[0]); ?>)</i> who have not achieved the first milestone.</p>
<form method="post" action="">
<input type="submit" value="Kill Unsupplied Humans" name="action" />
</form>
<h2>Milestone Lists</h2>
<?php
for( $i = 0; $i < $nmiles; $i++ )
{
	$players = get_players_for_milestones($i);
	echo "<h2>Milestone " . ($i+1) . " (" . $values[$i] . " points)";
	if( count($players) > 0 )
		echo "&nbsp;<i>(" . count($players) . ")</i>";
	echo "</h2>";
	if( count($players) > 0 )
	{
		echo "<table class='milestone'><tr><th>Name</th><th>E-Mail</th></tr>";
		foreach($players as $player)
		{
			echo "<tr><td>{$player['first_name']} {$player['last_name']}</td><td>{$player['email']}</td></tr>";
		}
		echo "</table>";
	}
	else echo "<p>No players in this bracket.</p>";
}
page_foot();
?>