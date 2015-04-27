<?php

require_once(dirname(__FILE__) . "/sql.php");
require_once(dirname(__FILE__) . "/mail.php");
require_once(dirname(__FILE__) . "/mailing.php");
require_once(dirname(__FILE__) . "/rgame.php");

$NONE = "NONE";
$HUMAN = "HUMAN";
$ZOMBIE = "ZOMBIE";
$ADMIN = "ADMIN";
$SPECTATE = "SPECTATE";
$BANNED = "BANNED";

$PLAYER_TYPES = array( $NONE, $HUMAN, $ZOMBIE, $ADMIN, $BANNED, $SPECTATE );

function generateRandomString($length)
{
	return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

function h_get_email($player)
{
	return $player["email"];
}

function fix_emails()
{
	$zombies = get_players("ZOMBIE");
	$humans = get_players("HUMAN");
	$admins = get_players("ADMIN");
	$spectators = get_players("SPECTATE");
	$all = get_players_with_sub();

	$ze = array_map("h_get_email", $zombies);
	$he = array_map("h_get_email", $humans);
	$ae = array_map("h_get_email", $all);
	$ade = array_map("h_get_email", $admins);
	$se = array_map("h_get_email", $spectators);

	fix_subscriptions($ae, $ade, $he, $ze, $se);
}

function kill_humans_below_score($score)
{
	global $sql;
	$score = $sql->real_escape_string($score);
	$sql->query("UPDATE `hvz_players` SET `type`='ZOMBIE' WHERE `type`='HUMAN' AND `score`<$score");
	fix_emails();
}

function count_humans_below_score($score)
{
	global $sql;
	$score = $sql->real_escape_string($score);
	$result = $sql->query("SELECT COUNT(`id`) FROM `hvz_players` WHERE `type`='HUMAN' AND `score`<$score");
	$row = $result->fetch_row();
	return intval($row[0]);
}

function register_player($first, $last, $email, $type)
{
	global $sql;
	
	$first = $sql->real_escape_string($first);
	$last = $sql->real_escape_string($last);
	$email = strtolower($sql->real_escape_string($email));
	$type = $sql->real_escape_string($type);
	
	$password = generateRandomString(8);
	$code = generateRandomString(6);
	
	if(!$sql->query("INSERT INTO `hvz_players` (`first_name`, `last_name`, `email`, `password_crypt`, `reg_date`, `code`, `type`) VALUES ('$first', '$last', '$email', MD5('$password'), NOW(), '$code', '$type');")) return FALSE;
	
	setup_subscription_by_type($email, $type);
	
	return array( "id" => $sql->insert_id, "password" => $password );
}

function change_password( $id, $new )
{
	global $sql;
	
	$id = $sql->real_escape_string($id);
	$new = $sql->real_escape_string($new);
	$sql->query("UPDATE `hvz_players` SET `password_crypt`=MD5('$new') WHERE `id`='$id'");
}

function h_pline($line)
{
	// get each comma separated entry
	$entry = explode(",", $line);
	
	// pop the last one, which is whether they are an OZ
	$oz = array_pop($entry);
	
	// generate a random code
	$code = generateRandomString(6);
	
	// Create a comma separated string of values followed by the NOW function for the registration date and the random code
	$str = "'" . implode("','", $entry) . "', NOW(), '$code', ";
	
	// if they are an OZ, use password 1234567, else use 123456
	if( $oz == "false" ) $str .= "MD5('123456')";
	else $str .= "MD5('1234567')";
	return $str;
}

function process_csv($data)
{
	// get each line
	$lines = explode("\n", $data);
	
	// Process entries into SQL insert entries
	$entries = array_map("h_pline", $lines);
	
	// make a string combining them all
	$str = "(" . implode("), (", $entries) . ")";
	
	global $sql;
	
	// Insert all players into the database
	$sql->query("INSERT INTO `hvz_players` (`first_name`, `last_name`, `email`, `type`, `reg_date`, `code`, `password_crypt`) VALUES " . $str . ";"); 
	
	// Add all players with password 1234567 to the oz pool
	$sql->query( "INSERT INTO `hvz_oz_pool` (`id`) SELECT `hvz_players`.`id` FROM `hvz_players` WHERE `hvz_players`.`password_crypt`=MD5('1234567');" );
	
	// Change all 1234567 passwords to 123456
	$sql->query( "UPDATE `hvz_players` SET `password_crypt`=MD5('123456') WHERE `password_crypt`=MD5('1234567')" );
	
	// Register everyone for their mailing lists
	fix_emails();
}

function add_to_pool($id)
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$result = $sql->query("INSERT INTO `hvz_oz_pool` (`id`) VALUES ('$id');");
	if( !$result ) return FALSE;
	return TRUE;
}
function remove_from_pool($id)
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$sql->query("DELETE FROM `hvz_oz_pool` WHERE `id`='$id';");
}
function add_to_list($id)
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$sql->query("INSERT INTO `hvz_oz_list` (`id`) VALUES ('$id');");
}
function remove_from_list($id)
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$sql->query("DELETE FROM `hvz_oz_list` WHERE `id`='$id';");
}

