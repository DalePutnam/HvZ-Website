<?php
session_start();
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/basic.php");
require_once("require/rgame.php");

$securemode = !Unsecure();

if( $securemode )
{
	Secure(false);
	page_head();
}
else
{
	page_unsecure_head();
}

function fdate( $time )
{
	return date('l F jS \a\t g:iA',$time);
}

// Constants
$width = 600;
$height = 400;
$max = 800;

// Start/End
$r_start = php_reg_start()*1000;
$r_end =  php_reg_end()*1000;
$g_start =  php_game_start()*1000;
$g_end =  php_game_end()*1000;

// Data
$registrations = array();
$tags = array();
$stuns = array();
$reg_days = array( 0, 0, 0, 0, 0 );
$tag_days = array( 0, 0, 0, 0, 0 );
$stun_days = array( 0, 0, 0, 0, 0 );
$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday");

$result = $sql->query("SELECT `reg_date` FROM `hvz_players` WHERE (`type` = 'HUMAN' OR `type`='ZOMBIE' OR `type`='NONE') AND `reg_date` > '" . get_reg_start() . "' ORDER BY `reg_date`");
while( $row = $result->fetch_assoc() )
{
	array_push( $registrations, strtotime( $row["reg_date"] )*1000 );
	
	$datenum = ((int)date( "w", strtotime($row["reg_date"]) )) - 1;
	//$reg_days[$datenum]++;
}
$result->free();

$result = $sql->query("SELECT `time` FROM `hvz_stuns` WHERE `ratified` IS NOT NULL AND `time` > '" . get_game_start() . "' ORDER BY `time`");
while( $row = $result->fetch_assoc() )
{
	array_push( $stuns, strtotime( $row["time"] )*1000 );
	
	$datenum = ((int)date( "w", strtotime($row["time"]) )) - 1;
	//$stun_days[$datenum]++;
}
$result->free();

$result = $sql->query("SELECT `time` FROM `hvz_tags` WHERE `time` > '" . get_game_start() . "' ORDER BY `time`");
while( $row = $result->fetch_assoc() )
{
	array_push( $tags, strtotime( $row["time"] )*1000 );
	
	$datenum = ((int)date( "w", strtotime($row["time"]) )) - 1;
	//$tag_days[$datenum]++;
}
$result->free();

// Test Data
/*$val = $g_start;
for( $i = 0; $i < 200; $i++ )
{
	$val += rand( 1000*60*5, 1000*60*20 );
	array_push($tags, $val );
	
	$datenum = ((int)date( "w", $val/1000 )) - 1;
	$tag_days[$datenum]++;
}*/

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
		createDateGraph( $("#graph"), [r_start, g_start, g_start], [r_end, g_end, g_end], width, height, [registrations, tags, stuns], ["black", "red", "blue"], max, now );
	});
</script>
<div class="row">
    <div class="col-md-12">
        <h2>Graphs</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-5"><label>Registration Start:</label></div>
            <div class="col-md-7"><?php echo fdate( $r_start/1000 ); ?></div>
        </div>
        <div class="row">
            <div class="col-md-5"><label>Registration End:</label></div>
            <div class="col-md-7"><?php echo fdate( $r_end/1000 ); ?></div>
        </div>
        <div class="row">
            <div class="col-md-5"><label>Game Start:</label></div>
            <div class="col-md-7"><?php echo fdate( $g_start/1000 ); ?></div>
        </div>
        <div class="row">
            <div class="col-md-5"><label>Game End:</label></div>
            <div class="col-md-7"><?php echo fdate( $g_end/1000 ); ?></div>
        </div>
    </div>
</div>
<p><i>Graph only shows ratified stuns, meaning stun data won't appear until ~1AM the following morning</i></p>
<!-- <p><i>Current tag data is for testing purposes. It will change to the real data when the game starts</i></p> -->
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
if( $securemode )
{
	page_foot();
}
else
{
	page_unsecure_foot();
}
?>
