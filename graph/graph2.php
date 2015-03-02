<?php	
	//get sql function the reads database variables from config
	require("../require/sql.php");
	require("../require/rgame.php");
	global $sql;
	
	if( !isset( $type ) ) { $type = 1; }
	if( $type != 1 && $type != 2 ) { exit("Hey, what are you doing!"); }
	
?>
<html>
	<head>
		<title>HvZ <?php if( $type == 1 ) { echo "Signup "; } else { echo "Tag "; } ?>Graph</title>
		<?php
			$width = 600;
			$height = 400;
			$start_date = "";
			$end_date = "";
			$max = 750;
			if( $type == 1 ) { $start_date = get_reg_start(); $end_date = get_reg_end(); }
			if( $type == 2 ) { $start_date = get_game_start(); $end_date = get_game_end(); }
		?>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<script type="text/javascript">
			function drawGrid( ctx, width, height, widthGridSize, heightGridSize, widthMarkInterval, heightMarkInterval, widthUnitsPerGrid, heightUnitsPerGrid ) {
				ctx.strokeStyle = "red";
				ctx.beginPath();
				ctx.font = "10px sans-serif";
				ctx.moveTo( 0, 0 );
				ctx.lineTo( 0, height );
				ctx.lineTo( width, height );
				ctx.stroke();
				
				for( var i = 0; i < width/widthGridSize; i++ ) {
					ctx.globalAlpha = 0.5;
					ctx.strokeStyle = "green";
					if( i % (widthMarkInterval) == 0 ) { ctx.strokeStyle = "red"; ctx.globalAlpha = 1; }
					ctx.beginPath();
					ctx.moveTo( i*widthGridSize, 0 );
					ctx.lineTo( i*widthGridSize, height );
					ctx.stroke();
					
					if( i % (widthMarkInterval) == 0 ) {
						ctx.strokeStyle = "black";
						ctx.strokeText( widthUnitsPerGrid * i, i*widthGridSize - 5, height - 5 );
					}
				}
				for( var i = 0; i <= height/heightGridSize; i++ ) {
					ctx.globalAlpha = 0.5;
					ctx.strokeStyle = "green";
					if( i % (heightMarkInterval) == 0 ) { ctx.strokeStyle = "orange"; ctx.globalAlpha = 1; }
					ctx.beginPath();
					ctx.moveTo( 0, height - i*heightGridSize );
					ctx.lineTo( width, height - i*heightGridSize );
					ctx.stroke();
					
					if( i % (heightMarkInterval) == 0 ) {
						ctx.strokeStyle = "black";
						ctx.strokeText( heightUnitsPerGrid * i, 5, height - i*heightGridSize + 3 );
					}
				}
				// old drawing code
				/*for( var i = 0; i <= width; i += widthGridSize ) {
					ctx.globalAlpha = 0.5;
					ctx.strokeStyle = "green";
					if( i % (widthMarkInterval*widthGridSize) == 0 ) { ctx.strokeStyle = "red"; ctx.globalAlpha = 1; }
					ctx.beginPath();
					ctx.moveTo( i, 0 );
					ctx.lineTo( i, height );
					ctx.stroke();
					
					if( i % (widthMarkInterval*widthGridSize) == 0 ) {
						ctx.strokeStyle = "black";
						ctx.strokeText( widthUnitsPerGrid * i / widthGridSize, i - 5, height - 5 );
					}
				}
				for( var i = 0; i <= height; i += heightGridSize ) {
					ctx.globalAlpha = 0.5;
					ctx.strokeStyle = "green";
					if( i % (heightMarkInterval*heightGridSize) == 0 ) { ctx.strokeStyle = "orange"; ctx.globalAlpha = 1; }
					ctx.beginPath();
					ctx.moveTo( 0, height - i );
					ctx.lineTo( width, height - i );
					ctx.stroke();
					
					if( i % (heightMarkInterval*heightGridSize) == 0 ) {
						ctx.strokeStyle = "black";
						ctx.strokeText( heightUnitsPerGrid * i / heightGridSize, 5, height - i + 3 );
					}
				}*/
				ctx.globalAlpha = 1;
			}
		
			function drawGraph( ctx, width, height, sortedDateObjects, startTime, endTime, maxPlayers, mouse ) {
				var range = endTime.getTime() - startTime.getTime();
				var pixelPerTime = width / range;
				var pixelPerPlayer = height / maxPlayers;
				
				ctx.clearRect( 0, 0, width, height );
				drawGrid( ctx, width, height, (2 * 60 * 60 * 1000 * pixelPerTime), (10 * pixelPerPlayer), 12, 10, 2, 10 );
				
				ctx.beginPath();
				ctx.strokeStyle = "black";
				ctx.moveTo(0,height);
				var playerCount = 0;
				
				var mouseCount = -1;
				var mouseTime = -1;
				if( mouse != -1 )
				{
					mouseTime = mouse / pixelPerTime + startTime.getTime();
				}
				
				for( var i = 0; i < sortedDateObjects.length; i++ ) {
					var time = sortedDateObjects[i].getTime();
					if( time < startTime.getTime() || time > endTime.getTime() ) continue;
					
					if( mouse != -1 && mouseCount == -1 && time > mouseTime )
					{
						mouseCount = playerCount;
					}
					
					ctx.lineTo( pixelPerTime * (time - startTime.getTime()), height - pixelPerPlayer * playerCount );
					playerCount ++;
					ctx.lineTo( pixelPerTime * (time - startTime.getTime()), height - pixelPerPlayer * playerCount );
				}
				ctx.stroke();
				
				if( mouse != -1 && mouseCount != -1 )
				{
					var mouseY = height - pixelPerPlayer * mouseCount
					ctx.beginPath();
					ctx.arc( mouse, mouseY, 5, 0, Math.PI * 2, false );
					ctx.fillStyle = 'black';
					ctx.fill();
					ctx.stroke();
					
					ctx.font = "12px Arial";
					ctx.fillText( "Players: " + mouseCount, mouse - 20, mouseY - 25 );
					ctx.fillText( "Hours: " + Math.floor((mouseTime - startTime.getTime())/1000/60/60), mouse - 20, mouseY - 10 );
				}
				
				return playerCount;
			}
			
			var start = new Date("<?php echo date( 'm/d/Y H:i', strtotime($start_date)); ?>");
			var end = new Date("<?php echo date( 'm/d/Y H:i', strtotime($end_date)); ?>");
			var width = <?php echo $width;?>;
			var height = <?php echo $height;?>;
			
			var dates = new Array();
			var daycounts = new Array();
			<?php
				$query = "";
				if( $type == 1  ) { $query = "select `reg_date` from `hvz_players` WHERE (`type` = 'HUMAN' OR `type`='ZOMBIE' OR `type`='NONE') AND `reg_date` > '$start_date' ORDER BY `reg_date`"; }
				else
				{
					$query = "select date_killed from hvz_players WHERE banned_bool = 'false' AND permission = 'player' AND hvz_type='zombie' ORDER BY date_killed";
				}
				$result = $sql->query($query);
				$table = array(0,0,0,0,0);
				
				while( $row = $result->fetch_assoc() ) {
					$text = "";
					if( $type == 1 ) { $text = $row["reg_date"]; }
					else { $text = $row["date_killed"]; }
					$value = date( "F j Y G:i:s", strtotime($text) );
					echo "dates.push( new Date(\"" . $value . "\") );";
					$datenum = ((int)date( "w", strtotime($text) )) - 1;
					if( !isset( $table[$datenum] ) ) { $table[$datenum] = 0; }
					$table[ $datenum ]++;
				}
				for( $i = 0; $i < 5; $i++ )
				{
					echo "daycounts.push( " . $table[$i] . " );";
				}
			?>
			
			$(document).ready( function() {
				var canvas = document.getElementById( "graph" );
				var ctx = canvas.getContext( "2d" );
				
				var playerCount = drawGraph( ctx, width, height, dates, start, end, <?php echo $max; ?>, -1 );
				for( var i = 0; i < 5; i++ )
				{
					document.getElementById("day" + i).innerHTML = daycounts[i];
				}
				document.getElementById("count").innerHTML = playerCount;
				
				$("#graph").mousemove( function(e) {
					drawGraph( ctx, width, height, dates, start, end, <?php echo $max; ?>, e.pageX - $("#graph").offset().left );
				});
				$("#graph").mouseleave( function(e) {
					drawGraph( ctx, width, height, dates, start, end, <?php echo $max; ?>, -1 );
				});
			});
		</script>
	</head>
