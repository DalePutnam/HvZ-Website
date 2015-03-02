<?php
require_once("require/rgame.php");
require_once("require/secure.php");
echo "Welcome " . First() . " " . Last();
if( Type() != "NONE" ) echo ", " . Team();
if( is_maintenance() )
{
	echo "<p><strong style='color:red'>WARNING: WEBSITE IS IN MAINTENANCE MODE, REGULAR USERS CAN NOT LOG IN. THIS CAN BE CHANGED VIA THE <a href='game.php'>GAME SETTINGS</a> PAGE.</strong></p>";
}
if( !is_game_started() )
{
	echo "<p>Game will start on " . date('l F jS \a\t g:iA', strtotime(get_game_start())) . "</p>";
}
$imp = GetImpersonate();
if( $imp != NULL )
{
	echo "<p><strong style='color:red'>YOU ARE CURRENTLY IMPERSONATING A USER. TO RETURN TO BEING AN ADMIN, CLICK <a href='panel.php?imp_end='>HERE</a></strong></p>";
}
?>