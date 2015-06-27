<?php session_start(); ?>
<?php 
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/mail.php");
require_once("require/rplayers.php");

// Admin only page
Secure(true);

if( isset( $_REQUEST["action"] ) )
{
	$action = $_REQUEST["action"];
	if( $action == "Start Game" )
	{
		// Kill everyone in the OZ list (SQL query, no subscription updates)
		$ozlist = get_oz_list();
		$ids = implode( "' OR `id`='", $ozlist );
		$sql->query("UPDATE `hvz_players` SET `type`='ZOMBIE' WHERE `id`='" . $ids . "'");
		
		// Create tag entries
		$ids2 = "(NULL, '" . implode( "'), (NULL,'", $ozlist ) . "')";
		$sql->query("INSERT INTO `hvz_tags` (`killer`, `victim`) VALUES $ids2");
		
		// Set all other NONE players to HUMAN
		$sql->query("UPDATE `hvz_players` SET `type`='HUMAN' WHERE `type`='NONE'");
		
		// Ensure everyone is in the right mailing list
		fix_emails();
		
		// Set the game as started
		set_game_start(true);
		
		// Send out intro emails only not in maintenance mode
		if( !is_maintenance() )
		{
			hvzmailf("hvz-humans@csclub.uwaterloo.ca", "welcome_human", array());
			hvzmailf("hvz-zombies@csclub.uwaterloo.ca", "welcome_zombie", array());
		}

        set_alert("SUCCESS", "Game started.");
	}
	elseif( $action == "Stop Game" )
	{
		set_game_start(false);
        set_alert("SUCCESS", "Game stopped.");
	}
}

page_head();

if( !is_game_started() )
{
?>

<h2>Starting the Game</h2>
<h2>Checklist</h2>
<ol>
<li>Have you submitted a FEDS event form?</li>
<li>Have you contacted campus police?</li>
<li>
<p>
Have you selected an OZ list?<br/>
<?php
$list = get_oz_list();
$num = count($list);
if( $num > 0 ) echo "<i>$num OZ(s) have been selected</i>";
else echo "<strong>Warning:&nbsp;</strong>No OZs have been selected";
?>
</p>
</li>
</ol>

<strong>What exactly does pressing this button do?</strong><br/>
<ol>
<li>Kills all players in the OZ list</li>
<li>Creates tag records for these players so others can't claim their kill</li>
<li>Assigns all other players to team human</li>
<li>Runs the "Fix Subscriptions" algorithm to clear all mailing lists, and adds people to the right list</li>
<li>Sets the game as "started", enabling features such as tagging</li>
<li>Emails both teams' mailing lists with an introduction email following the <a href="edit_template.php?edit=welcome_human">welcome_human</a> and <a href="edit_template.php?edit=welcome_zombie">welcome_zombie</a> templates respectively. (NOTE: This step will be skipped if the website is in <a href="game.php">maintenance</a> mode</li>
</ol>
<form method="post" action="">
<input class="btn btn-success" type="submit" value="Start Game" name="action" />
</form>

<?php
}
else
{
?>
<h2>Starting the Game</h2>
<p>The game has already been started. If you would like to stop the game (disables tagging and other in-game systems) use the button below. You will be able to start the game later.</p>
<form method="post" action="">
    <input class="btn btn-danger" type="submit" value="Stop Game" name="action" />
</form>
<?php
}
page_foot();
?>