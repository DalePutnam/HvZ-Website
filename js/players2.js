// contains our data on every player in the game
var globalPlayerData2 = {};

var modalDialog =
    '<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
        '<div class="modal-dialog modal-sm">' +
            '<div class="modal-content">' +
                '<div class="modal-body">' +
                    '<p></p>' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-primary">Confirm</button>' +
                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';

function LoadPlayerData2( players )
{
	globalPlayerData2 = players;
}

function PlayerTable2( props, enums, actions )
{
	this.playerProperties = props;
	this.playerActions = actions;
	this.typeEnums = enums;
	this.players = [];
}

PlayerTable2.prototype.getPlayerObj = function( playerID )
{
	return globalPlayerData2[playerID];
}

PlayerTable2.prototype.build = function( parent, boxes ) {
	this.boxes = boxes;
	this.parent = $(parent);
	this.table = $("<table>").appendTo( parent ).addClass("players");
	var header = $("<tr>").appendTo( this.table );
	
	$("<th>").appendTo(header);
	this.eachPlayerProperty( function( name, display ) {
		$("<th>").appendTo(header).text(display);
	} );
	
	this.buildMenuSystem();
}

PlayerTable2.prototype.take = function( parent, boxes ) {
	this.boxes = boxes;
	this.parent = $(parent);
	this.table = $( "table", parent );
	
	this.players = $( "input[name='id']", this.table ).map( function() {
		return parseInt( $(this).val() );
	}).get().sort(function(a,b) { return a - b; });
	
	this.buildMenuSystem();
	
	var thisTable = this;
	
	if( !$.isEmptyObject( this.playerActions ) )
	{
		$("tr.player", this.table).on("contextmenu", function(event) {
			thisTable.onContext( this, event );
			return false;
		});
	}
	
	if( this.boxes )
	{
		$("input[name='id']", this.table).change( function() {
			thisTable.onChange(this);
		});
	}
}

PlayerTable2.prototype.onChange = function( input )
{
	if( $(input).is(":checked") ) $(input).parents("tr.player").first().addClass("over");
	else $(input).parents("tr.player").first().removeClass("over");
}

PlayerTable2.prototype.onContext = function( row, event )
{
	var thisTable = this;
	if( this.boxes )
	{
		$("input[name='id']", row).prop("checked", true);
		this.selectedId = [];
		$("input[name='id']:checked", this.table).each( function() {
			thisTable.selectedId.push( $(this).val() );
		});
	}
	else
	{
		this.selectedId = parseInt($("input[name='id']", row).val());
	}
	
	this.amenu.show().position({
		my: "left top",
		of: event
	});
	$( document ).one( "click", function() { 
		thisTable.amenu.hide();
		$("tr.over", thisTable.table).removeClass("over");
	});
	$(row).addClass("over");
}

PlayerTable2.prototype.buildMenuSystem = function() {
	// don't bother if there aren't any actions (it's faster this way)
	if( $.isEmptyObject( this.playerActions ) ) return;
	
	this.amenu = $("<ul>").appendTo(this.parent).addClass("context-menu");
	this.aform = $("<form method='post' action=''>").appendTo(this.parent);
	$("<input type='hidden' name='id' value='' />").addClass("hiddenid").appendTo(this.aform);
	$("<input type='hidden' name='action' value=''/>").addClass('hiddensubmit').appendTo(this.aform);
	var thisTable = this;
	this.eachActionType( function( name, type, func, conf ) {
		var li = $("<li>").appendTo(thisTable.amenu);
		var a = $("<a href='#'>").appendTo(li).html(name);
		
		if( type == "js" )
		{
			a.click( function() {
				if( thisTable.boxes )
				{
					$("input[name='id']", thisTable.table).prop("checked", false);
				}
				func(thisTable.selectedId);
			});
		}
		else if( type == "post" )
		{
			a.click( function() {
				$(".hiddenid", thisTable.aform).val( thisTable.selectedId );
				$(".hiddensubmit", thisTable.aform).val( name );
				
				if( !conf ) {
					thisTable.aform.submit();
				}
				else {
					var pl = "";
					if( $.isArray( thisTable.selectedId ) )
					{
						$(thisTable.selectedId).each( function() {
							pl += this["first_name"] + " " + this["last_name"] + ", ";
						});
					}
					else
					{
						var obj = thisTable.getPlayerObj( thisTable.selectedId );
						pl = obj["first_name"] + " " + obj["last_name"];
					}
					thisTable.getPlayerObj( thisTable.selectedId )
					$("p", thisTable.aconfirm).text("Are you sure you'd like to perform a '" + name + "' on " + pl + "?");
					//thisTable.aconfirm.dialog( "open" );
                    thisTable.aconfirm.modal("show");
				}
				return false;
			});
		}
	});
	
	/*this.aconfirm = $("<div title='Confirm Action'>")
		.appendTo( thisTable.parent )
		.append( $("<p>") )
		.dialog( {
			autoOpen: false,
			modal: true,
			position: {my:"top", at:"top", of:window},
			buttons: {
				Confirm: ,
				Cancel : function( ) {
					$(this).dialog("close");
				}
			}
		});*/

        this.aconfirm = $(modalDialog).appendTo(thisTable.parent);
        $('button', this.aconfirm).click(function( ) {
            thisTable.aform.submit();
        });


	
	// Hide all UI elements
	this.amenu.menu().hide();
	this.aform.hide();
	
	// Select All/None buttons
	if( this.boxes )
	{
		var p = $("<p>").prependTo( this.parent ).append( $("<button>").html("Select All").click( function() { 
			// Select All
			$("input[name='id']", thisTable.table).prop("checked", true).parents("tr.player").addClass("over");
		} ) ).append( $("<button>").html("Select None").click( function() {
			// Select None
			$("input[name='id']", thisTable.table).prop("checked", false).parents("tr.player").removeClass("over");
		} ) );
	}
}

