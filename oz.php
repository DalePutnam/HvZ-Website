<?php session_start(); ?>
<?php 
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/rgame.php");

// Admin only page
Secure(true);

if( isset($_REQUEST["action"]) && !is_game_started())
{
	if( $_REQUEST["action"] == "Save OZ List" )
	{
		
		$ids = array();
		if( isset($_REQUEST["ids"] ) ) { $ids = $_REQUEST["ids"]; }
		set_oz_list( $ids );
        set_alert("SUCCESS", "OZ list saved.");
	}
	if( $_REQUEST["action"] == "Delete" )
	{
		$id = $_REQUEST["id"];
		remove_from_pool($id);
        set_alert("SUCCESS", "Player removed from OZ Pool.");
	}
}

$players = get_players();
$oz_list = get_oz_list();
$oz_pool = get_oz_pool();

$player_ids = get_ids($players);

page_head();
if( is_game_started() ) echo "<strong>Game has been started, OZ tables are now read-only</strong>";
?>
<table style="width: 100%;">
	<tr>
		<th>OZ Pool (<span id="pn"></span>)</th><?php if( !is_game_started() ) { ?><th>All Players (<span id="an"></span>)</th><?php } ?><th>OZ List (<span id="ln"></span>)</th>
	</tr>
	<tr>
		<td id="pool" style="vertical-align:top;">
		<?php if( !is_game_started() ) { ?>
		<div>Add&nbsp;<input id="rand" style="width:40px;" value="0"/>&nbsp;random OZs <button id="go">Go</button></div>
		<?php } ?>
		</td>
		<?php if( !is_game_started() ) { ?>
		<td id="all" style="vertical-align:top;">
		</td>
		<?php } ?>
		<td id="list" style="vertical-align:top;">
		<form method="post" action="" id="save">
			<?php
				foreach($oz_list as $id)
				{
					?>
					<input type="hidden" name="ids[]" value="<?php echo $id; ?>"/>
					<?php
				}
			?>
			<input type="submit" name="action" value="Save OZ List" id="save_button" style="display:none;" />
		</form>
		<?php if( !is_game_started() ) { ?>
		<div><button id="clear">Clear</button></div>
		<?php } ?>
		</td>
	</tr>
</table>
<script type="text/javascript">
	var changes = false;
	var started = <?php if(is_game_started()) { echo "true"; } else { echo "false"; } ?>;
	var ozpool = <?php echo json_encode($oz_pool); ?>;
	var ozlist = <?php echo json_encode($oz_list); ?>;
	var players = <?php echo json_encode($player_ids); ?>;
	ozpool.sort(function(a,b) { return a - b; });
	ozlist.sort(function(a,b) { return a - b; }); 
	if( !started )
		players.sort(function(a,b) { return a - b; });
	var playersSkimmed = []; var ozSkimmed = [];
	var i = 0; var j = 0;
	for( var k = 0; k < ozlist.length; k++ )
	{
		if( !started )
		{
			while( players[i] <= ozlist[k] )
			{
				if( players[i] != ozlist[k] )
				{
					playersSkimmed.push( players[i] );
				}
				i++;
			}
		}
		while( ozpool[j] <= ozlist[k] )
		{
			if( ozpool[j] != ozlist[k] )
			{
				ozSkimmed.push( ozpool[j] );
			}
			j++;
		}
	}
	if( !started )
		playersSkimmed = playersSkimmed.concat( players.slice(i) );
		
	ozSkimmed = ozSkimmed.concat( ozpool.slice(j) );
	function UpdateCounts()
	{
		$("span#pn").html(poolTable.count());
		if( !started )
			$("span#an").html(allTable.count());
		$("span#ln").html(listTable.count());
	}
	function OnChange()
	{
		if( !changes )
		{
			$("input#save_button").show();
			window.onbeforeunload = function()
			{
				return "Unsaved changes have been made to the OZ List, are you sure you want to leave?";
			};
			changes = true;
		}
		UpdateCounts();
	}
	function Saved()
	{
		window.onbeforeunload = null;
	}
	$("input#save_button").click(function() { Saved(); });
	function AddToList(id)
	{
		if( listTable.addPlayer(id) )
		{
			$("<input type='hidden' name='ids[]'>").val(id).appendTo($("#save"));
			poolTable.removePlayer(id);
			allTable.removePlayer(id);
			OnChange();
		}
	}
	function RemoveFromList(id)
	{
		if( listTable.removePlayer(id) )
		{
			$("input[name='ids[]'][value=" + id + "]").remove();
			if( $.inArray( id, ozpool ) > -1 )
			{
				poolTable.addPlayer( id );
			}
			allTable.addPlayer( id );
			OnChange();
		}
	}
	function ClearList()
	{
		var x = listTable.getPlayerIDs();
		if( x.length == 0 ) return;
		for( var i = 0; i < x.length; i++ )
		{
			if( $.inArray( x[i], ozpool ) > -1 )
			{
				poolTable.addPlayer( x[i] );
			}
			allTable.addPlayer( x[i] );
		}
		listTable.clear();
		OnChange();
	}
	
	// load all player data
	LoadPlayerData(<?php echo json_encode( $players ); ?>);
	
	var add_all = {};
	var add_pool = {};
	var remove = {};
	if( !started )
	{
		add_pool = { "Add":{"type":"js", "func":AddToList}, "Delete":{"type":"post"} };
		add_all = { "Add":{"type":"js", "func":AddToList} };
		remove = { "Remove":{"type":"js", "func":RemoveFromList} };
	}
	
	// pool table
	var poolTable = new PlayerTable( $("#pool"), namePlayerProperties, { }, add_pool );
	poolTable.addPlayers( ozSkimmed );
	
	// all table
	var allTable = null;
	if( !started )
	{
		allTable = new PlayerTable( $("#all"), namePlayerProperties, { }, add_all );
		allTable.addPlayers( playersSkimmed );
	}
	
	// list table
	var listTable = new PlayerTable( $("#list"), namePlayerProperties, { }, remove );
	listTable.addPlayers( ozlist );
	
	UpdateCounts();
	
	// random OZ adding
	$("button#go").click( function() {
		var p = poolTable.getPlayerIDs();
		var r = [];
		var num = parseInt($("input#rand").val());
		if( isNaN( num ) ) {
			alert( "Not a number!");
			return;
		}
		if( num > p.length ) {
			alert("Not enough players in the pool!");
			return;
		}
		for( var a = 0; a < num; a++ )
		{
			var x = Math.floor( Math.random()*p.length );
			r.push(p[x]);
			p.splice(x,1);
		}
		for( var a = 0; a < num; a++ )
		{
			AddToList( r[a] );
		}
	});
	
	$("button#clear").click( ClearList );
</script>
<?php
page_foot();
?>