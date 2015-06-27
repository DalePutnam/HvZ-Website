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
	if( $action == "Enable Maintenance Mode" )
	{
		set_maintenance( true );
        set_alert("SUCCESS", "Maintenance mode enabled");
	}
	elseif( $action == "Disable Maintenance Mode" )
	{
		set_maintenance( false );
        set_alert("SUCCESS", "Maintenance mode disabled");
	}
	elseif( $action == "Update Score Settings" )
	{
		$spz = trim($sql->real_escape_string(trim($_REQUEST["score_per_zed"])));
		$sm = trim($sql->real_escape_string(trim($_REQUEST["score_milestones"])));
		$ss = intval(trim($sql->real_escape_string($_REQUEST["supply_score"])));
		$ts = intval(trim($sql->real_escape_string($_REQUEST["tag_score"])));
		$result = $sql->query("UPDATE `hvz_game_info` SET `score_milestones` = '$sm', `score_per_zed`='$spz', `score_per_supply`='$ss', `score_per_tag`='$ts'");
		if( !$result ) set_alert("ERROR", "Failed to update score information due to an internal error. Please contact the webmaster.");
		else set_alert("SUCCESS", "Updated score information.");
	}
	elseif( $action == "Update Dates" )
	{
		$gstart = strtotime($_REQUEST["game_start"]);
		$gend = strtotime($_REQUEST["game_end"]);
		$rstart = strtotime($_REQUEST["reg_start"]);
		$rend = strtotime($_REQUEST["reg_end"]);
		
		if( $gstart == FALSE || $gend == FALSE || $rstart == FALSE || $rend == FALSE )
		{
            set_alert("ERROR", "One of the entered dates is invalid.");
		}
		$gstart = sql_date($gstart);
		$gend = sql_date($gend);
		$rstart = sql_date($rstart);
		$rend = sql_date($rend);
		$result = $sql->query("UPDATE `hvz_game_info` SET `start_date` = '$gstart', `end_date` = '$gend', `reg_start`='$rstart', `reg_end`='$rend'");
		if( !$result ) set_alert("ERROR", "Internal error. Please contact a webmaster.");
        set_alert("SUCCESS", "Dates updated.");
	}
	elseif( $action == "Upload Rules" )
	{
		if( $_FILES["file"]["error"] > 0 )
		{
            set_alert("ERROR", "Failed to upload file.");
		}
		elseif( $_FILES["file"]["type"] != "application/pdf" )
		{
            set_alert("ERROR", "File must be a PDF.");
		}
		else
		{
			global $rules_file;
			$result = move_uploaded_file($_FILES["file"]["tmp_name"], $rules_file);
			if( $result == FALSE ) set_alert("ERROR", "Failed to copy file.");
            set_alert("SUCCESS", "Rules document uploaded successfully.");
		}
	}
	elseif( $action == "Upload Map" )
	{
		if( $_FILES["file"]["error"] > 0 )
		{
            set_alert("ERROR", "Failed to upload file.");
		}
		elseif( $_FILES["file"]["type"] != "image/png" )
		{
            set_alert("ERROR", "File must be a PNG.");
		}
		else
		{
			global $rules_file;
			$result = move_uploaded_file($_FILES["file"]["tmp_name"], $map_file);
			if( $result == FALSE ) set_alert("ERROR", "Failed to copy file.");
            set_alert("SUCCESS", "Map uploaded successfully.");
		}
	}
    elseif($action == "Delete Score Entry")
    {
        if(count(get_stun_scores()) == 1)
        {
            set_alert("ERROR", "Can't Delete Last Get Scoring Entry!");
        }
        else
        {
            $id = $_REQUEST["id"];
            remove_stun_scores($id);
        }
        recalculate_stuns();
        set_alert("SUCCESS", "Removed entry.");
    }
    elseif($action == "Add Points Per Stun Entry")
    {
        $scores = $_REQUEST["scores"];
        $date = $_REQUEST["date"];

        add_stun_scores($scores, $date);
        recalculate_stuns();

        set_alert("SUCCESS", "Added entry.");
    }
}

page_head();
$m = is_maintenance();
?>

<h2>Game Settings</h2>
<h3>Maintenance Mode</h3>
<p>During Maintenance Mode only Admins are permitted to log in. All other players will be told to wait for maintenance mode to end.</p>
    <form action="" method="post">
        <input class="btn btn-default" type="submit" name="action" value="<?php if($m) { echo "Disable Maintenance Mode"; } else { echo "Enable Maintenance Mode"; } ?>" />
    </form>
<h3>Date Settings</h3>
<p>These are mostly used for cosmetic purposes (included in e-mails, etc) but in some cases (such as stun reporting) they are used to validate user input.</p>
<div class="row">
    <div class="col-md-4">
        <form action="" method="post">
            <div class="form-group">
                <label>Registration Start</label>
                <input class="form-control date" name="reg_start" value="<?php echo date("m/d/Y H:i", php_reg_start());?>" />
            </div>
            <div class="form-group">
                <label>Registration End</label>
                <input class="form-control date" name="reg_end" value="<?php echo date("m/d/Y H:i", php_reg_end());?>" />
            </div>
            <div class="form-group">
                <label>Game Start</label>
                <input class="form-control date" name="game_start" value="<?php echo date("m/d/Y H:i", php_game_start());?>" />
            </div>
            <div class="form-group">
                <label>Game End</label>
                <input class="form-control date" name="game_end" value="<?php echo date("m/d/Y H:i", php_game_end());?>" />
            </div>
            <input class="btn btn-default" name="action" value="Update Dates" type="submit"/>
        </form>
    </div>
</div>
<h3>Score Settings</h3>
<form action="" method="post">
    <p>Write a good explanation about how points per stun works, but for now this.</p>
    <div class="row">
        <div class="col-md-6">
            <?php stun_value_editor(get_stun_scores()); ?>
        </div>
    </div>
    <p>
        Milestones are score brackets that can be used for giving out score-based rewards.
        The first milestone represents the minimum point value for being 'supplied' and not dying on the final day.
        The other values (comma-separated) can be used for whatever the mods wish.
    </p>
    <div class="row">
        <div class="form-group col-md-4">
            <label>Milestones</label>
            <input class="form-control" name="score_milestones" value="<?php echo implode(",", get_score_milestones());?>" />
        </div>
    </div>
    <p>Points per supply code is the number of points a player gets when they cash in a supply code. This can be changed throughout the week.</p>
    <div class="row">
        <div class="form-group col-md-4">
            <label>Points Per Supply Code</label>
            <input class="form-control" name="supply_score" value="<?php echo get_supply_score(); ?>" />
        </div>
    </div>
    <p>Points per tag is the number of points scored by a zombie when they tag a human</p>
    <div class="row">
        <div class="form-group col-md-4">
            <label>Points Per Tag</label>
            <input class="form-control" name="tag_score" value="<?php echo get_tag_score(); ?>" />
        </div>
    </div>
    <input class="btn btn-default" name="action" type="submit" value="Update Score Settings" />
</form>
<h3>Rules</h3>
<p>Use this to upload a new rules document (must be a PDF).</p>
<form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label>File</label>
        <input class="file" type="file" name="file" />
    </div>
    <input class="btn btn-default" type="submit" value="Upload Rules" name="action" />
</form>
<h3>Map</h3>
<p>Use this to upload a new map (must be a PNG).</p>
<form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label>File</label>
        <input type="file" name="file" />
    </div>
    <input class="btn btn-default" type="submit" value="Upload Map" name="action" />
</form>
<script type="text/javascript">
$("input.date").datetimepicker();
</script>

<?php
page_foot();
?>