function remove_player($id)
{
	global $sql;
	
	$id = $sql->real_escape_string($id);
	$player = get_player($id);
	remove_subscription($player["email"]);
	
	return $sql->query("DELETE FROM `hvz_players` WHERE `id`='$id'");
}

function update_player( $id, $first, $last, $email, $type, $code, $subs = true )
{
	global $sql;
	
	$id = $sql->real_escape_string($id);
	$first = $sql->real_escape_string($first);
	$last = $sql->real_escape_string($last);
	$email = strtolower($sql->real_escape_string($email));
	$type = $sql->real_escape_string($type);
	$code = $sql->real_escape_string($code);
	
	$player = get_player($id);
	$result = $sql->query("UPDATE `hvz_players` SET `first_name`='$first', `last_name`='$last', `code`='$code', `email`='$email', `type`='$type' WHERE `id`='$id'");
	
	if( $subs )
	{
		if( $player["email"] != $email )
		{
			setup_subscription($player["email"], false, false, false);
		}
		if( $player["type"] != $type || $player["email"] != $email )
		{
			$oldsub = TRUE;
			if( intval($player["subscribe"]) == 0 ) $oldsub = FALSE;
			setup_subscription_by_type($email, $type, $oldsub);
		}
	}
	if( $player["type"] == "ZOMBIE" && ($type != "ZOMBIE" && $type != "BANNED") )
	{
		$sql->query("DELETE FROM `hvz_tags` WHERE `victim`='$id'");
	}
	return $result;
}

function update_type( $id, $type )
{
	global $sql;
	
	$id = $sql->real_escape_string($id);
	$type = $sql->real_escape_string($type);
	
	$player = get_player($id);
	$result = $sql->query("UPDATE `hvz_players` SET `type`='$type' WHERE `id`='$id'");
	$oldsub = TRUE;
	if( $player["subscribe"] == 0 ) $oldsub = FALSE;
	setup_subscription_by_type($player["email"], $type, $oldsub);
	if( $player["type"] == "ZOMBIE" && ($type != "ZOMBIE" && $type != "BANNED") )
	{
		$sql->query("DELETE FROM `hvz_tags` WHERE `victim`='$id'");
	}
	return $result;
}

function update_player_sub($id, $sub)
{
	global $sql;
	$id = $sql->real_escape_string($id);
	if( $sub == TRUE ) $sub = 1;
	else $sub = 0;

	$sql->query("UPDATE `hvz_players` SET `subscribe`='$sub' WHERE `id`='$id'");
}

function get_players($type = NULL)
{
	global $sql;
	$players = array();
	
	$query = "SELECT * FROM `hvz_players`";
	if( $type != NULL )
	{
		$type = $sql->real_escape_string($type);
		$query .= " WHERE `type`='$type'";
	}
	$result = $sql->query($query);
	while( $row = $result->fetch_assoc() )
	{
		$players[$row["id"]] = $row;
	}
	
	return $players;
}
function get_players_with_sub()
{
	global $sql;
	$players = array();
	
	$query = "SELECT * FROM `hvz_players` WHERE `subscribe`=1";
	$result = $sql->query($query);
	while( $row = $result->fetch_assoc() )
	{
		$players[$row["id"]] = $row;
	}
	
	return $players;
}
function get_players_without_waiver()
{
	global $sql;
	$players = array();
	
	$query = "SELECT * FROM `hvz_players` WHERE `waiver`=0";
	$result = $sql->query($query);
	while( $row = $result->fetch_assoc() )
	{
		$players[$row["id"]] = $row;
	}
	
	return $players;
}
function get_players_for_milestones( $milestone )
{
	global $sql;
	$milestones = get_score_milestones();
	if( $milestone >= count($milestones) ) return array();
	
	$query = "SELECT * FROM `hvz_players` WHERE (`type` = 'HUMAN') AND `score` >= {$milestones[$milestone]}";
	if( $milestone+1 < count($milestones) )
	{
		$milestone = $milestone + 1;
		$query .= " AND `score` < {$milestones[$milestone]}";
	}
	$result = $sql->query($query);
	$players = array();
	while( $row = $result->fetch_assoc() )
	{
		array_push($players, $row);
	}
	return $players;
}

