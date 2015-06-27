<?php
/**
 * Created by PhpStorm.
 * User: Dale
 * Date: 2015-06-25
 * Time: 12:07 AM
 */

require_once(dirname(__FILE__) . "/sql.php");

function add_location($location)
{
    global $sql;

    $location = $sql->real_escape_string($location);

    if (!$sql->query("INSERT INTO hvz_signup_locations (name) VALUES ('$location');"))
    {
        return FALSE;
    }

    return TRUE;
}

function get_total_signups_at_location($id)
{
    global $sql;

    $id = $sql->real_escape_string($id);

    $query = "SELECT hvz_signup_locations.id, hvz_signup_locations.name, COUNT(hvz_players.location_id) AS signups
              FROM hvz_signup_locations LEFT JOIN hvz_players ON (hvz_signup_locations.id = hvz_players.location_id)
              WHERE hvz_signup_locations.id = '$id'
              GROUP BY hvz_signup_locations.id";

    $result = $sql->query($query);
    $row = $result->fetch_assoc();

    return intval($row["signups"]);
}

function delete_location($id)
{
    global $sql;

    $id = $sql->real_escape_string($id);

    if (!$sql->query("DELETE FROM hvz_signup_locations WHERE id='$id';"))
    {
        return FALSE;
    }

    return TRUE;
}

function get_locations_with_signups()
{
    global $sql;

    $locations = array();
    $query = "SELECT hvz_signup_locations.id, hvz_signup_locations.name, COUNT(hvz_players.location_id) AS signups
              FROM hvz_signup_locations LEFT JOIN hvz_players ON (hvz_signup_locations.id = hvz_players.location_id)
              GROUP BY hvz_signup_locations.id";

    $result = $sql->query($query);
    while( $row = $result->fetch_assoc() )
    {
        $locations[$row["id"]] = $row;
    }

    return $locations;
}

function get_locations()
{
    global $sql;

    $locations = array();
    $query = "SELECT * FROM hvz_signup_locations";

    $result = $sql->query($query);
    while( $row = $result->fetch_assoc() )
    {
        $locations[$row["id"]] = $row;
    }

    return $locations;
}

$locationProperties = json_decode( "{
						\"name\": 		    { \"display\":\"Name\", \"type\":\"text\"},
                        \"signups\": 	{ \"display\":\"Sign Ups\", \"type\":\"text\"}
                    }", true);

function gen_location_table()
{
    $locations = get_locations_with_signups();

    //echo "<table class='players'>";
    echo "<table class='table table-striped table-bordered table-condensed'>";
    echo "<tr>";
    echo "<th style='display: none'>";
    echo "</th>";

    echo "<th>Location</th>";
    echo "<th>Sign Ups</th>";
    //echo "<th>Release</th>";
    //echo "<th>Expire</th>";
    //echo "<th></th>";
    echo "</tr>";

    foreach ($locations as $key=>$location)
    {
        echo "<tr class='player'>";

        echo "<form action='' method='post'>";
        echo "<input type='hidden' value='$location[id]' name='id'/>";
        echo "<td style='display: none'></td>";
        echo "<td>$location[name]</td>";
        echo "<td>$location[signups]</td>";
        //echo "<td>$briefing[release_time]</td>";
        //echo "<td>$briefing[expire_time]</td>";
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
