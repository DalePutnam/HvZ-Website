<?php
header("Content-Type: image/png");

$width = 800;
$height = 600;
$img = @imagecreate( $width, $height );

$font = "/users/watsfic/www/hvz/arial.ttf";
//$font = "arial.ttf";
$black = imagecolorallocate($img, 0, 0, 0);
$white = imagecolorallocate($img, 225, 225, 225);
$red = imagecolorallocate($img, 225, 0, 0);
$padding = 20;

$data = array(
	"Brook" => array(
		"Bob" ),
	"Grant",
	"Devin" => array( 
		"Jack",
		"Jill" => array( 
			"Allison",
			"Bill",
			"Christabelle",
			"Dylan",
		)),
);

$counts = array();

function count_leaves( $root )
{
	global $counts;
	$result = 0;
	foreach( $root as $key=>$value )
	{
		if( is_string($value ) )
		{
			$result++;
			$counts[$value] = 1;
		}
		else
		{
			$delta = count_leaves( $value );
			$result += $delta;
			$counts[$key] = $delta;
		}
	}
	return $result;
}
function padding() { global $padding; return $padding; }
function draw_text( $text, $x, $y, $angle )
{
	global $img;
	global $white;
	global $font;
	imagettftext($img, 8, $angle, $x, $y, $white, $font, $text);
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
function text_bounds( $text, $angle )
{
	global $font;
	return imagettfbbox( 8, $angle, $font, $text );
}
function draw_tree( $root, $counts, $count, $height, $r, $x, $y, $ang=0, $angs=360, $level=1 )
{
	$angle_per_leaf = $angs/$count;
	$angle = $ang;
	$maxw = 0;
	$angles = array();
	$positions1 = array();
	$positions2 = array();
	foreach( $root as $key=>$value )
	{
		$name = $key;
		if( is_string($value ) ) $name = $value;
		
		array_push($angles, $angle);
		$delta = $counts[$name]*$angle_per_leaf;
		$angle += $delta/2;
		
		$rad = deg2rad($angle);
		$shiftx = -sin($rad)*$height/2;
		$shifty = -cos($rad)*$height/2;
		$dx = cos($rad)*$r;
		$dy = -sin($rad)*$r;
		$bounds = text_bounds($name, 0);
		$w = $bounds[2]-$bounds[0];
		if( $w > $maxw ) $maxw = $w;
		
		array_push( $positions1, array( "x" => $x+$dx, "y" => $y+$dy ) );
		array_push( $positions2, array( "x" => $x+$dx/$r*($r+$w), "y" => $y+$dy/$r*($r+$w) ) );
		
		draw_text($name, $x+$dx+$shiftx, $y+$dy+$shifty, $angle);
		$angle += $delta/2;
	}
	array_push($angles, $ang+$angs);

	$i = 0;
	foreach( $root as $key=>$value )
	{
		if( !is_string($value) )
		{
			$ang_start = $angles[$i];
			$ang_len = $angles[$i+1] - $ang_start;
			$new_positions = draw_tree($value, $counts, $counts[$key], $height, $r+$maxw+padding()*$level, $x, $y, $ang_start, $ang_len, $level+1);
			
			foreach( $new_positions as $p )
			{
				draw_arrow( $positions2[$i]["x"], $positions2[$i]["y"], $p["x"], $p["y"] );
			}
		}
		$i++;
	}
	return $positions1;
}
$total_leaves = count_leaves($data);

$x = $width / 2;
$y = $height / 2;

$text_height = 0;

$bounds = text_bounds( "X", 0 );
$text_height = $bounds[7]-$bounds[1];

$positions = draw_tree( $data, $counts, $total_leaves, $text_height, padding(), $x, $y );
foreach($positions as $p)
{
	draw_arrow( $x, $y, $p["x"], $p["y"] );
}
$bounds = text_bounds("OZ", 0);
$ozw = $bounds[2]-$bounds[0];
$ozh = $bounds[7]-$bounds[1];
draw_text("OZ", $x-$ozw/2, $y-$ozh/2, 0);

imagepng($img);
imagedestroy($img);