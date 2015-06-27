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
            set_alert("SUCCESS", "Player id $id deleted.");
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
			if( !is_array($addresult) ) set_alert("ERROR", "E-mail already exists.");
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
            set_alert("SUCCESS", "Player $first $last has been added.");
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
            set_alert("SUCCESS", "Player $first $last has been modified.");
		}
		elseif($_REQUEST["action"] == "Reset Password")
		{
			$id = $_REQUEST["id"];
			$result = reset_password_by_id( $id );
			if( $result === FALSE ) set_alert("ERROR", "Failed to reset password.");
			$player_reset = $players[$id];
			hvzmailf($player_reset["email"], "reset", array("password" => $result));
            set_alert("SUCCESS", "Password for {$player_reset['first_name']} {$player_reset['last_name']} has been reset.");
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
            set_alert("SUCCESS", "{$player_ban['first_name']} {$player_ban['last_name']} has been banned.");
		}
		elseif($_REQUEST["action"] == "Add To OZ Pool")
		{
			$id = $_REQUEST["id"];
			$pool_player = $players[$id];
			if(add_to_pool($id)) set_alert("SUCCESS", "{$pool_player['first_name']} {$pool_player['last_name']} added to OZ Pool.");
			else set_alert("ERROR", "{$pool_player['first_name']} {$pool_player['last_name']} is already in OZ Pool!");
		}
		elseif($_REQUEST["action"] == "Cancel")
		{
			reload_self();
		}
	}
}

page_head();

if( IsAdmin() && $show_add ) {
?>
	<h2>Add Player</h2><div><?php
	player_form( );
	?></div><?php
}
else if( IsAdmin() )
{
	?><h2>Edit Player</h2><div><?php
		player_form( $players[$_REQUEST["id"]] );
	?></div><?php
}
?>
<h2>Players</h2>
<div id="table">
</div>
<?php
$sort_fields = array("first_name", "last_name");
$sort_asc = TRUE;
if( IsAdmin() )
{
	/*$ids = get_ids( $players );*/
	$ids = get_ids_sorted_by( $sort_fields, $sort_asc );
}
elseif( IsZombie() )
{
	/*$split_ids = split_ids_types( $players );*/
	$split_ids = get_ids_sorted_by($sort_fields, $sort_asc, TRUE);
	hide_player_properties( $players, array("id", "first_name", "last_name", "type", "email", "score", "show_score") );
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
?>
<script type="text/javascript">
LoadPlayerData(<?php echo json_encode( $players ); ?>);
<?php if( IsAdmin() ) { ?>
	var actions = { "Edit" : { "type" : "post" }, "Delete" : { "type" : "post" }, "Ban" : { "type" : "post" }, "Reset Password" : { "type" : "post" }, "Impersonate" : { "type" : "post" }, "Add To OZ Pool" : {"type":"post"} };
	var t = new PlayerTable( $("#table"), fullPlayerProperties, { }, actions );
	t.addPlayers( <?php echo json_encode($ids); ?> );
<?php } else if( IsZombie() ) { ?>
	$("<h3>").html("Humans").appendTo($("#table"));
	var humanTable = new PlayerTable( $("#table"), basicTeamPlayerProperties, { }, { } );
	humanTable.addPlayers( <?php echo json_encode($split_ids["HUMAN"]); ?> );
	$("<h3>").html("Zombies").appendTo($("#table"));
	var zombieTable = new PlayerTable( $("#table"), basicTeamPlayerProperties, { }, { } );
	zombieTable.addPlayers( <?php echo json_encode($split_ids["ZOMBIE"]); ?> );
<?php } else { ?>
	var t = new PlayerTable( $("#table"), basicPlayerProperties, { }, { } );
	t.addPlayers( <?php echo json_encode($players_only); ?> );
<?php } ?>
</script>
<?php page_foot() ?>