function reset_password_by_id( $id )
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$password = generateRandomString(8);
	
	$sql->query("UPDATE `hvz_players` SET `password_crypt`=MD5('$password') WHERE `id`='$id';");
	if( $sql->affected_rows <= 0 ) return FALSE;
	return $password;
}

function reset_password( $email )
{
	global $sql;
	$email = strtolower($sql->real_escape_string($email));
	$password = generateRandomString(8);
	
	$result = $sql->query("UPDATE `hvz_players` SET `password_crypt`=MD5('$password') WHERE `email`='$email';");
	if( $sql->affected_rows <= 0 ) return FALSE;
	return $password;
}

function get_players_limited()
{
	global $sql;
	$players = array();
	
	$result = $sql->query("SELECT `id`, `first_name`, `last_name`, `email`, `score`, `show_score` FROM `hvz_players`");
	while( $row = $result->fetch_assoc() )
	{
		if( $row['show_score'] == 0 ) { $row['score'] = "HIDDEN"; }
		$players[$row["id"]] = $row;
	}
	
	return $players;
}

function get_players_limited_plus_type()
{
	global $sql;
	$players = array();
	
	$result = $sql->query("SELECT `id`, `first_name`, `last_name`, `email`, `type` FROM `hvz_players`");
	while( $row = $result->fetch_assoc() )
	{
		$players[$row["id"]] = $row;
	}
	
	return $players;
}

function get_player($id)
{
	global $sql;
	$id = $sql->real_escape_string($id);
	
	$result = $sql->query("SELECT * FROM `hvz_players` WHERE `id`='$id'");
	if( $result->num_rows < 1 ) return NULL;
	else return $result->fetch_assoc();
}

function get_player_by_code($code)
{
	global $sql;
	$code = $sql->real_escape_string($code);
	
	$result = $sql->query("SELECT * FROM `hvz_players` WHERE `code`='$code'");
	if( $result->num_rows < 1 ) return NULL;
	else return $result->fetch_assoc();
}

function get_player_by_email($email)
{
	global $sql;
	$email = $sql->real_escape_string($email);
	
	$result = $sql->query("SELECT * FROM `hvz_players` WHERE `email`='$email'");
	if( $result->num_rows < 1 ) return NULL;
	else return $result->fetch_assoc();
}

function tag_player($id, $idother)
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$idother = $sql->real_escape_string($idother);
	
	// Can't tag self
	if( $id == $idother ) return FALSE;
	
	$player = get_player($idother);
	if( $player == NULL ) return FALSE;
	if( $player["type"] == "ZOMBIE" ) // claim existing
	{
		$findtagresult = $sql->query("SELECT * FROM `hvz_tags` WHERE `victim`='$idother'");
		if( $findtagresult->num_rows > 0 ) return FALSE; // they have already been claimed
	}
	elseif( $player["type"] != "HUMAN" ) return FALSE;
	
	$result = $sql->query("INSERT INTO `hvz_tags` (`killer`, `victim`, `time`) VALUES ('$id', '$idother', NOW())");
	update_player( $idother, $player["first_name"], $player["last_name"], $player["email"], "ZOMBIE", $player["code"] );
	
	// score
	$points = get_tag_score();
	$sql->query( "UPDATE `hvz_players` SET `score`=`score`+$points WHERE `id`='$id'");
	return $result;
}

function get_tags($id)
{
	global $sql;
	
	$id = $sql->real_escape_string($id);
	$result = $sql->query("SELECT P.`id`, P.`first_name`, P.`last_name`, T.`time` FROM `hvz_tags` AS T INNER JOIN `hvz_players` AS P ON P.`id` = T.`victim` WHERE T.`killer` = '$id'");
	$players = array();
	while( $row = $result->fetch_assoc() )
	{
		$players[$row["id"]] = $row;
	}
	
	return $players;	
}

