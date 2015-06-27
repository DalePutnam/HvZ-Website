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
        set_alert("SUCCESS", "Inventory updated.");
    }
}

page_head();
?>
<h2>Inventory Management</h2>
<div class="row">
    <div class="col-md-6">
        <form method="post" action="">
            <div class="form-group">
                <label for="inventory">Inventory</label>
                <textarea class="form-control" name="inventory" cols="30" rows="20"><?php echo get_inventory(); ?></textarea>
            </div>
            <input class="btn btn-default" type="submit" name="action" value="Save" />
        </form>
    </div>
</div>
<?php
page_foot();
?>