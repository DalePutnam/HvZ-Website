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
		reload_self("OZ list saved.");
	}
	if( $_REQUEST["action"] == "Remove from Pool" )
	{
		$id = $_REQUEST["id"];
		remove_from_pool($id);
		reload_self("Player removed from OZ Pool.");
	}
}

$players = get_players();
$oz_list = get_oz_list();
$oz_pool = get_oz_pool();

$player_ids = get_ids($players);

$pool_initial = array_diff( $oz_pool, $oz_list );
$all_initial = array_diff( $player_ids, $oz_list );

page_head();?>
<div class="row">
<div class="col-md-12">

<?php if( is_game_started() ) echo "<strong>Game has been started, OZ tables are now read-only</strong>";?>

<div class="row">
    <div class="col-md-4">
        <h4>OZ Pool (<span id="pn"></span>)</h4>
    </div>
<?php if ( !is_game_started() ) { ?>
    <div class="col-md-4">
        <h4>All Players (<span id="an"></span>)</h4>
    </div>
<?php } ?>
    <div class="col-md-4">
        <h4>OZ List (<span id="ln"></span>)</h4>
    </div>
</div>
<?php if( !is_game_started() ) { ?>
<div class="row">
    <div class="col-md-4">
        <form class="form-inline">
            <div class="form-group">
                <label>Add <input style="width: 40px" class="form-control" id="rand" value="0"/> random OZs</label>
                <input type="button" class="btn btn-default" id="go" value="Go" />
            </div>
        </form>
    </div>
    <?php if( !is_game_started() ) { ?>
        <div class="col-md-4 col-md-offset-4">

            <form class="form-inline" method="post" action="" id="save">
                <input type="button" class="btn btn-default" id="clear" value="Clear"/>
                <?php
                foreach($oz_list as $id)
                {
                    ?>
                    <input type="hidden" name="ids[]" value="<?php echo $id; ?>"/>
                <?php
                }
                ?>
                <input class="form-control" type="submit" name="action" value="Save OZ List" id="save_button" style="display:none;" />
            </form>
        </div>
    <?php } ?>
</div>
<?php } ?>

<div class="row">
    <div id="pool" class="col-md-4">
        <?php gen_player_table( $players, $namePlayerProperties, $pool_initial, false ); ?>
    </div>
<?php if( !is_game_started() ) { ?>
    <div id="all" class="col-md-4">
        <?php gen_player_table( $players, $namePlayerProperties, $all_initial, false ); ?>
    </div>
<?php } ?>
    <div id="list" class="col-md-4">
        <?php gen_player_table( $players, $namePlayerProperties, $oz_list, false ); ?>
    </div>
</div>

<!--<table style="width: 100%;">
	<tr>
		<th>OZ Pool (<span id="pn"></span>)</th><?php if( !is_game_started() ) { ?><th>All Players (<span id="an"></span>)</th><?php } ?><th>OZ List (<span id="ln"></span>)</th>
	</tr>
	<tr>
		<td id="pool" style="vertical-align:top;">
		<?php if( !is_game_started() ) { ?>
		<div>Add&nbsp;<input id="rand" style="width:40px;" value="0"/>&nbsp;random OZs <button id="go">Go</button></div>
		<?php } ?>
		<?php gen_player_table( $players, $namePlayerProperties, $pool_initial, false ); ?>
		</td>
        <?php if( !is_game_started() ) { ?>
		<td id="all" style="vertical-align:top;">
		<?php gen_player_table( $players, $namePlayerProperties, $all_initial, false ); ?>
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
		<?php gen_player_table( $players, $namePlayerProperties, $oz_list, false ); ?>
		</td>
	</tr>
</table>-->
</div>
</div>
<script type="text/javascript">
	var changes = false;
	var started = <?php if(is_game_started()) { echo "true"; } else { echo "false"; } ?>;
	var ozpool = <?php echo json_encode($oz_pool); ?>;
	var ozlist = <?php echo json_encode($oz_list); ?>;
	var players = <?php echo json_encode($player_ids); ?>;
	
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
	LoadPlayerData2(<?php echo json_encode( $players ); ?>);
	
	var add_all = {};
	var add_pool = {};
	var remove = {};
	if( !started )
	{
		add_pool = { "Add":{"type":"js", "func":AddToList}, "Remove from Pool":{"type":"post", "confirm":true} };
		add_all = { "Add":{"type":"js", "func":AddToList} };
		remove = { "Remove":{"type":"js", "func":RemoveFromList} };
	}
	
	// pool table
	var poolTable = new PlayerTable2( namePlayerProperties, { }, add_pool );
	poolTable.take( $("#pool"), false );
	
	// all table
	var allTable = null;
	if( !started )
	{
		allTable = new PlayerTable2( namePlayerProperties, { }, add_all );
		allTable.take( $("#all"), false );
	}
	
	// list table
	var listTable = new PlayerTable2( namePlayerProperties, { }, remove );
	listTable.take( $("#list"), false );
	
	UpdateCounts();
	
	// random OZ adding
    $("input#go").click( function() {
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

    $("input#clear").click( ClearList );
</script>

<?php
page_foot();
?>