PlayerTable2.prototype.eachPlayerProperty = function(callback) {
	$.each( this.playerProperties, function(name, data) { callback(name, data.display, data.type); });
}

PlayerTable2.prototype.eachActionType = function(callback) {
	$.each( this.playerActions, function(name, data) { 
		var confirm = false;
		if( "confirm" in data )
		{
			confirm = data.confirm;
		}		
		callback(name, data.type, data.func, confirm); 
	});
}

PlayerTable2.prototype.setPlayerData = function( entry, player ) {
	var thisTable = this;
	this.eachPlayerProperty( function(name, display, type) {
		var row = $("td." + name, entry).text(thisTable.translateToString( player[name], type ));
	});
}

PlayerTable2.prototype.addPlayer = function( playerID, bulk ) {
	if( bulk == null || bulk == undefined ) bulk = false;
	if( this.getPlayerRow(playerID).size() != 0 ) return false;
	
	var entry = $("<tr>").appendTo(this.table).addClass("player");
	var thisTable = this;
	
	var idRow = $("<td style='display:none;'>").appendTo(entry);
	if( this.boxes )
	{
		idRow.append($("<input name='id' type='checkbox'>").val(playerID));
		$("input", idRow).change( function() {
			thisTable.onChange(this);
		});
	}
	else
	{
		idRow.append($("<input name='id' type='hidden'>").val(playerID));
	}
	
	this.eachPlayerProperty( function( name, display, type ) {
		var row = $("<td>").appendTo(entry).addClass(name);
	});
	
	this.setPlayerData( entry, globalPlayerData2[playerID] );
	this.players.push( playerID );
	
	if( !$.isEmptyObject( this.playerActions ) )
	{
		entry.on("contextmenu", function(event) {
			thisTable.onContext( this, event );
			return false;
		});
	}
	if( !bulk ) this.players.sort(function(a,b) { return a - b; });
	return true;
}

PlayerTable2.prototype.addPlayers = function( players ) {
	for( var i = 0; i < players.length; i++ ) {
		this.addPlayer( players[i], true );
	}
	
	// bulk mode disables sorting per-insert, so sort after the bulk is finished
	this.players.sort(function(a,b) { return a - b; });
}

PlayerTable2.prototype.getPlayerIDs = function() {
	return this.players.slice(0);
}

PlayerTable2.prototype.hasPlayer = function( playerID ) {
	for( var i = 0; i < this.players.length; i++ ) {
		if( this.players[i] == playerID ) return true;
		if( this.players[i] < playerID ) return false;
	}
	return false;
}

PlayerTable2.prototype.removePlayer = function( playerID ) {
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
PlayerTable2.prototype.clear = function () {
	$("tr.player", this.table).remove();
	this.players = [];
};
PlayerTable2.prototype.count = function() {
	return this.players.length;
};
PlayerTable2.prototype.getPlayerRow = function( playerID ) {
	return $("input[name=id][value=" + playerID + "]", this.table).parents("tr.player").first();
}

PlayerTable2.prototype.translateToString = function( data, type ) {
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

