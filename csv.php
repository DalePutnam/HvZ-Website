<?php session_start(); ?>
<?php 
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/rgame.php");
require_once("require/rstun.php");

// Admin only page
Secure(true);

if( isset( $_REQUEST["action"] ) )
{
	$action = $_REQUEST["action"];
	if( $action == "Process" )
	{
		if ($_FILES["file"]["error"] <= 0 )
		{
			$data = file_get_contents( $_FILES["file"]["tmp_name"] );
			process_csv( $data );
            set_alert("SUCCESS", "Succesfully processed CSV.");
		}
		else
		{
			set_alert("ERROR", "Failed to upload file.");
		}
	}
	elseif( $action == "Process Stuns" )
	{
		if ($_FILES["file"]["error"] <= 0 )
		{
			$data = file_get_contents( $_FILES["file"]["tmp_name"] );
			$result = process_stun_csv( $data );
			if( $result ) set_alert("SUCCESS", "Succesfully processed CSV.");
			else set_alert("ERROR", "Failed to process CSV.");
		}
		else
		{
            set_alert("ERROR", "Failed to upload file.");
		}
	}
	elseif( $action == "Generate" )
	{
		$n = intval(trim($_REQUEST["n"]));
		if( $n == 0 ) set_alert("ERROR", "Please enter an integer number of people.");
		$oz = intval(trim($_REQUEST["oz"]));
		$type = trim($_REQUEST["type"]);
		if( $type != "HUMAN" && $type != "ZOMBIE" && $type != "ADMIN" && $type != "BANNED" && $type != "NONE" ) set_alert("ERROR", "$type is an invalid type.");
		
		// mark as CSV
		header('Content-type: text/csv');
		header('Content-disposition: attachment;filename=players.csv');
		
		for( $i = 0; $i < $n; $i++ )
		{
			$first = generateRandomString(6);
			$last = generateRandomString(8);
			$email = $first . "." . $last . "@test.ca";
			$isoz = "false";
			$x = mt_rand(0,100);
			if( $x < $oz ) $isoz = "true";
			echo $first . "," . $last . "," . $email . "," . $type . "," . $isoz;
			if( $i != $n-1 ) echo "\n";
		}
		exit();
	}
	elseif( $action == "Generate Stuns" )
	{
		$n = intval(trim($_REQUEST["n"]));
		if( $n == 0 ) set_alert("ERROR", "Please enter an integer number of people");
		
		// mark as CSV
		header('Content-type: text/csv');
		header('Content-disposition: attachment;filename=stuns.csv');
		
		$start = php_game_start();
		$end = php_game_end();
		
		$ids = get_ids_sorted_by(null, true, true, array("HUMAN", "ZOMBIE"));
		$nz = count($ids["ZOMBIE"]);
		$nh = count($ids["HUMAN"]);
		
		for( $i = 0; $i < $n; $i++ )
		{
			$date = mt_rand($start, $end);
			$killer = $ids["HUMAN"][mt_rand(0, $nh-1)];
			$victim = $ids["ZOMBIE"][mt_rand(0, $nz-1)];
			echo $killer . "," . $victim . "," . sql_date($date) . ",Test";
			if( $i != $n-1 ) echo "\n";
		}
		exit();
	}
}

page_head();
?>
<h2>CSV Processing</h2>
<h3>Upload a Player CSV</h3>
<p>
    Use this form to upload a comma separated file containing player data. The file should be a series of lines of the format:
    <i>first name,last name,email,type,oz</i> where <i>oz</i> is <i>true</i> or <i>false</i>. The <i>type</i> field is one of <i>HUMAN,ZOMBIE,ADMIN,NONE,BANNED</i>.
    Uploaded users will have the password 123456.
</p>
<div class="row">
    <div class="col-md-4">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Filename</label>
                <input type="file" name="file" id="file">
            </div>
            <input class="btn btn-default" type="submit" name="action" value="Process">
        </form>
    </div>
</div>
<h3>Generate a Player CSV</h3>
<p>Use this form to generate random player data for test upload</p>
<div class="row">
    <div class="col-md-4">
        <form action="" method="post">
            <div class="form-group">
                <label>Number of players</label>
                <input class="form-control" name="n" value="500" />
            </div>
            <div class="form-group">
                <label>Chance of OZ Pool (Percent)</label>
                <input class="form-control" name="oz" value="10" />
            </div>
            <div class="form-group">
                <label>Player Type</label>
                <input class="form-control" name="type" value="NONE" />
            </div>
            <input class="btn btn-default" type="submit" name="action" value="Generate"/>
        </form>
    </div>
</div>
<h3>Generate a Stun Table CSV</h3>
<p>Use this form to generate test stun table data using existing players. It may not function correctly if the game has not yet been started.</p>
<div class="row">
    <div class="col-md-4">
        <form action="" method="post">
            <div class="form-group">
                <label>Number of Stuns</label>
                <input class="form-control" name="n" value="200" />
            </div>
            <input class="btn btn-default" type="submit" name="action" value="Generate Stuns" />
        </form>
    </div>
</div>
<h3>Load a Stun CSV</h3>
<p>
    Use this form to upload a comma separated file containing stun data. The file should be a series of lines of the format:
    <i>killer,victim,time,comment</i> where <i>killer</i> and <i>victim</i> are player IDs and <i>time</i> is a MySQL formatted datetime string.
</p>
<div class="row">
    <div class="col-md-4">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Filename:</label>
                <input type="file" name="file" id="file">
            </div>
            <input class="btn btn-default" type="submit" name="action" value="Process Stuns">
        </form>
    </div>
</div>
<?php
page_foot();
?>