function get_tagged_by($id)
{
	global $sql;
	
	$id = $sql->real_escape_string($id);
	$result = $sql->query("SELECT P.`id`, P.`first_name`, P.`last_name`, T.`time` FROM `hvz_players` AS P INNER JOIN `hvz_tags` AS T ON P.`id` = T.`killer` WHERE P.`id` = '$id'");
	if( $result->num_rows < 1 ) return FALSE;
	else return $result->fetch_assoc();
}

function find_login($email, $password, $crypt=FALSE)
{
	global $sql;
	
	$email = strtolower($sql->real_escape_string($email));
	$password = $sql->real_escape_string($password);
	
	$query = "SELECT * FROM `hvz_players` WHERE `email`='$email' AND `password_crypt`=";
	if( $crypt ) { $query .= "'$password'"; }
	else { $query .= "MD5('$password')"; }
	
	$result = $sql->query($query);
	
	if($result->num_rows > 0)
	{
		return $result->fetch_assoc();
	}
	else
	{
		return FALSE;
	}
}

function get_oz_pool()
{
	global $sql;
	
	$query = "SELECT * FROM `hvz_oz_pool`";
	$result = $sql->query($query);
	$ozs = array();
	while( $row = $result->fetch_assoc() )
	{
		array_push($ozs, intval($row["id"]));
	}
	return $ozs;
}

function get_oz_list()
{
	global $sql;
	
	$query = "SELECT * FROM `hvz_oz_list`";
	$result = $sql->query($query);
	$ozs = array();
	while( $row = $result->fetch_assoc() )
	{
		array_push($ozs, intval($row["id"]));
	}
	return $ozs;
}

function set_oz_list($ids)
{
	global $sql;
	$sql->query("DELETE FROM `hvz_oz_list` WHERE 1=1");
	$query = "INSERT INTO `hvz_oz_list` (`id`) VALUES ";
	foreach($ids as $id)
	{
		$id = $sql->real_escape_string($id);
		if($id == $ids[0]) $query .= "($id)";
		else $query .= ", ($id)";
	}
	$sql->query($query);
}

function get_ids_sorted_by( $fields, $asc = TRUE, $typesort = false, $filter_types = NULL, $waiver = NULL )
{
	global $sql;
	$query = "SELECT `id`";
	if( $typesort == TRUE )
	{
		$query .= ", `type`";
	}
	$sep = "`,`";
	if( $asc == FALSE ) $sep = "` DESC, `";
	$query .= " FROM `hvz_players` ";
	if( $filter_types !== NULL )
	{
		$query .= "WHERE `type`='" . implode("' OR `type`='", $filter_types) . "' ";
	}
	if( $waiver !== NULL )
	{
		if( $filter_types === NULL )
		{
			$query .= "WHERE";
		}
		else
		{
			$query .= "AND";
		}
		if( $waiver === TRUE )
		{
			$query .= "`waiver`=1 ";
		}
		else
		{
			$query .= "`waiver`=0 ";
		}
	}
	if( $fields !== NULL )
	{
		$query .= "ORDER BY `" . implode($sep, $fields) . "`";
		if( $asc == FALSE )
		{
			$query .= " DESC";
		}
	}
	$result = $sql->query($query);
	$ids = array();	
	while( $row = $result->fetch_row() )
	{
		if( $typesort )
		{
			if( !isset($ids[$row[1]]) )
			{
				$ids[$row[1]] = array();
			}
			array_push($ids[$row[1]], $row[0]);
		}
		else
		{
			array_push($ids, $row[0]);
		}
	}
	
	// initialize unused type arrays so they're not NULL, only empty
	if( $typesort )
	{
		global $PLAYER_TYPES;
		foreach( $PLAYER_TYPES as $t )
		{
			if( $ids[$t] === NULL )
			{
				$ids[$t] = array();
			}
		}
	}
	return $ids;
}

function get_ids( $players )
{
	$ids = array();
	foreach( $players as $key => $value )
	{
		array_push($ids, $key);
	}
	return $ids;
}

function get_ids_types( $players, $types )
{
	$ids = array();
	foreach( $players as $key => $value )
	{
		if( in_array( $value["type"], $types ) )
		{
			array_push($ids, $key);
		}
	}
	return $ids;
}

