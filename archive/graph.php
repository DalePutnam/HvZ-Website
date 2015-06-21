<?php
/**
 * Created by PhpStorm.
 * User: brwarner
 * Date: 21/02/14
 * Time: 11:55 PM
 */

require_once("../require/basic.php");
require_once("../require/tree.php");
require_once("../require/rplayers.php");

$archive = page_archive_head();

function format_date( $time )
{
    return date('l F jS, Y \a\t g:iA',$time);
}

// Constants
$width = 600;
$height = 400;
$max = 0;

$game_info = get_game_archive($archive);

// Start/End
$r_start =  strtotime($game_info["reg_start"])*1000;
$r_end =    strtotime($game_info["reg_end"])*1000;
$g_start =  strtotime($game_info["start_date"])*1000;
$g_end =    strtotime($game_info["end_date"])*1000;

// Data
$registrations = array();
$tags = array();
$stuns = array();
$reg_days = array( 0, 0, 0, 0, 0 );
$tag_days = array( 0, 0, 0, 0, 0 );
$stun_days = array( 0, 0, 0, 0, 0 );
$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday");

$result = $sql->query("SELECT reg_date FROM hvz_player_archive WHERE (type = 'HUMAN' OR type='ZOMBIE' OR type='NONE') AND reg_date > '" . $game_info["reg_start"] . "' AND reg_date < '" . $game_info["reg_end"] . "' AND term = '$archive' ORDER BY reg_date");
while( $row = $result->fetch_assoc() )
{
    array_push( $registrations, strtotime( $row["reg_date"] )*1000 );

    $date_num = ((int)date( "w", strtotime($row["reg_date"]) )) - 1;
    $reg_days[$date_num]++;
    $max++;
}
$result->free();

$max += 50;

$result = $sql->query("SELECT time FROM hvz_stun_archive WHERE time > '" . $game_info["start_date"] . "' AND term = '$archive' ORDER BY time");
while( $row = $result->fetch_assoc() )
{
    array_push( $stuns, strtotime( $row["time"] )*1000 );

    $date_num = ((int)date( "w", strtotime($row["time"]) )) - 1;
    $stun_days[$date_num]++;
}
$result->free();

$result = $sql->query("SELECT time FROM hvz_tag_archive WHERE time > '" . $game_info["start_date"] . "' AND term = '$archive' ORDER BY time");
while( $row = $result->fetch_assoc() )
{
    array_push( $tags, strtotime( $row["time"] )*1000 );

    $date_num = ((int)date( "w", strtotime($row["time"]) )) - 1;
    $tag_days[$date_num]++;
}
$result->free();

?>
<script type="text/javascript">
    // Data from PHP
    var registrations = <?php echo json_encode( $registrations ); ?>;
    var tags = <?php echo json_encode( $tags ); ?>;
    var stuns = <?php echo json_encode( $stuns ); ?>;
    var r_start = <?php echo $r_start; ?>;
    var r_end = <?php echo $r_end; ?>;
    var g_start = <?php echo $g_start; ?>;
    var g_end = <?php echo $g_end; ?>;
    var width = <?php echo $width; ?>;
    var height = <?php echo $height; ?>;
    var max = <?php echo $max; ?>;
    var now = <?php echo time()*1000; ?>;

    // Setup
    $( document ).ready( function() {
        createDateGraph( $("#graph"), [r_start, g_start, g_start], [r_end, g_end, g_end], width, height, [registrations, tags, stuns], ["black", "red", "blue"], max + 50, now );
    });
</script>
<strong>Registration Start:</strong> <?php echo format_date( $r_start/1000 ); ?><br/>
<strong>Registration End:</strong> <?php echo format_date( $r_end/1000 ); ?><br/>
<strong>Game Start:</strong> <?php echo format_date( $g_start/1000 ); ?><br/>
<strong>Game End:</strong> <?php echo format_date( $g_end/1000 ); ?><br/>
<table style="text-align: center; vertical-align: center;">
    <tr>
        <td>
            <table style="text-align: center; vertical-align: center;">
                <tr><td><strong>Players</strong><br/>(increments of 10)</td><td><canvas id="graph" width="<?php echo $width; ?>" height="<?php echo $height; ?>"></canvas></td></tr>
                <tr><td>&nbsp;</td><td><strong>Time</strong><br/>(from respective start)<br/>(increments of 2 hours)</td></tr>
            </table>
        </td>
        <td style="text-align:left;">
            <strong>Legend</strong><br/>
            <strong>--</strong>&nbsp;Registration<br/>
            <strong style="color: red;">--</strong>&nbsp;Tags<br/>
            <strong style="color: blue;">--</strong>&nbsp;Stuns<br/><br/>
            <strong>Players By The Numbers</strong><br/>
            <span style="font-size:50%"><a href="http://www.youtube.com/watch?v=fmRjgWW8yn0">(troubles by the score...)</a></span><br/>
            <table>
                <tr><th>Day</th><th>Registrations</th><th>Tags</th><th>Stuns</th></tr>
                <?php
                for( $i = 0; $i < 5; $i++ )
                {
                    echo "<tr><td>{$days[$i]}</td><td>{$reg_days[$i]}</td><td>{$tag_days[$i]}</td><td>{$stun_days[$i]}</td></tr>";
                }
                ?>
            </table>
        </td>
    </tr>
</table>
<?php
page_archive_foot();