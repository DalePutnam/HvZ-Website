<?php session_start();
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rgame.php");
require_once("require/rplayers.php");
require_once("require/forms.php");
Secure(false);
$show_add = true;
$edit_player = -1;
$players = get_players();
if( IsAdmin() ) {
	if( isset($_REQUEST["action"]) )
	{
		if($_REQUEST["action"] == "Delete")
		{
			$id = $_REQUEST["id"];
			remove_player($id);
			reload_self("Player id $id deleted.");
		}
		elseif($_REQUEST["action"] == "Edit")
		{
			$show_add = false;
			$edit_player = $_REQUEST["id"];
		}
		elseif($_REQUEST["action"] == "Add")
		{
			$first = trim($_REQUEST["first_name"]);
			$last = trim($_REQUEST["last_name"]);
			$email = trim($_REQUEST["email"]);
			$type = trim($_REQUEST["type"]);
			
			$addresult = register_player($first, $last, $email, $type);
			if( !is_array($addresult) ) reload_self("&E-mail already exists.");
			$id = $addresult["id"];
			$password = $addresult["password"];
			if( isset( $_REQUEST["oz"] ) )
			{
				add_to_pool($id);
			}
			if( !is_maintenance() )
			{
				hvzmailf($email, "register", array("password" => $password, "first_name" => $first, "last_name" => $last));
			}
			reload_self("Player $first $last has been added.");
		}
		elseif($_REQUEST["action"] == "Commit")
		{
			$id = $_REQUEST["id"];
			$first = trim($_REQUEST["first_name"]);
			$last = trim($_REQUEST["last_name"]);
			$email = trim($_REQUEST["email"]);
			$type = trim($_REQUEST["type"]);
			$code = trim($_REQUEST["code"]);
			
			update_player($id, $first, $last, $email, $type, $code);
			reload_self("Player $first $last has been modified.");
		}
		elseif($_REQUEST["action"] == "Reset Password")
		{
			$id = $_REQUEST["id"];
			$result = reset_password_by_id( $id );
			if( $result === FALSE ) reload_self("&Failed to reset password.");
			$player_reset = $players[$id];
			hvzmailf($player_reset["email"], "reset", array("password" => $result));
			reload_self("Password for {$player_reset['first_name']} {$player_reset['last_name']} has been reset.");
		}
		elseif($_REQUEST["action"] == "Impersonate")
		{
			Impersonate( $_REQUEST["id"] );
		}
		elseif($_REQUEST["action"] == "Ban")
		{
			$id = $_REQUEST["id"];
			update_type($id, "BANNED");
			$player_ban = $players[$id];
			hvzmailf($player_ban["email"], "ban", array());
			reload_self("{$player_ban['first_name']} {$player_ban['last_name']} has been banned.");
		}
		elseif($_REQUEST["action"] == "Add To OZ Pool")
		{
			$id = $_REQUEST["id"];
			$pool_player = $players[$id];
			if(add_to_pool($id)) reload_self("{$pool_player['first_name']} {$pool_player['last_name']} added to OZ Pool.");
			else reload_self("&{$pool_player['first_name']} {$pool_player['last_name']} is already in OZ Pool!");
		}
		elseif($_REQUEST["action"] == "Cancel")
		{
			reload_self();
		}
	}
}

