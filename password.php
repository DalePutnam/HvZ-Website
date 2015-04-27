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
<div class="row">
    <div class="col-md-4">
        <h1>Reset Your Password</h1>
        <p>Warning: This process will log you out</p>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <form class="form-horizontal" method="post" action="">
            <div class="form-group">
                <div class="col-md-12"><input class="form-control" placeholder="Old Password" type="password" name="old" /></div>
            </div>
            <div class="form-group">
                <div class="col-md-12"><input class="form-control" placeholder="New Password" type="password" name="new" /></div>
            </div>
            <div class="form-group">
                <div class="col-md-12"><input class="form-control" placeholder="Confirm Password" type="password" name="confirm" /></div>
            </div>
            <input class="btn btn-default" type="submit" name="action" value="Change Password" />
        </form>
    </div>
</div>
<?php
page_foot();
?>