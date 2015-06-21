// table configurations
var namePlayerProperties = {
						"first_name": 		{ "display":"First Name", "type":"text"}, 
						"last_name": 		{ "display":"Last Name", "type":"text"}, 
					};
var basicPlayerProperties = { 
						"first_name": 		{ "display":"First Name", "type":"text"}, 
						"last_name": 		{ "display":"Last Name", "type":"text"}, 
						"email":			{ "display":"E-Mail", "type":"text"},
						"score":			{ "display":"Score", "type":"text"},
					};
var basicTeamPlayerProperties = { 
						"first_name": 		{ "display":"First Name", "type":"text"}, 
						"last_name": 		{ "display":"Last Name", "type":"text"}, 
						"email":			{ "display":"E-Mail", "type":"text"},
						"type":				{ "display":"Type", "type":"text"},
					};
var fullPlayerProperties = {
						"id": 				{ "display":"ID", "type":"text"}, 
						"first_name": 		{ "display":"First Name", "type":"text"}, 
						"last_name": 		{ "display":"Last Name", "type":"text"}, 
						"email":			{ "display":"E-Mail", "type":"text"},
						"code":				{ "display":"Code", "type":"text"},
						"type":				{ "display":"Type", "type":"text"},
						"score":			{ "display":"Score", "type":"text"},
					};

// Format of the produced table
// <table>
//	<tr>(<th>[Attr]</th>)*<th>Actions</th></tr>
//	(<tr>(<td>[Val]</td>)*<td>[Action Form]</td></tr>
// </table>

// contains our data on every player in the game
var globalPlayerData = {};
function LoadPlayerData( players )
{
	globalPlayerData = players;
}

function PlayerTable( parent, props, enums, actions )
{
	this.playerProperties = props;
	this.playerActions = actions;
	this.typeEnums = enums;
	this.table = $("<table>").appendTo( parent ).addClass("players");
	this.players = [];
	
	var header = $("<tr>").appendTo( this.table );
	this.eachPlayerProperty( function( name, display ) {
		$("<th>").appendTo(header).text(display);
	} );
}

PlayerTable.prototype.eachPlayerProperty = function(callback) {
	$.each( this.playerProperties, function(name, data) { callback(name, data.display, data.type); });
}

PlayerTable.prototype.eachActionType = function(callback) {
	$.each( this.playerActions, function(name, data) { callback(name, data.type, data.func); });
}

PlayerTable.prototype.setPlayerData = function( entry, player ) {
	var thisTable = this;
	this.eachPlayerProperty( function(name, display, type) {
		var row = $("td." + name, entry);
		$(".text", row).text( thisTable.translateToString( player[name], type ) );
		$(".val", row).val( player[name] );
	});
}

PlayerTable.prototype.addPlayer = function( playerID, bulk ) {
	if( bulk == null || bulk == undefined ) bulk = false;
	if( this.getPlayerRow(playerID).size() != 0 ) return false;
	
	var entry = $("<tr>").appendTo(this.table).addClass("player");
	var thisTable = this;
	
	this.eachPlayerProperty( function( name, display, type ) {
		var row = $("<td>").appendTo(entry).addClass(name);
		$("<span>").addClass("text").appendTo(row);
		$("<input type='hidden'>").addClass("val").appendTo(row);
	});
	
	var actionRow = $("<td>").appendTo(entry).addClass("actions");
	var form = $("<form method='post' action=''>").appendTo(actionRow);
	this.eachActionType( function( name, type, func ) {
		if( type == "js" )
		{
			$("<button>").html(name).click(function() { func(playerID); }).appendTo(actionRow);
		}
		else if( type == "post" )
		{
			$("<input type='submit' name='action'>").val(name).appendTo(form);
		}
	});
	$("<input type='hidden' name='id'>").val(playerID).appendTo(form);
	
	this.setPlayerData( entry, globalPlayerData[playerID] );
	this.players.push( playerID );
	if( !bulk ) this.players.sort(function(a,b) { return a - b; });
	return true;
}

PlayerTable.prototype.addPlayers = function( players ) {
	for( var i = 0; i < players.length; i++ ) {
		this.addPlayer( players[i], true );
	}
	
	// bulk mode disables sorting per-insert, so sort after the bulk is finished
	this.players.sort(function(a,b) { return a - b; });
}

PlayerTable.prototype.getPlayerIDs = function() {
	return this.players.slice(0);
}

PlayerTable.prototype.hasPlayer = function( playerID ) {
	for( var i = 0; i < this.players.length; i++ ) {
		if( this.players[i] == playerID ) return true;
		if( this.players[i] < playerID ) return false;
	}
	return false;
}

PlayerTable.prototype.removePlayer = function( playerID ) {
	var row = this.getPlayerRow(playerID);
	if( row.size() == 0 ) return false;
	row.remove();
	
	// remove from array
	var index = $.inArray( playerID, this.players );
	if( index != -1 )
	{
		this.players.splice( index, 1 );
	}
	return true;
}
PlayerTable.prototype.clear = function () {
	$("tr.player", this.table).remove();
	this.players = [];
};
PlayerTable.prototype.count = function() {
	return this.players.length;
};
PlayerTable.prototype.getPlayerRow = function( playerID ) {
	return $("input[name=id][value=" + playerID + "]", this.table).parent().parent().parent();
}

PlayerTable.prototype.translateToString = function( data, type ) {
	if( type == "text" ) { 
		if( data == null ) {
			return "-NULL-";
		}
		else return data; 
	}
	else if( type == "bool" ) { 
		if( data == 0 ) return "False";
		else return "True";
	} else if( type in this.typeEnums ) {
		return this.typeEnums[type][data];
	}
	else { return data; }
}