function hide_player_properties( &$players, $wanted )
{
	foreach( $players as &$player )
	{
		foreach( $player as $key => &$value )
		{
			if( !in_array($key, $wanted) )
			{
				unset( $player[$key] );
			}
		}
	}
}

function split_ids_types( $players )
{
	$ids = array( "HUMAN" => array(), "ZOMBIE" => array(), "BANNED" => array(), "NONE" => array(), "ADMIN" => array());
	foreach( $players as $key => $value )
	{
		array_push( $ids[$value["type"]], $key );
	}
	return $ids;
}

function get_team_score( $type = NULL, $avg = false )
{
	global $sql;
	$query = "SELECT ";
	if( $avg ) { $query .= "AVG"; }
	else { $query .= "SUM"; }
	$query .= "(`score`) FROM `hvz_players`";
	if( $type === NULL )
	{
		$query .= " WHERE `type` = 'HUMAN' OR `type` = 'ZOMBIE'";
	}
	else
	{
		$type = $sql->real_escape_string($type);
		$query .= " WHERE `type`='$type'";
	}
	$result = $sql->query($query);
	$row = $result->fetch_row();
	return intval($row[0]);
}

// table configurations
$namePlayerProperties = json_decode( "{
						\"first_name\": 		{ \"display\":\"First Name\", \"type\":\"text\"}, 
						\"last_name\": 		{ \"display\":\"Last Name\", \"type\":\"text\"}
					}", true);
$basicPlayerProperties = json_decode( "{ 
						\"first_name\": 		{ \"display\":\"First Name\", \"type\":\"text\"}, 
						\"last_name\": 		{ \"display\":\"Last Name\", \"type\":\"text\"}, 
						\"email\":			{ \"display\":\"E-Mail\", \"type\":\"text\"},
						\"score\":			{ \"display\":\"Score\", \"type\":\"text\"}
					}", true);
$basicTeamPlayerProperties = json_decode( "{ 
						\"first_name\": 		{ \"display\":\"First Name\", \"type\":\"text\"}, 
						\"last_name\": 		{ \"display\":\"Last Name\", \"type\":\"text\"}, 
						\"email\":			{ \"display\":\"E-Mail\", \"type\":\"text\"},
						\"type\":				{ \"display\":\"Type\", \"type\":\"text\"}
					}", true);
$fullPlayerProperties = json_decode( "{
						\"id\": 				{ \"display\":\"ID\", \"type\":\"text\"}, 
						\"first_name\": 		{ \"display\":\"First Name\", \"type\":\"text\"}, 
						\"last_name\": 		{ \"display\":\"Last Name\", \"type\":\"text\"}, 
						\"email\":			{ \"display\":\"E-Mail\", \"type\":\"text\"},
						\"code\":				{ \"display\":\"Code\", \"type\":\"text\"},
						\"type\":				{ \"display\":\"Type\", \"type\":\"text\"},
						\"score\":			{ \"display\":\"Score\", \"type\":\"text\"}
					}", true);

function gen_player_table( $players, $properties, $order=NULL, $boxes=false )
{
	//echo "<table class='players'>";
    echo "<table class='table table-striped table-bordered table-condensed'>";
	echo "<tr>";
	echo "<th style='display: none'>";
	echo "</th>";
	foreach( $properties as $key=>$prop )
	{
		echo "<th>${prop['display']}</th>";
	}
	echo "</tr>";
	if( $order === NULL )
	{
		foreach( $players as $key=>$player )
		{
			echo "<tr class='player'>";
			echo "<td style='display: none'>";
			if( $boxes )
			{
				echo "<input type='checkbox' name='id' value='$key' />";
			}
			else
			{
				echo "<input type='hidden' name='id' value='$key' />";
			}
			echo "</td>";
			foreach( $properties as $key2=>$prop )
			{
				echo "<td class='$key2'>{$player[$key2]}</td>";
			}
			echo "</tr>";
		}
	}
	else
	{
		foreach( $order as $key )
		{
			$player = $players[$key];
			echo "<tr class='player'>";
			echo "<td style='display: none'>";
			if( $boxes )
			{
				echo "<input type='checkbox' name='id' value='$key' />";
			}
			else
			{
				echo "<input type='hidden' name='id' value='$key' />";
			}
			echo "</td>";
			foreach( $properties as $key2=>$prop )
			{
				echo "<td class='$key2'>{$player[$key2]}</td>";
			}
			echo "</tr>";
		}
	}
	echo "</table>";
}
