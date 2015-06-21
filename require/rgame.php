<?php
require_once(dirname(__FILE__) . "/sql.php");

$game_info_result = $sql->query( "SELECT * FROM `hvz_game_info`" );
$game_info = $game_info_result->fetch_assoc();

function get_inventory()
{
    global $game_info;
    return $game_info['inventory'];
}
function set_inventory($text)
{
    global $sql;
    global $game_info;
    $text = $sql->real_escape_string($text);
    $sql->query("UPDATE hvz_game_info SET inventory='$text';");
    $game_info['inventory'] = $text;
}

function get_game_start()
{
	global $game_info;
	return $game_info["start_date"];
}
function get_game_end()
{
	global $game_info;
	return $game_info["end_date"];
}
function get_reg_start()
{
	global $game_info;
	return $game_info["reg_start"];
}
function get_reg_end()
{
	global $game_info;
	return $game_info["reg_end"];
}
function php_game_start()
{
	return strtotime(get_game_start());
}
function php_game_end()
{
	return strtotime(get_game_end());
}
function php_reg_start()
{
	return strtotime(get_reg_start());
}
function php_reg_end()
{
	return strtotime(get_reg_end());
}
function is_game_started()
{
	global $game_info;
	if( $game_info["game_started"] > 0 ) 
		return true;
	else
		return false;
}
function is_maintenance()
{
	global $game_info;
	if( $game_info["maintenance"] > 0 ) return true;
	else return false;
}

function set_maintenance($m)
{
	if( $m ) $val = 1;
	else $val = 0;
	global $sql;
	global $game_info;
	$sql->query("UPDATE `hvz_game_info` SET `maintenance`='$val'");
	$game_info["maintenance"] = $val;
}

function set_game_start($m)
{
	if( $m ) $val = 1;
	else $val = 0;
	global $sql;
	global $game_info;
	$sql->query("UPDATE `hvz_game_info` SET `game_started`='$val'");
	$game_info["game_started"] = $val;
}
$score_milestones = array_map("intval", explode(",", $game_info["score_milestones"]));
function get_score_milestones()
{
	global $score_milestones;
	return $score_milestones;
}
function get_num_milestones()
{
	global $score_milestones;
	return count($score_milestones);
}

$score_per_zed = array_map("intval", explode(",", $game_info["score_per_zed"]));
function get_score_per_zed()
{
	global $score_per_zed;
	return $score_per_zed;
}

function get_score_for_zed($count)
{
	global $score_per_zed;
	if( $count < count($score_per_zed)) return $score_per_zed[$count];
	return $score_per_zed[count($score_per_zed)-1];
}

function allow_public_scores()
{
	global $game_info;
	return $game_info["allow_public_scores"] == 1;
}

function get_supply_score()
{
	global $game_info;
	return intval( $game_info["score_per_supply"] );
}

function get_tag_score()
{
	global $game_info;
	return intval( $game_info["score_per_tag"] );
}

function get_mail_email()
{
	global $game_info;
	return $game_info["main_email"];
}