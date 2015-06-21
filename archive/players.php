<?php
/**
 * Created by PhpStorm.
 * User: brwarner
 * Date: 18/02/14
 * Time: 9:57 PM
 */

require_once("../require/basic.php");
require_once("../require/rarchive.php");
require_once("../require/secure.php");

$archive = page_archive_head();

$result = get_players_and_ids_archive($archive);
$players = $result["players"];
$ids = $result["ids"];

?>
<table class="archive-players">
    <tr>
        <?php
        foreach($ids as $type=>$entries)
        {
            if($type == "BANNED" || $type == "NONE") continue;
            if(count($entries) == 0) continue;
            echo "<td>";
            echo "<h2>" . TeamName($type) . " (" . count($entries) . ")</h2>";
            echo "<div id='$type'>";
            gen_player_table($players, $namePlayerProperties, $entries);
            echo "</div>";
            echo "</td>";
        }
        ?>
    </tr>
</table>

<?php
page_archive_foot();