<?php
session_start();
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/basic.php");
require_once("require/rsupply.php");
require_once("require/rgame.php");
Secure( false, 'HUMAN' );
if( !is_game_started() ) { to_panel("&Can not redeem supply before game starts."); }

if( isset($_REQUEST["action"]) )
{
	$action = $_REQUEST["action"];
	if( $action == "Redeem" )
	{
		$codestr = trim($_REQUEST["codes"]);
		
		$codes = explode(",", $codestr);
		$codesworked = array();
		$codesfailed = array();
		foreach( $codes as $code )
		{
			if( use_supply( ID(), trim($code) ) === true )
			{
				array_push($codesworked, $code);
			}
			else
			{
				array_push($codesfailed, $code);
			}
		}
		$query = array();
		if( count($codesworked) > 0 ) $query["worked"] = implode(",", $codesworked);
		if( count($codesfailed) > 0 ) $query["failed"] = implode(",", $codesfailed);
		reload_self($query);
	}
}

page_head();

if( isset($_REQUEST["worked"]) )
{
	$worked = explode(",", $_REQUEST["worked"]);
	echo "<span class='green'>The following supply codes were redeemed successfully:</span><br/>";
	foreach($worked as $work) { echo "$work<br/>"; }
}
if( isset($_REQUEST["failed"]) )
{
	$failed = explode(",", $_REQUEST["failed"]);
	echo "<span class='red'>The following supply codes failed to redeem:</span><br/>";
	foreach($failed as $fail) { echo "$fail<br/>"; }
}
?>
<h1>Redeem Supply Codes</h1>
<p>Use this form to redeem supply codes. If you'd like to redeem more than one at a time, simply separate them by commas. If there are any errors you'll be told which supply codes failed to redeem. Each code is currently worth <?php echo get_supply_score(); ?> points.</p>
<form method="post" action="">
Supply Code(s):&nbsp;<input name="codes" /><br/>
<input type="submit" name="action" value="Redeem" />
</form>
<?php
page_foot();
?>