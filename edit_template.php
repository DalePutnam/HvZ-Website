<?php
session_start();
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/basic.php");
require_once("require/rstun.php");
require_once("require/config.php");
Secure( true );
global $templates_dir;

if( isset($_REQUEST["action"]) )
{
	$action = $_REQUEST["action"];
	if( $action == "Save" )
	{
		$name = $_REQUEST["file"];
		$content = $_REQUEST["content"];
		echo $content;
		
		//if( !ctype_alnum( $name ) ) reload_self("&Invalid filename.");
		
		$filename = $name . ".txt";
		
		$result = file_put_contents($templates_dir . $filename, $content);
		if( $result === FALSE ) set_alert("ERROR", "Failed to write to file $templates_dir$filename.");
		set_alert("SUCCESS", "File saved.");
	}
}
page_head();
?>
<h2>E-Mail Templates</h2>
<?php
if( isset($_GET["edit"]) )
{
	$name = $_GET["edit"];
	echo "<h3>Editing $name</h3>";
	echo "<p>The first line will be the subject, the rest is the body.</p>";
	$filename = $name . ".txt";
	$contents = file_get_contents( $templates_dir . $filename );
	?>
    <div class="row">
        <div class="col-md-6">
            <form method="post" action="">
                <input type="hidden" name="file" value="<?php echo $name; ?>" />
                <div class="form-group">
                    <textarea class="form-control" name="content" rows="10" cols="30"><?php echo $contents;?></textarea>
                </div>
                <input class="btn btn-default" type="submit" name="action" value="Save" />
            </form>
        </div>
    </div>
	<?php
}
?>
<h3>Template List</h3>
<p>Click on the template you wish to edit</p>
<?php

$handle = opendir($templates_dir);
if( $handle == FALSE )
{
	echo "<span class='red'>Failed to open templates directory for reading.</span>";
}
else
{
	while( false !== ($entry = readdir($handle)) )
	{
		$pos = stripos($entry, ".txt");
		if( $pos === FALSE ) continue;
		$name = substr($entry, 0, $pos);
		echo "<a href='?edit=" . urlencode($name) . "'>$name</a><br/>";
	}
	closedir($handle);
}
?>
<?php
page_foot();
?>