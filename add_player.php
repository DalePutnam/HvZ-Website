<?php session_start();
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/rgame.php");
require_once("require/forms.php");
Secure(true);

if( isset( $_REQUEST["action"] ) )
{
	if($_REQUEST["action"] == "Add")
	{
		$first = trim($_REQUEST["first_name"]);
		$last = trim($_REQUEST["last_name"]);
		$email = trim($_REQUEST["email"]);
		$type = trim($_REQUEST["type"]);
		
		$addresult = register_player($first, $last, $email, $type);
		if( !is_array($addresult) ) reload_self("&E-mail already exists.");
		$id = $addresult["id"];
		$password = $addresult["password"];
		if( isset( $_REQUEST["oz"] ) )
		{
			add_to_pool($id);
		}
		if( !is_maintenance() )
		{
			hvzmailf($email, "register", array("password" => $password, "first_name" => $first, "last_name" => $last));
		}
		reload_self("Player $first $last has been added.");
	}
}

page_head();
?>
<h2>Add Player</h2>
<div>
	<?php
		player_form( NULL, false );
	?>
</div>
<?php
page_foot();
?>
