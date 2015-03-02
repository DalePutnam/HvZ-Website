<?php session_start(); ?>
<?php 
require_once("require/secure.php");
require_once("require/basic.php");
require_once("require/config.php");
Secure( false, NULL, true );

if( isset($_REQUEST["action"]) )
{
	$action = $_REQUEST["action"];
	if( $action == "Accept" )
	{
		$sql->query("UPDATE `hvz_players` SET `waiver`=1 WHERE `id`='" . ID() . "'");
		to_panel();
	}
	elseif( $action == "Reject" )
	{
		header("Location: login.php?logout=&reason=" . urlencode("You must accept the waiver to play."));
		exit();
	}
}

if( HasSignedWaiver() )
{
	to_panel();
}

page_head();

$waiver = file_get_contents( $templates_dir . "waiver.txt" );
echo "<h1>Sign the Waiver</h1>";
echo "<p>You <strong>must</strong> agree to the following waiver before you are allowed to play the game. Please read it carefully and click accept below.</p>";
echo "<p>$waiver</p>";
?>
<form method="post" action="">
	<input type="submit" name="action" value="Accept" />
	<input type="submit" name="action" value="Reject" />
</form>
<?php
page_foot();
?>