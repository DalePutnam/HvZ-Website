<?php
require_once("require/rgame.php");
require_once("require/secure.php");

$first = First();
$last = Last();
/*if ( Type() != "NONE" )
{
    $team = Team();
    echo "<div class='navbar-header'><a class='navbar-brand' href='#'>$first $last, $team</a></div>";
}
else
{
    echo "<div class='navbar-header'><a class='navbar-brand' href='#'>$first $last</a></div>";
}*/
echo "<div class='navbar-header col-md-2'><h4 style='margin-top: 15px; height: 25px; padding: 0; color: white'>Humans vs Zombies</h4></div>";
echo "<div class='navbar-collapse collapse'>";
echo "<ul class='nav navbar-nav navbar-right'>";

$imp = GetImpersonate();
if( $imp != NULL )
{
    echo "<li style='margin-top: 15px; height: 25px; padding: 0; padding-right: 10px; color: red'>IMPERSONATING $first $last</li>";
}
else
{
    echo "<li style='margin-top: 15px; height: 25px; padding: 0; padding-right: 10px; color: white'>$first $last</li>";
}
echo "</ul>";

echo "<ul class='nav navbar-nav'>";
if( !is_game_started() )
{

    echo "<li style='margin-top: 15px; height: 25px; padding: 0; padding-right: 10px; color: white'>Game will start on " . date('l F jS \a\t g:iA', strtotime(get_game_start())) . "</li>";
    //echo "<p>Game will start on " . date('l F jS \a\t g:iA', strtotime(get_game_start())) . "</p>";
}
if( is_maintenance() )
{
	echo "<li style='margin-top: 15px; height: 25px; padding: 0; color: red'>MAINTENANCE MODE ENABLED. SEE <a style='padding: 0; display: inline' href='game.php'>GAME SETTINGS</a> PAGE FOR MORE INFO</li>";
}

/*$imp = GetImpersonate();
if( $imp != NULL )
{
	//echo "<p><strong style='color:red'>YOU ARE CURRENTLY IMPERSONATING A USER. TO RETURN TO BEING AN ADMIN, CLICK <a href='panel.php?imp_end='>HERE</a></strong></p>";
    echo "<li style='margin-top: 15px; height: 25px; padding: 0; color: red'><a style='padding: 0; display: inline' href='panel.php?imp_end='>END IMPERSONATION</li>";
}*/
echo "</ul></div>";
?>