<?php
/**
 * Created by PhpStorm.
 * User: Dale
 * Date: 2015-06-24
 * Time: 11:02 PM
 */
session_start();
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rlocations.php");
Secure(true);

if( isset( $_REQUEST["action"] ) )
{
    $action = $_REQUEST["action"];
    if( $action == "Add" )
    {
        $location = $_REQUEST["location"];

        if (add_location($location))
        {
            set_alert("SUCCESS", "Location Added");
        }
        else
        {
            set_alert("ERROR", "Failed to add location");
        }
    }
    if ( $action == "Delete" )
    {
        $id = $_REQUEST["id"];

        if ( get_total_signups_at_location($id) == 0)
        {
            $result = delete_location($id);

            if ($result)
            {
                set_alert("SUCCESS", "Location deleted successfully");
            }
            else
            {
                set_alert("ERROR", "Failed to delete location");
            }
        }
        else
        {
            set_alert("ERROR", "Only locations with no sign ups can be deleted");
        }
    }
}

page_head();
?>

<div class="row">
    <div class="col-md-12">
        <h2>Manage Sign Up Locations</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <form class="" action="" method="post">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Enter Location" name="location" required="true"/>
                <span class="input-group-btn">
                    <input type="submit" class="btn btn-default" value="Add" name="action"/>
                </span>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div id="table" class="col-md-4">
        <?php gen_location_table(); ?>
    </div>
</div>

<script type="text/javascript">
    LoadTableData( <?php echo json_encode(get_locations_with_signups()); ?>);
    var actions = { "Delete" : { "type" : "post",
                        "confirm":true,
                        "message" : "Are you sure you would like to delete ",
                        "columns" : ["name"] } };
    var locationTable = new Table(  <?php echo json_encode($locationProperties); ?>, { }, actions );
    locationTable.take( $("#table"), false );
</script>

<?php
page_foot();
?>