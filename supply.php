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
        set_alert("SUCCESS", $query);
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
<h2>Redeem Supply Codes</h2>
<p>
    Use this form to redeem supply codes. If you'd like to redeem more than one at a time, simply separate them by commas.
    If there are any errors you'll be told which supply codes failed to redeem. Each code is currently worth <?php echo get_supply_score(); ?> points.
</p>

<div style="margin-top: 10px;" class="row">
    <div class="col-md-4">
        <form method="post" action="">
            <div class="input-group">
                <input class="form-control" name="codes" placeholder="Supply Code(s)" required/>
                <span class="input-group-btn">
                    <input class="btn btn-default" type="submit" name="action" value="Redeem" />
                </span>
            </div>
        </form>
    </div>
</div>
<?php
page_foot();
?>