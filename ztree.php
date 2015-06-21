<?php
session_start();
require_once("require/sql.php");
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/basic.php");

Secure(false);
if( IsPlayer() && !IsZombie() )
{
	to_panel("&Can not access zombie only page.");
}

$players = get_players( "ZOMBIE" );
$kills_by_id = load_kill_table( );
$textsize = 12;

function get_name( $id )
{
	global $players;
	return $players[$id]["first_name"] . " " . $players[$id]["last_name"];
}
function padding() { global $padding; return $padding; }
function draw_text( $text, $x, $y, $angle )
{
	global $img;
	global $white;
	global $font;
	global $textsize;
	imagettftext($img, $textsize, $angle, $x, $y, $white, $font, $text);
}
function draw_arrow( $x1, $y1, $x2, $y2 )
{
	global $img;
	global $white;
	global $red;
	$d = 3;
	imageline( $img, $x1, $y1, $x2, $y2, $red );
	
	$px = $y2-$y1;
	$py = $x2-$x1;
	$py *= -1;
	
	$len = sqrt( ($px)*($px) + ($py)*($py) );
	$px /= $len;
	$px *= $d;
	$py /= $len;
	$py *= $d;
	
	$dx = $x2-$x1;
	$dy = $y2-$y1;
	$dx /= $len;
	$dy /= $len;
	$dx *= ($len-$d);
	$dy *= ($len-$d);
	
	$dx += $px;
	$dy += $py;
	imageline( $img, $x2, $y2, $x1+$dx, $y1+$dy, $red );
	
	$dx -= 2*$px;
	$dy -= 2*$py;
	imageline( $img, $x2, $y2, $x1+$dx, $y1+$dy, $red );
}
function text_bounds( $text, $angle, $x=0, $y=0 )
{
	global $font;
	global $textsize;
	$bounds = imagettfbbox( $textsize, $angle, $font, $text );
	for($i = 0; $i < 8; $i += 2)
	{
		$bounds[$i] += $x;
		$bounds[$i+1] += $y;
	}
	return $bounds;
}
function text_width( $text )
{
	$bounds = text_bounds($text, 0);
	return $bounds[2]-$bounds[0];
}

