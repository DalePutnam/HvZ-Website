<?php
/**
 * Created by PhpStorm.
 * User: brwarner
 * Date: 16/02/14
 * Time: 10:02 PM
 */

require_once(dirname(__FILE__) . "/sql.php");
require_once(dirname(__FILE__) . "/rplayers.php");
require_once(dirname(__FILE__) . "/mail.php");

function archive_game($name)
{
    global $sql;

    // Insert the archive
    $name = $sql->real_escape_string($name);
    $sql->query("INSERT INTO hvz_archive (name, created) VALUES ('$name', NOW())");
    $archive_id = $sql->insert_id;

    // Copy data from main tables to archive tables
    $sql->query("INSERT INTO hvz_game_archive (start_date, end_date, reg_start, reg_end, score_milestones, score_per_zed, allow_public_scores, score_per_supply, main_email, score_per_tag, term)
        SELECT start_date, end_date, reg_start, reg_end, score_milestones, score_per_zed, allow_public_scores, score_per_supply, main_email, score_per_tag, '$archive_id' FROM hvz_game_info;");
    $sql->query("INSERT INTO hvz_player_archive (id, first_name, last_name, type, score, term, reg_date) SELECT id, first_name, last_name, type, score, '$archive_id', reg_date FROM hvz_players");
    $sql->query("INSERT INTO hvz_stun_archive (killer, victim, helper, time, term) SELECT killer, victim, helper, time, '$archive_id' FROM hvz_stuns");
    $sql->query("INSERT INTO hvz_tag_archive (killer, victim, time, term) SELECT killer, victim, time, '$archive_id' FROM hvz_tags");
}

function reset_game($first, $last, $admin_email)
{
    global $sql;

    $first = $sql->real_escape_string($first);
    $last = $sql->real_escape_string($last);
    $admin_email = $sql->real_escape_string($admin_email);

    // Drop game data
    $sql->query("DELETE FROM hvz_oz_list");
    $sql->query("DELETE FROM hvz_oz_pool");
    $sql->query("DELETE FROM hvz_mail");
    $sql->query("DELETE FROM hvz_supply_codes");
    $sql->query("DELETE FROM hvz_tags");
    $sql->query("DELETE FROM hvz_stuns");
    $sql->query("DELETE FROM hvz_players");
    $sql->query("DELETE FROM hvz_zombie_stun_scores");

    // Reset game information
    $sql->query("UPDATE hvz_game_info SET game_started = 0, start_date=NOW(), end_date=NOW(), reg_start=NOW(), reg_end=NOW(), maintenance=1");

    // Register a new admin
    $registration = register_player($first, $last, $admin_email, 'ADMIN');

    // Email them their password
    $password = $registration["password"];
    hvzmailf($admin_email, "register", array("password" => $password, "first_name" => $first, "last_name" => $last));
}

function delete_archive($id)
{
    global $sql;
    $id = $sql->real_escape_string($id);

    $sql->query("DELETE FROM hvz_archive WHERE id='$id'");
}

function get_archives()
{
    global $sql;

    $array = array();
    $result = $sql->query("SELECT * FROM hvz_archive ORDER BY created DESC");
    while($row = $result->fetch_assoc())
    {
        array_push($array, $row);
    }
    return $array;
}

function is_archive($id)
{
    $id = sql()->real_escape_string($id);
    $result = sql()->query("SELECT COUNT(id) FROM hvz_archive WHERE id=$id");
    $row = $result->fetch_row();
    if($row === NULL || $row[0] == 0) return false;
    return true;
}

function get_players_and_ids_archive($term)
{
    $term = sql()->real_escape_string($term);
    $result = sql()->query("SELECT * FROM hvz_player_archive WHERE term=$term ORDER BY first_name, last_name");

    $players = array();
    $ids = array();

    global $PLAYER_TYPES;
    foreach($PLAYER_TYPES as $player_type)
    {
        if( !isset($ids[$player_type]) )
            $ids[$player_type] = array();
    }

    while($row = $result->fetch_assoc())
    {
        $id = $row["id"];
        $type = $row["type"];
        $players[$id] = $row;
        array_push($ids[$type], $id);
    }

    return array("ids" => $ids, "players" => $players);
}

function get_game_archive($term)
{
    $term = sql()->real_escape_string($term);
    $result = sql()->query("SELECT * FROM hvz_game_archive WHERE term=$term");

    return $result->fetch_assoc();
}