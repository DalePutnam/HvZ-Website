<?php
/**
 * Created by PhpStorm.
 * User: Dale
 * Date: 2015-03-06
 * Time: 3:25 PM
 */
session_start();
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rbriefing.php");
Secure(true);

$time = time();

$briefing = array("id" => 0,
    "release_time" => implode("T", explode(" ", date("Y-m-d H:i:s", $time))),
    "expire_time" => implode("T", explode(" ", date("Y-m-d H:i:s", $time + 3600))),
    "human_title" => "",
    "human_body" => "",
    "zombie_title" => "",
    "zombie_body" => "");
$editing = false;

if( isset( $_REQUEST["action"] ) )
{
    $action = $_REQUEST["action"];
    if( $action == "Submit" )
    {
        $release = $_REQUEST["release_time"];
        $expire = $_REQUEST["expire_time"];
        $human_title = $_REQUEST["human_title"];
        $human_body = $_REQUEST["human_body"];
        $zombie_title = $_REQUEST["zombie_title"];
        $zombie_body = $_REQUEST["zombie_body"];

        if (check_briefing_conflict($release, $expire))
        {
            set_alert("ERROR", "&This briefing conflicts with a scheduled briefing");
        }
        else
        {
            schedule_briefing($release, $expire, $human_title, $human_body, $zombie_title, $zombie_body);
            set_alert("SUCCESS", "Briefing has been scheduled.");
        }
    }
    elseif( $action == "Update" )
    {
        $id = $_REQUEST["id"];
        $release = $_REQUEST["release_time"];
        $expire = $_REQUEST["expire_time"];
        $human_title = $_REQUEST["human_title"];
        $human_body = $_REQUEST["human_body"];
        $zombie_title = $_REQUEST["zombie_title"];
        $zombie_body = $_REQUEST["zombie_body"];

        update_briefing($id, $release, $expire, $human_title, $human_body, $zombie_title, $zombie_body);
        set_alert("SUCCESS", "Briefing has been updated");
    }
    elseif( $action == "Delete")
    {
        $id = $_REQUEST["id"];
        $result = delete_briefing($id);

        if ($result)
        {
            set_alert("SUCCESS", "Briefing deleted successfully");
        }
        else
        {
            set_alert("ERROR", "Failed to delete briefing");
        }
    }
    elseif( $action == "Edit" )
    {
        $id = $_REQUEST["id"];
        $briefing = get_briefing($id);
        $briefing["release_time"] = implode("T", explode(" ", $briefing["release_time"]));
        $briefing["expire_time"] = implode("T", explode(" ", $briefing["expire_time"]));
        $editing = true;
    }
    else
    {

    }
}

page_head();
?>
<div class="row">
    <div class="col-md-12">
        <h2>Manage Mission Briefings</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <form action="" method="post">
            <label>Release Time</label><input class="form-control" name="release_time" type="datetime-local" value="<?php echo $briefing['release_time'];?>"/><br/>
            <label>Expire Time</label><input class="form-control" name="expire_time" type="datetime-local" value="<?php echo $briefing['expire_time'];?>"/><br/>
            <label>Human Title</label><input class="form-control" name="human_title" type="text" value="<?php echo $briefing['human_title'];?>"/><br/>
            <label>Human Briefing</label><br/>
            <textarea class="form-control" name="human_body" cols="100" rows="10" style="resize: vertical"><?php echo $briefing['human_body'];?></textarea><br/>
            <label>Zombie Title</label><input class="form-control" name="zombie_title" type="text" value="<?php echo $briefing['zombie_title'];?>"/><br/>
            <label>Zombie Briefing</label><br/>
            <textarea class="form-control" name="zombie_body" cols="100" rows="10" style="resize: vertical"><?php echo $briefing['zombie_body'];?></textarea><br/>
            <?php
                if ($editing)
                {
                    echo "<input type='hidden' value='$briefing[id]' name='id'/>";
                    echo "<input class='btn btn-default' type='submit' value='Update' name='action'/>";
                }
                else
                {
                    echo "<input class='btn btn-default' type='submit' value='Submit' name='action'/>";
                }
            ?>

            <input class='btn btn-default' type="submit" value="Clear" name="action"/>
        </form>
    </div>
    <div id="table" class="col-md-6">
        <label>Right click to edit/delete</label>
        <?php gen_briefing_table(); ?>
    </div>
</div>

<script type="text/javascript">
    LoadTableData( <?php echo json_encode(get_briefings()); ?>);
    var actions = { "Edit" : { "type" : "post" },
                    "Delete" : { "type" : "post",
                                 "confirm":true,
                                 "message" : "Are you sure you would like to delete ",
                                 "columns" : ["title"] } };
    var briefingTable = new Table(  <?php echo json_encode($briefingProperties); ?>, { }, actions );
    briefingTable.take( $("#table"), false );
</script>

<?php
page_foot();
?>