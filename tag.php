<?php session_start(); ?>
<?php 
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/forms.php");
require_once("require/basic.php");
Secure( false );
if( Type() == "NONE" || !IsPlayer() ) { to_panel("&Not authorized to tag."); }
if( !is_game_started() ) { to_panel("&Can not tag before game is started."); }

if( isset( $_REQUEST["action"] ) )
{
	$code = trim($_REQUEST["code"]);
	$player = get_player_by_code($code);
	if( $player == NULL ) reload_self("&Not a valid player");
	$tagged = tag_player(ID(), $player["id"]);
	if( !$tagged ) reload_self("&Failed to tag player");
	if( IsHuman() )
	{
		update_type( ID(), "ZOMBIE" );
	}
	hvzmailf($player["email"], "tag", array( "killer_first" => First(), "killer_last" => Last(), "kill_time" => date('l F jS \a\t g:iA') ));
	$points = get_tag_score();
	reload_self("You have tagged {$player['first_name']} {$player['last_name']} for $points points.");
}
page_head();
?>
<h1>Report Tag</h1>
<?php
if( IsHuman() )
{
	echo "<strong>Warning! Reporting a tag will switch you to the zombie team. As a human you should only be using this form if someone has tagged you and they haven't reported it yet</strong><br/>";
}
if( IsAdmin() )
{
	echo "<i>As an ADMIN, it is advised that you use the Player List to zombify players.</i><br/>";
}
?>
<p>To report killing a human, you'll need their player code. Make sure you get it from them when you catch them.</p>
<form method="post" action="">
	Player Code:&nbsp;<input name="code" /><br/>
	<input type="submit" name="action" value="Tag" /><br/>
</form>
<strong>You have tagged:&nbsp;</strong>
<?php
$tags = get_tags(ID());
$list = array();
foreach($tags as $tag)
{
	array_push( $list, $tag["first_name"] . " " . $tag["last_name"] );
}
if(count($tags) == 0) echo "No one.";
else echo implode( ", ", $list );
page_foot();
?>