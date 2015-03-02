<?php session_start(); ?>
<?php 
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/forms.php");
require_once("require/basic.php");
require_once("require/rstun.php");
Secure( false, 'HUMAN' );
if( !is_game_started() ) { to_panel("&Can not tag stun game is started."); }

if( isset( $_REQUEST["action"] ) )
{
	$action = $_REQUEST["action"];
	if( $action == "Report Stun" )
	{
		$code = trim($_REQUEST["code"]);
		$datetime = trim($_REQUEST["datetime"]);
		$comment = strip_tags(trim($_REQUEST["comment"]));
		$helper = trim($_REQUEST["helper"]);

        if($comment == "Enter a descriptive comment or your stun may be deleted!")
        {
            reload_self("&You failed to enter a descriptive comment! Put some effort in, will ya?");
        }

		$idhelper = NULL;
		if( $helper != "" )
		{
			$plhelper = get_player_by_email($helper);
			if( $plhelper != NULL )
			{
				$idhelper = $plhelper['id'];
			}
			else
			{
				reload_self("&There is a typo in your helper's email address. Please correct.");
			}
		}
		
		$player = get_player_by_code($code);
		if( $player == NULL ) reload_self("&Not a valid player");
		$stunned = stun_player(ID(), $player["id"], $datetime, $comment, $idhelper);
		if( $stunned === TRUE )
		{
			$count = get_num_stuns(ID(), $player["id"]);
			
			hvzmailf($player["email"], "stun", array("killer_first" => First(), "killer_last" => Last(), "kill_time" => date("l F jS \a\t g:iA", strtotime($datetime)), "comment" => $comment, "count" => $count));
			
			reload_self("You have stunned {$player['first_name']} {$player['last_name']}. You have reported stunning  {$player['first_name']} $count time(s) today. Points will be tallied and awarded at the end of each day.");
		}
		else
		{
			reload_self("&$stunned");
		}
	}
}

page_head();
?>
<h1>Report a Stun</h1>
<p>Use this form to report stunning a zombie (via nerf darts or socks). Note that you will require the zombies player code to report the stun, and remember that you are not safe while trying to collect this code from them in person. Make sure to secure the area.</p>
<p>In addition to entering the estimated time you stunned them, also leave a <strong>descriptive comment</strong> about the how and where. Both these will be e-mailed to the zombie so they can contest the stun if needed</p>
<p><strong>New: </strong>If you'd like, you can enter the e-mail address of another player below and one point you would have gained from this stun will be transferred to them. If not, leave it blank.</p>
<form method="post" action="">
	Player Code:&nbsp;<input name="code" /><br/>
	<strong>Note: Time is measured in 24-hour format</strong><br/>
	Approximate Time:&nbsp;<input name="datetime" id="datepicker" /><br/>
	Comment:&nbsp;<textarea name="comment" rows="5" cols="40">Enter a descriptive comment or your stun may be deleted!</textarea><br/>
	Email of Other Player to Transfer One Point (optional):&nbsp;<input name="helper" /><br/>
	<input type="submit" name="action" value="Report Stun" /><br/>
</form>
<script type="text/javascript">
$("#datepicker").datetimepicker();
</script>
<?php
// todo stun list
page_foot();
?>