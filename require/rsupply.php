<?php

require_once(dirname(__FILE__) . "/sql.php");
require_once(dirname(__FILE__) . "/rplayers.php");
require_once(dirname(__FILE__) . "/rgame.php");

function generate_supply( $num )
{
	global $sql;
	$num = intval($num);
	$codes = array();
	if( $num == 0 ) return $codes;
	for( $i = 0; $i < $num; $i++ )
	{
		array_push($codes, generateRandomString( 4 ));
	}
	$result = $sql->query("INSERT INTO `hvz_supply_codes` (`code`) VALUES ('" . implode("'), ('", $codes) . "')");
	if( !$result ) return false;
	else return $codes;
}

function use_supply( $id, $code )
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$code = $sql->real_escape_string($code);
	
	$sql->query("UPDATE `hvz_supply_codes` SET `player`='$id' WHERE `code`='$code' AND `player` IS NULL");
	if( $sql->affected_rows <= 0 ) return "Not a valid supply code";
	$points = get_supply_score();
	$sql->query( "UPDATE `hvz_players` SET `score`=`score`+$points WHERE `id`='$id'");
	return true;
}

function get_supply( )
{
	global $sql;
	$result = $sql->query("SELECT C.`code`, P.`first_name`, P.`last_name` FROM `hvz_supply_codes` AS C LEFT JOIN `hvz_players` AS P ON `P`.`id` = `C`.`player`");
	$codes = array();
	while( $row = $result->fetch_assoc() )
	{
		array_push($codes, $row);
	}
	return $codes;
}

function get_num_my_supply($id)
{
    global $sql;
    $id = $sql->real_escape_string($id);
    $result = $sql->query("SELECT COUNT(code) FROM hvz_supply_codes WHERE player = '$id'");
    $row = $result->fetch_row();

    return intval($row[0]);
}