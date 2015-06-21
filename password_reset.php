<?php
require_once("require/rplayers.php");
require_once("require/mail.php");
require_once("require/basic.php");

if( isset($_REQUEST["action"]) )
{
	$action = $_REQUEST["action"];
	if( $action == "Reset Password" )
	{
		$email = trim($_REQUEST["email"]);
		$result = reset_password( $email );
		if( $result === FALSE ) 
		{
			reload_self("&E-mail address not found in database");
		}
		else 
		{
			hvzmailf($email, "reset", array("password" => $result));
			reload_self("Password reset, please check your inbox.");
		}
	}
}
page_unsecure_head();
write_response();
?>
<p>If you've forgotten or lost your password, please enter your e-mail address below. This will cause a new password to be emailed to you.</p>
<form method="post" action="">
E-Mail Address&nbsp;<input name="email"/><br/>
<input type="submit" name="action" value="Reset Password" />
</form>
<a href="login.php">Back to Login Screen</a>
<?php
page_unsecure_foot();
?>