function load_kill_table( )
{
	global $sql;
	$result = $sql->query("SELECT `killer`, `victim` FROM `hvz_tags` ORDER BY `killer`");

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

function count_leaves( $killer_id )
{
	global $counts;
	global $kills_by_id;
	
	if( !isset($kills_by_id[$killer_id]) ) return 1;
	$result = 0;
	foreach( $kills_by_id[$killer_id] as $victim )
	{
		$delta = count_leaves( $victim );
		$result += $delta;
		$counts[$victim] = $delta;
	}
	return $result;
}

function extend_bounds( &$minx, &$miny, &$maxx, &$maxy, $new_bound )
{
	for( $i = 0; $i < 8; $i += 2 )
	{
		$x = $new_bound[$i];
		$y = $new_bound[$i+1];
		if( $x > $maxx ) $maxx = $x;
		if( $y > $maxy ) $maxy = $y;
		if( $x < $minx ) $minx = $x;
		if( $y < $miny ) $miny = $y;
	}
}

function get_tree_bounds( &$minx, &$miny, &$maxx, &$maxy, $kills, $kills_by_id, $counts, $count, $height, $r, $ang=0, $angs=360, $level=1 )
{
	if( $kills === NULL ) return;
	$x = 0;
	$y = 0;
	
	$angle_per_leaf = $angs/$count;
	$angle = $ang;
	$maxw = 0;
	$angles = array();
	
	foreach( $kills as $victim )
	{
		$name = get_name($victim);
		
		array_push($angles, $angle);
		$delta = $counts[$victim]*$angle_per_leaf;
		$angle += $delta/2;
		
		$rad = deg2rad($angle);
		$shiftx = -sin($rad)*$height/2;
		$shifty = -cos($rad)*$height/2;
		$dx = cos($rad)*$r;
		$dy = -sin($rad)*$r;
		$w = text_width($name);
		if( $w > $maxw ) $maxw = $w;
		
		$bounds = text_bounds($name, $angle, $x+$dx+$shiftx, $y+$dy+$shifty);
		extend_bounds($minx, $miny, $maxx, $maxy, $bounds);
		$angle += $delta/2;
	}
	array_push($angles, $ang+$angs);

	$i = 0;
	foreach( $kills as $victim )
	{
		if( isset( $kills_by_id[$victim] ) )
		{
			$ang_start = $angles[$i];
			$ang_len = $angles[$i+1] - $ang_start;
			get_tree_bounds($minx, $miny, $maxx, $maxy, $kills_by_id[$victim], $kills_by_id, $counts, $counts[$victim], $height, $r+$maxw+padding()*$level, $ang_start, $ang_len, $level+1);
		}
		$i++;
	}
}

function draw_tree( $kills, $kills_by_id, $counts, $count, $height, $r, $x, $y, $ang=0, $angs=360, $level=1 )
{
	if( $kills === NULL ) return array();
	
	$angle_per_leaf = $angs/$count;
	$angle = $ang;
	$maxw = 0;
	$angles = array();
	$positions1 = array();
	$positions2 = array();
	
	foreach( $kills as $victim )
	{
		$name = get_name($victim);
		
		array_push($angles, $angle);
		$delta = $counts[$victim]*$angle_per_leaf;
		$angle += $delta/2;
		
		$rad = deg2rad($angle);
		$shiftx = -sin($rad)*$height/2;
		$shifty = -cos($rad)*$height/2;
		$dx = cos($rad)*$r;
		$dy = -sin($rad)*$r;
		$w = text_width($name);
		if( $w > $maxw ) $maxw = $w;
		
		array_push( $positions1, array( "x" => $x+$dx, "y" => $y+$dy ) );
		array_push( $positions2, array( "x" => $x+$dx/$r*($r+$w), "y" => $y+$dy/$r*($r+$w) ) );
		
		draw_text($name, $x+$dx+$shiftx, $y+$dy+$shifty, $angle);
		$angle += $delta/2;
	}
	array_push($angles, $ang+$angs);

	$i = 0;
	foreach( $kills as $victim )
	{
		if( isset( $kills_by_id[$victim] ) )
		{
			$ang_start = $angles[$i];
			$ang_len = $angles[$i+1] - $ang_start;
			$new_positions = draw_tree($kills_by_id[$victim], $kills_by_id, $counts, $counts[$victim], $height, $r+$maxw+padding()*$level, $x, $y, $ang_start, $ang_len, $level+1);
			
			foreach( $new_positions as $p )
			{
				draw_arrow( $positions2[$i]["x"], $positions2[$i]["y"], $p["x"], $p["y"] );
			}
		}
		$i++;
	}
	return $positions1;
}

$font = "./arial.ttf";
$total_leaves = count_leaves(NULL);

$text_height = 0;
$padding = 40;
$edge_padding = 30;

$bounds = text_bounds( "X", 0 );
$text_height = $bounds[7]-$bounds[1];

$minx = 0;
$miny = 0;
$maxx = 0;
$maxy = 0;
get_tree_bounds( $minx, $miny, $maxx, $maxy, $kills_by_id[NULL], $kills_by_id, $counts, $total_leaves, $text_height, padding() );
$width = intval($maxx-$minx)+$edge_padding*2;
$height = intval($maxy-$miny)+$edge_padding*2;
$img = @imagecreate( $width, $height );

$x = -$minx+$edge_padding;
$y = -$miny+$edge_padding;

//$font = "arial.ttf";
$black = imagecolorallocate($img, 0, 0, 0);
$white = imagecolorallocate($img, 225, 225, 225);
$red = imagecolorallocate($img, 225, 0, 0);

$positions = draw_tree( $kills_by_id[NULL], $kills_by_id, $counts, $total_leaves, $text_height, padding(), $x, $y );
foreach($positions as $p)
{
	draw_arrow( $x, $y, $p["x"], $p["y"] );
}
$bounds = text_bounds("OZ", 0);
$ozw = $bounds[2]-$bounds[0];
$ozh = $bounds[7]-$bounds[1];
draw_text("OZ", $x-$ozw/2, $y-$ozh/2, 0);

header("Content-Type: image/png");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

imagepng($img);
imagedestroy($img);

?>