page_head(); ?>
<div class="row">
<div class="col-md-9">
<?php
if ( IsAdmin() ) {
?>
    <div class="row">
        <div class="col-md-12">
            <h2>Players</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <form class="form-inline" method="get" action="">
                <div class="form-group">
                    <label>Sorting Options:</label>
                    <select class="form-control" name="sf">
                        <option value="first_name,last_name">Name</option>
                        <option value="score">Score</option>
                        <option value="type,score">Team and Score</option>
                    </select>
                </div>
                <div class="form-group text-center checkbox">
                    <label><input type="checkbox" name="sa" value="false" /> Sort Descending</label>
                </div>
                <div class="form-group">
                    <input class="btn btn-default" type="submit" value="Sort"</input>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <h5>Right click users to edit/delete/ban/etc</h5>
        </div>
    </div>
<?php
}
$sort_fields = array("first_name", "last_name");
$sort_asc = TRUE;
if( IsAdmin() )
{
    if( isset( $_GET["sf"] ) )
    {
        $sort_fields = explode(",", trim($_GET["sf"]));
    }
    if( isset( $_GET["sa"] ) )
    {
        if( $_GET["sa"] == "false" )
        {
            $sort_asc = FALSE;
        }
    }
    /*$ids = get_ids( $players );*/
    $ids = get_ids_sorted_by( $sort_fields, $sort_asc );
}
elseif( IsZombie() || IsSpectator() )
{
    /*$split_ids = split_ids_types( $players );*/
    $split_ids = get_ids_sorted_by($sort_fields, $sort_asc, TRUE);
    hide_player_properties( $players, array("id", "first_name", "last_name", "type", "email", "score", "show_score") );

    foreach($players as &$player)
    {
        if( $player["show_score"] == 0 )
        {
            $player["score"] = "HIDDEN";
        }
    }
}
elseif( !IsAdmin() )
{
    /*$players_only = get_ids_types( $players, array("HUMAN", "ZOMBIE", "NONE") );*/
    $players_only = get_ids_sorted_by($sort_fields, $sort_asc, FALSE, array("HUMAN", "ZOMBIE", "NONE"));
    hide_player_properties( $players, array("id", "first_name", "last_name", "email", "score", "show_score") );

    foreach($players as &$player)
    {
        if( $player["show_score"] == 0 )
        {
            $player["score"] = "HIDDEN";
        }
    }
}

    if( IsAdmin() ) {
		echo "<div id='table'>";
		gen_player_table( $players, $fullPlayerProperties, $ids, false );
		echo "</div>";
	}
	else if( IsZombie() || IsSpectator() )
	{
		echo "<h2>Humans</h2>";
		echo "<div id='table'>";
		gen_player_table( $players, $basicPlayerProperties, $split_ids["HUMAN"], false );
		echo "</div>";
		
		echo "<h2>Zombies</h2>";
		echo "<div id='table2'>";
		gen_player_table( $players, $basicPlayerProperties, $split_ids["ZOMBIE"], false );
		echo "</div>";
	}
	else
	{
		echo "<h2>Players</h2>";
		echo "<div id='table'>";
		gen_player_table( $players, $basicPlayerProperties, $players_only, false );
		echo "</div>";
	}
?>
</div>

<div class="col-md-3">
<?php if( IsAdmin() && $show_add ) {
?>
    <h2>Add Player</h2>
    <div class="row"><div class="col-md-12"><?php player_form( ); ?></div></div>
    <?php
    }
    else if( IsAdmin() )
    { ?>
        <h2>Edit Player</h2>
        <div class="row"><div class="col-md-12"><?php player_form( $players[$_REQUEST["id"]] ); ?></div></div>
    <?php
    }

    ?>
    <?php
    if( IsAdmin() ) {
        //echo "<h2>Players</h2>";
        echo "<h2>Stats</h2>";
        echo "<strong>All Players Score:</strong> " . get_team_score() . "<br/>";
        echo "<strong>Average Players Score:</strong> " . get_team_score(NULL, true) . "<br/><br/>";

        echo "<strong>Total Human Score:</strong> " . get_team_score("HUMAN") . "<br/>";
        echo "<strong>Average Human Score:</strong> " . get_team_score("HUMAN", true) . "<br/><br/>";

        echo "<strong>Total Zombie Score:</strong> " . get_team_score("ZOMBIE") . "<br/>";
        echo "<strong>Average Zombie Score:</strong> " . get_team_score("ZOMBIE", true) . "<br/>";
    } ?>
</div>
</div>

<script type="text/javascript">
<?php if( IsAdmin() ) { ?>
	LoadPlayerData2(<?php echo json_encode( $players ); ?>);
    function ViewScore(id)
    {
        document.location.href = "breakdown.php?id=" + id;
    }

	var actions = { "Edit" : { "type" : "post" },
                    "Delete" : { "type" : "post", "confirm":true },
                    "Ban" : { "type" : "post", "confirm":true },
                    "Reset Password" : { "type" : "post", "confirm":true },
                    "Impersonate" : { "type" : "post" },
                    "Add To OZ Pool" : {"type":"post"},
                    "View Score Breakdown" : {"type":"js", "func":ViewScore} };

	var playerTable = new PlayerTable2(  <?php echo json_encode($fullPlayerProperties); ?>, { }, actions );
	playerTable.take( $("#table"), false );
<?php } else if( IsZombie() || IsSpectator() ) { ?>
	var humanTable = new PlayerTable2(  <?php echo json_encode($basicPlayerProperties); ?>, { }, { } );
	humanTable.take( $("#table"), false );
	
	var zombieTable = new PlayerTable2(  <?php echo json_encode($basicPlayerProperties); ?>, { }, { } );
	zombieTable.take( $("#table2"), false );
<?php } else { ?>
	var playerTable = new PlayerTable2(  <?php echo json_encode($basicPlayerProperties); ?>, { }, { } );
	playerTable.take( $("#table"), false );
<?php } ?>
</script>
<?php page_foot() ?>