<body>
	<h1><?php if( $type == 1 ) { echo "Registration "; } else { echo "Tag "; }?>Graph</h1>
	Start Date: <?php echo $start_date; ?><br/>
	End Date: <?php echo $end_date; ?><br/>
	<table style="text-align: center; vertical-align: center;">
		<tr><td>Players<br/>(increments of 10)</td><td><canvas id="graph" width="<?php echo $width; ?>" height="<?php echo $height; ?>"></canvas></td></tr>
		<tr><td>&nbsp;</td><td>Time<br/>(increments of 2 hours)</td></tr>
	</table>
	<div>
	<h2 style="line-height:75%"><?php if( $type == 1 ) echo "Signups "; else { echo "Captures "; }?>By The Numbers</h2>
	<span style="font-size:50%"><a href="http://www.youtube.com/watch?v=fmRjgWW8yn0">(troubles by the score...)</a></span><br/>
	<table>
		<?php
			$days = array( "Monday", "Tuesday", "Wednesday", "Thursday", "Friday" );
			for( $i = 0; $i < 5; $i++ )
			{
				echo "<tr><td>" . $days[$i] . "</td><td id='day$i'></td></tr>";
			}
		?>
		<tr><td>Total</td><td id="count"></td></tr>
	</table>
	</div>
</body>
</html>
