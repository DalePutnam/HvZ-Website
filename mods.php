<?php
session_start();
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/rgame.php");

$securemode = !Unsecure();

if( $securemode )
{
	Secure(false);
	page_head();
}
else
{
	page_unsecure_head();
}
$admin_email = get_mail_email();
?>
<div class="row"><div class="col-md-12"><h4>The main moderator e-mail address is <?php echo "<a href='$admin_email'>$admin_email</a>"; ?>.</h4></div></div>
<div class="row"><div class="col-md-6">
<table class='table table-striped table-bordered table-condensed'>
<tr><th>Name</th><th>Email</th><th>Score</th></tr>
<?php
$admins = get_players("ADMIN");
foreach($admins as $admin)
{
	echo "<tr><td>{$admin['first_name']} {$admin['last_name']}</td><td><a href='mailto:{$admin['email']}'>{$admin['email']}</a></td><td>{$admin['score']}</td></tr>";
}
?>
</table>
</div>
</div>
<?php
if( $securemode ) page_foot();
else
{
	echo "<a href='login.php'>Back to Login Page</a>";
	page_unsecure_foot();
}
?>