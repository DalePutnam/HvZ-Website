<?php session_start();
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/forms.php");
Secure(false);

if( isset( $_REQUEST["action"] ) )
{
	$action = $_REQUEST["action"];
	if($action == "Change Password")
	{
		$old = md5(trim($_REQUEST["old"]));
		$new = md5(trim($_REQUEST["new"]));
		$confirm = md5(trim($_REQUEST["confirm"]));
		
		if( $new != $confirm ) reload_self("&Entered passwords don't match");
		if( PasswordCrypt() != $old ) reload_self("&Old password does not match current");
		else
		{
			change_password(ID(), trim($_REQUEST["new"]));
			header("Location: login.php?logout=&reason=Password reset, please log back in");
			exit();
		}
	}
}

page_head();
?>
<h1>Reset Your Password</h1>
<p>Warning: This process will log you out</p>
<form method="post" action="">
Old Password:&nbsp;<input type="password" name="old" /><br/>
New Password:&nbsp;<input type="password" name="new" /><br/>
Confirm Password:&nbsp;<input type="password" name="confirm" /><br/>
<input type="submit" name="action" value="Change Password" />
</form>
<?php
page_foot();
?>