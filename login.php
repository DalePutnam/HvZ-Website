<?php session_start(); ?>
<?php
require_once("require/rgame.php");
require_once("require/basic.php");
?>
<?php if( isset( $_GET["logout"] ) )
{
	unset( $_SESSION["user"] );
	unset($_SESSION["impersonate"]);
}
?>
<?php if( isset( $_SESSION["user"] ) ) { 
	if( isset( $_GET["reason"] ) )
		header("Location: panel.php?response=" . $_GET["reason"]); 
	else header("Location: panel.php"); 
	exit(); 
} ?>
<?php
page_unsecure_head();
 
if( is_maintenance( ) ) { echo "<strong style='color:red;'>Website is in maintenance mode, please come back later</strong>"; }
?>
<form action="panel.php?reload=1" method="post">
	E-Mail:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="user" /><br/>
	Password:&nbsp;<input name="pass" type="password" /><br/>
	<input type="submit" value="Login" />
</form>
<a href="mods.php">Contact Moderators</a><br/>
<a href="documents/rules.pdf">Game Rules</a><br/>
<a href="graphs.php">Game Graphs</a><br/>
<a href="password_reset.php">Forgot/Reset Password</a><br/>

<?php
page_unsecure_foot();
?>