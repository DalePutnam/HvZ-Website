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
            set_alert("ERROR", "You failed to enter a descriptive comment! Put some effort in, will ya?");
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
                set_alert("ERROR", "There is a typo in your helper's email address. Please correct.");
			}
		}
		
		$player = get_player_by_code($code);
		if( $player == NULL ) set_alert("ERROR", "Not a valid player");
		$stunned = stun_player(ID(), $player["id"], $datetime, $comment, $idhelper);
		if( $stunned === TRUE )
		{
			$count = get_num_stuns(ID(), $player["id"]);
			
			hvzmailf($player["email"], "stun", array("killer_first" => First(), "killer_last" => Last(), "kill_time" => date("l F jS \a\t g:iA", strtotime($datetime)), "comment" => $comment, "count" => $count));

            set_alert("SUCCESS", "You have stunned {$player['first_name']} {$player['last_name']}. You have reported stunning  {$player['first_name']} $count time(s) today. Points will be tallied and awarded at the end of each day.");
		}
		else
		{
            set_alert("ERROR", "$stunned");
		}
	}
}

page_head();
?>
<h2>Report a Stun</h2>
<p>Use this form to report stunning a zombie (via nerf darts or socks). Note that you will require the zombies player code to report the stun, and remember that you are not safe while trying to collect this code from them in person. Make sure to secure the area.</p>
<p>In addition to entering the estimated time you stunned them, also leave a <strong>descriptive comment</strong> about the how and where. Both these will be e-mailed to the zombie so they can contest the stun if needed</p>
<p><strong>New: </strong>If you'd like, you can enter the e-mail address of another player below and one point you would have gained from this stun will be transferred to them. If not, leave it blank.</p>

<div class="row">
<div class="col-md-4">
<form method="post" action="">
    <div class="form-group">
        <input class="form-control" name="code" placeholder="Player Code" required/>
    </div>
    <div class="form-group">
        <label>Note: Time is measured in 24-hour format</label>
        <input class="form-control" name="datetime" id="datepicker" placeholder="Approximate Time" required/>
    </div>
    <div class="form-group">
	    <textarea class="form-control" name="comment" rows="5" cols="40">Enter a descriptive comment or your stun may be deleted!</textarea>
    </div>
    <div class="form-group">
	    <input class="form-control" name="helper" placeholder="Email of Accomplice (optional)"/>
    </div>
    <div class="form-group">
	    <input class="btn btn-default" type="submit" name="action" value="Report Stun" />
    </div>
</form>
</div>
</div>
<script type="text/javascript">
$("#datepicker").datetimepicker();
</script>
<?php
// todo stun list
page_foot();
?>