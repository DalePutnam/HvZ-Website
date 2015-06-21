<?php
/**
 * Created by PhpStorm.
 * User: brwarner
 * Date: 20/02/14
 * Time: 7:55 PM
 */
require_once("../require/basic.php");
require_once("../require/tree.php");
require_once("../require/rplayers.php");

if(!isset($_REQUEST["archive"]))
{
    // Will display an error since archive is not set
    page_archive_head();
}
$archive = $_REQUEST["archive"];

function load_kill_table( $archive )
{
    global $sql;
    $archive = $sql->real_escape_string($archive);
    $result = $sql->query("SELECT killer, victim FROM hvz_tag_archive WHERE term=$archive ORDER BY killer");

    $tmp_killer = NULL;
    $tmp_kill_table = array();

    $kills_by_id = array();

    while( $row = $result->fetch_assoc() )
    {
        if($tmp_killer != $row["killer"])
        {
            $kills_by_id[$tmp_killer] = $tmp_kill_table;
            $tmp_killer = $row["killer"];
            $tmp_kill_table = array();
        }
        array_push( $tmp_kill_table, $row["victim"] );
    }
    $kills_by_id[$tmp_killer] = $tmp_kill_table;
    return $kills_by_id;
}
function load_names_table( $archive )
{
    global $sql;
    $archive = $sql->real_escape_string($archive);
    $result = $sql->query("SELECT id, CONCAT(first_name, ' ', last_name) FROM hvz_player_archive WHERE term=$archive");

    $names = array();
    while( $row = $result->fetch_row() )
    {
        $names[$row[0]] = $row[1];
    }
    return $names;
}

$kill_table = load_kill_table($archive);
$name_table = load_names_table($archive);
/*$kill_table = array(NULL => array(1,2,3), 2=>array(4,5), 3=>array(6), 4=>array(7));
$name_table = array("Test", "A", "B", "C", "D", "E", "F", "G", "H", "I");*/

$img = new ImageTree();
$img->load_data($name_table, $kill_table);
$img->init();
$img->draw();

header("Content-Type: image/png");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
$img->output();
