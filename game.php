<?php session_start(); ?>
<?php 
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rgame.php");
require_once("require/forms.php");
require_once("require/rstun.php");

// Admin only page
Secure(true);

if( isset( $_REQUEST["action"] ) )
{
	$action = $_REQUEST["action"];
	if( $action == "Turn On" )
	{
		set_maintenance( true );
		reload_self("&Maintenance mode enabled");
	}
	elseif( $action == "Turn Off" )
	{
		set_maintenance( false );
		reload_self("Maintenance mode disabled");
	}
	elseif( $action == "Update Score Settings" )
	{
		$spz = trim($sql->real_escape_string(trim($_REQUEST["score_per_zed"])));
		$sm = trim($sql->real_escape_string(trim($_REQUEST["score_milestones"])));
		$ss = intval(trim($sql->real_escape_string($_REQUEST["supply_score"])));
		$ts = intval(trim($sql->real_escape_string($_REQUEST["tag_score"])));
		$result = $sql->query("UPDATE `hvz_game_info` SET `score_milestones` = '$sm', `score_per_zed`='$spz', `score_per_supply`='$ss', `score_per_tag`='$ts'");
		if( !$result ) reload_self("&Failed to update score information due to an internal error. Please contact the webmaster.");
		else reload_self("Updated score information.");
	}
	elseif( $action == "Update Dates" )
	{
		$gstart = strtotime($_REQUEST["game_start"]);
		$gend = strtotime($_REQUEST["game_end"]);
		$rstart = strtotime($_REQUEST["reg_start"]);
		$rend = strtotime($_REQUEST["reg_end"]);
		
		if( $gstart == FALSE || $gend == FALSE || $rstart == FALSE || $rend == FALSE )
		{
			reload_self("&One of the entered dates is invalid.");
		}
		$gstart = sql_date($gstart);
		$gend = sql_date($gend);
		$rstart = sql_date($rstart);
		$rend = sql_date($rend);
		$result = $sql->query("UPDATE `hvz_game_info` SET `start_date` = '$gstart', `end_date` = '$gend', `reg_start`='$rstart', `reg_end`='$rend'");
		if( !$result ) reload_self("&Internal error. Please contact a webmaster.");
		reload_self("Dates updated.");
	}
	elseif( $action == "Upload Rules" )
	{
		if( $_FILES["file"]["error"] > 0 )
		{
			reload_self("&Failed to upload file.");
		}
		elseif( $_FILES["file"]["type"] != "application/pdf" )
		{
			reload_self("&File must be a PDF.");
		}
		else
		{
			global $rules_file;
			$result = move_uploaded_file($_FILES["file"]["tmp_name"], $rules_file);
			if( $result == FALSE ) reload_self("&Failed to copy file.");
			reload_self("Rules document uploaded successfully.");
		}
	}
	elseif( $action == "Upload Map" )
	{
		if( $_FILES["file"]["error"] > 0 )
		{
			reload_self("&Failed to upload file.");
		}
		elseif( $_FILES["file"]["type"] != "image/png" )
		{
			reload_self("&File must be a PNG.");
		}
		else
		{
			global $rules_file;
			$result = move_uploaded_file($_FILES["file"]["tmp_name"], $map_file);
			if( $result == FALSE ) reload_self("&Failed to copy file.");
			reload_self("Map uploaded successfully.");
		}
	}
    elseif($action == "Delete Score Entry")
    {
        if(count(get_stun_scores()) == 1)
        {
            reload_self("&Can't Delete Last Get Scoring Entry!");
        }
        else
        {
            $id = $_REQUEST["id"];
            remove_stun_scores($id);
        }
        recalculate_stuns();
        reload_self("Removed entry.");
    }
    elseif($action == "Add Points Per Stun Entry")
    {
        $scores = $_REQUEST["scores"];
        $date = $_REQUEST["date"];

        add_stun_scores($scores, $date);
        recalculate_stuns();

        reload_self("Added entry.");
    }
}

page_head();
$m = is_maintenance();
?>

<h1>Game Settings</h1>
<h2>Maintenance Mode</h2>
<p>During Maintenance Mode only Admins are permitted to log in. All other players will be told to wait for maintenance mode to end.</p>
<form action="" method="post">
Maintenance mode:&nbsp;<input type="submit" name="action" value="<?php if($m) { echo "Turn Off"; } else { echo "Turn On"; } ?>" /><br/>
</form>
<h2>Date Settings</h2>
<p>These are mostly used for cosmetic purposes (included in e-mails, etc) but in some cases (such as stun reporting) they are used to validate user input.</p>
<form action="" method="post">
Registration Start:&nbsp;<input class="date" name="reg_start" value="<?php echo date("m/d/Y H:i", php_reg_start());?>" /><br/>
Registration End:&nbsp;<input class="date" name="reg_end" value="<?php echo date("m/d/Y H:i", php_reg_end());?>" /><br/>
Game Start:&nbsp;<input class="date" name="game_start" value="<?php echo date("m/d/Y H:i", php_game_start());?>" /><br/>
Game End:&nbsp;<input class="date" name="game_end" value="<?php echo date("m/d/Y H:i", php_game_end());?>" /><br/>
<input name="action" value="Update Dates" type="submit"/>
</form>
<h2>Score Settings</h2>
<form action="" method="post">
<p>Write a good explanation about how points per stun works, but for now this.</p>
    <?php stun_value_editor(get_stun_scores()); ?><br/>
<p>Milestones are score brackets that can be used for giving out score-based rewards. The first milestone represents the minimum point value for being 'supplied' and not dying on the final day. The other values (comma-separated) can be used for whatever the mods wish.</p>
Milestones:&nbsp;<input name="score_milestones" value="<?php echo implode(",", get_score_milestones());?>" /><br/>
<p>Points per supply code is the number of points a player gets when they cash in a supply code. This can be changed throughout the week.</p>
Points Per Supply Code:&nbsp;<input name="supply_score" value="<?php echo get_supply_score(); ?>" /><br/>
<p>Points per tag is the number of points scored by a zombie when they tag a human</p>
Points Per Tag:&nbsp;<input name="tag_score" value="<?php echo get_tag_score(); ?>" /><br/>
<input name="action" type="submit" value="Update Score Settings" />
</form>
<h2>Rules</h2>
<p>Use this to upload a new rules document (must be a PDF).</p>
<form action="" method="post" enctype="multipart/form-data">
File:&nbsp;<input type="file" name="file" /><br/>
<input type="submit" value="Upload Rules" name="action" />
</form>
<h2>Map</h2>
<p>Use this to upload a new map (must be a PNG).</p>
<form action="" method="post" enctype="multipart/form-data">
File:&nbsp;<input type="file" name="file" /><br/>
<input type="submit" value="Upload Map" name="action" />
</form>
<script type="text/javascript">
$("input.date").datetimepicker();
</script>

<?php
page_foot();
?>