<?php session_start(); ?>
<?php
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rgame.php");

// Admin only page
Secure(true);

if( isset( $_REQUEST["action"] ) )
{
    $action = $_REQUEST["action"];
    if( $action == "Save" )
    {
        $text = $_REQUEST["inventory"];
        set_inventory($text);
        reload_self("Inventory updated.");
    }
}

page_head();
?>
    <h1>Inventory Management</h1>
    <form method="post" action="">
        <label for="inventory">Inventory</label><textarea name="inventory" cols="30" rows="20"><?php echo get_inventory(); ?></textarea>
        <input type="submit" name="action" value="Save" />
    </form>
<?php
page_foot();
?>