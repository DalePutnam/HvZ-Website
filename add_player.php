<?php session_start();
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/rgame.php");
require_once("require/forms.php");
Secure(true);

$location = 0;

if( isset( $_REQUEST["action"] ) )
{
	if($_REQUEST["action"] == "Add")
	{
		$first = trim($_REQUEST["first_name"]);
		$last = trim($_REQUEST["last_name"]);
		$email = trim($_REQUEST["email"]);
		$type = trim($_REQUEST["type"]);
        $location = trim($_REQUEST["location"]);
		
		$addresult = register_player($first, $last, $email, $type, $location);
		if( !is_array($addresult) ) set_alert("ERROR", "E-mail already exists.");
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
		set_alert("SUCCESS", "Player $first $last has been added.");
	}
}

page_head();
?>
<div class="row">
<div class="col-md-3">
    <h2>Add Player</h2>
    <?php player_form( NULL, false, true, $location ); ?>
</div>
</div>
<?php
page_foot();
?>
