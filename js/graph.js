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
	ctx.globalAlpha = 1;
}

function drawData( color, ctx, pixelPerTime, pixelPerPlayer, width, height, sortedDateObjects, startTime, endTime, nowTime, mouse )
{
	ctx.beginPath();
	ctx.strokeStyle = color;
	ctx.moveTo(0,height);
	var playerCount = 0;
	
	var mouseCount = -1;
	var mouseTime = -1;
	if( mouse != -1 )
	{
		mouseTime = mouse / pixelPerTime + startTime;
	}
	if( nowTime > endTime ) nowTime = endTime;
	if( nowTime < startTime ) nowTime = startTime;
	
	for( var i = 0; i < sortedDateObjects.length; i++ ) {
		var time = sortedDateObjects[i];
		if( time < startTime || time > endTime ) continue;
		
		if( mouse != -1 && mouseCount == -1 && time > mouseTime )
		{
			mouseCount = playerCount;
		}
		
		ctx.lineTo( pixelPerTime * (time - startTime), height - pixelPerPlayer * playerCount );
		playerCount ++;
		ctx.lineTo( pixelPerTime * (time - startTime), height - pixelPerPlayer * playerCount );
	}
	if( mouse != -1 && mouseCount == -1 && mouseTime <= nowTime )
	{
		mouseCount = playerCount;
	}
	ctx.lineTo( pixelPerTime * (nowTime - startTime), height - pixelPerPlayer * playerCount );
	ctx.stroke();
	
	if( mouse != -1 && mouseCount != -1 )
	{
		var mouseY = height - pixelPerPlayer * mouseCount
		ctx.beginPath();
		ctx.arc( mouse, mouseY, 5, 0, Math.PI * 2, false );
		ctx.fillStyle = color;
		ctx.fill();
		ctx.stroke();
		
		ctx.font = "12px Arial";
		ctx.fillText( "Players: " + mouseCount, mouse - 20, mouseY - 25 );
		ctx.fillText( "Hours: " + Math.floor((mouseTime - startTime)/1000/60/60), mouse - 20, mouseY - 10 );
	}
}

function drawGraph( ctx, width, height, sortedDateObjectList, colorList, startTimes, endTimes, maxPlayers, nowTime, mouse ) {
	var range = 0;
	for( var i = 0; i < sortedDateObjectList.length; i++ )
	{
		var nrange = endTimes[i] - startTimes[i];
		if( nrange > range) range = nrange;
	}
	var pixelPerTime = width / range;
	var pixelPerPlayer = height / maxPlayers;
	
	ctx.clearRect( 0, 0, width, height );
	drawGrid( ctx, width, height, (4 * 60 * 60 * 1000 * pixelPerTime), (20 * pixelPerPlayer), 6, 5, 4, /*10*/20 );
	
	for( var i = 0; i < sortedDateObjectList.length; i++ )
	{
		drawData( colorList[i], ctx, pixelPerTime, pixelPerPlayer, width, height, sortedDateObjectList[i], startTimes[i], endTimes[i], nowTime, mouse );
	}
}

function createDateGraph( canvas, starts, ends, width, height, dates, colors, max, nowTime )
{
	var ctx = $(canvas).get(0).getContext( "2d" );
	
	drawGraph( ctx, width, height, dates, colors, starts, ends, max, nowTime, -1 );
	
	$(canvas).mousemove( function(e) {
		drawGraph( ctx, width, height, dates, colors, starts, ends, max, nowTime, e.pageX - $(canvas).offset().left );
	});
	$(canvas).mouseleave( function(e) {
		drawGraph( ctx, width, height, dates, colors, starts, ends, max, nowTime, -1 );
	});
}
