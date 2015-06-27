<?php
/**
 * Created by PhpStorm.
 * User: Dale
 * Date: 2015-03-06
 * Time: 4:10 PM
 */

require_once(dirname(__FILE__) . "/sql.php");

function check_briefing_conflict($release, $expire)
{
    global $sql;

    $release = $sql->real_escape_string($release);
    $expire = $sql->real_escape_string($expire);

    $query = "SELECT id FROM hvz_briefings WHERE (release_time <= '$release' and expire_time > '$release') or (release_time < '$expire' and expire_time >= '$expire')";
    $result = $sql->query($query);

    if (mysqli_num_rows($result) == 0)
    {
        return FALSE;
    }

    return TRUE;
}

function schedule_briefing($release, $expire, $title, $body)
{
    global $sql;

    $release = $sql->real_escape_string($release);
    $expire = $sql->real_escape_string($expire);
    $title = $sql->real_escape_string($title);
    $body = $sql->real_escape_string($body);

    if (!$sql->query("INSERT INTO hvz_briefings (release_time, expire_time, title, body) VALUES ('$release', '$expire', '$title', '$body');"))
    {
        return FALSE;
    }

    return TRUE;
}

function update_briefing($id, $release, $expire, $title, $body)
{
    global $sql;

    $id = $sql->real_escape_string($id);
    $release = $sql->real_escape_string($release);
    $expire = $sql->real_escape_string($expire);
    $title = $sql->real_escape_string($title);
    $body = $sql->real_escape_string($body);

    if (!$sql->query("UPDATE hvz_briefings SET release_time = '$release', expire_time = '$expire', title = '$title', body = '$body' WHERE id= '$id';"))
    {
        return FALSE;
    }

    return TRUE;
}

function delete_briefing($id)
{
    global $sql;

    $id = $sql->real_escape_string($id);

    if (!$sql->query("DELETE FROM hvz_briefings WHERE id='$id';"))
    {
        return FALSE;
    }

    return TRUE;
}

function get_active_briefings()
{
    global $sql;

    $briefings = array();
    $current_time = date("Y-m-d H:i:s");
    $query = "SELECT title,body FROM hvz_briefings WHERE release_time <= '$current_time' AND expire_time > '$current_time'";


    if ($result = $sql->query($query))
    {
        $key = 0;
        while ( $row = $result->fetch_assoc() )
        {
            $briefings[$key] = $row;
            $key += 1;
        }
    }

    return $briefings;
}

function get_queued_briefings()
{
    global $sql;

    $briefings = array();
    $current_time = date("Y-m-d H:i:s");
    $query = "SELECT release_time FROM hvz_briefings WHERE release_time > '$current_time' ORDER BY release_time ASC;";

    if ($result = $sql->query($query))
    {
        $key = 0;
        while ( $row = $result->fetch_assoc() )
        {
            $briefings[$key] = $row;
            $key += 1;
        }
    }

    return $briefings;
}


function get_briefings()
{
    global $sql;

    $briefings = array();
    $query = "SELECT * FROM hvz_briefings";

    $result = $sql->query($query);
    while( $row = $result->fetch_assoc() )
    {
        $briefings[$row["id"]] = $row;
    }

    return $briefings;
}

function get_briefing($id)
{
    global $sql;

    $id = $sql->real_escape_string($id);

    $briefings = array();
    $query = "SELECT * FROM hvz_briefings WHERE id=$id";
    $result = $sql->query($query);

    while( $row = $result->fetch_assoc())
    {
        $briefings[$row["id"]] = $row;
    }

    return $briefings["$id"];
}

$briefingProperties = json_decode( "{
						\"title\": 		    { \"display\":\"Title\", \"type\":\"text\"},
                        \"release_time\": 	{ \"display\":\"Release\", \"type\":\"text\"},
                        \"expire_time\": 	{ \"display\":\"Expire\", \"type\":\"text\"}
                    }", true);

function gen_briefing_table()
{
    $briefings = get_briefings();

    //echo "<table class='players'>";
    echo "<table class='table table-striped table-bordered table-condensed'>";
    echo "<tr>";
    echo "<th style='display: none'>";
    echo "</th>";

    echo "<th>Title</th>";
    echo "<th>Release</th>";
    echo "<th>Expire</th>";
    //echo "<th></th>";
    echo "</tr>";

    foreach ($briefings as $key=>$briefing)
    {
        echo "<tr class='player'>";

        echo "<form action='' method='post'>";
        echo "<input type='hidden' value='$briefing[id]' name='id'/>";
        echo "<td style='display: none'></td>";
        echo "<td>$briefing[title]</td>";
        echo "<td>$briefing[release_time]</td>";
        echo "<td>$briefing[expire_time]</td>";
        //echo "<td>";
        //echo "<form action='' method='post'>";
        //echo "<input type='submit' value='Edit' name='action'/>";
        //echo "<input type='hidden' value='$briefing[id]' name='edit_id'/>";
        //echo "</td>";
        echo "</form>";

        echo "</tr>";
    }

    echo "</table>";
}
?>