<?php
/**
 * Created by PhpStorm.
 * User: brwarner
 * Date: 16/02/14
 * Time: 10:19 PM
 */
session_start();

require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rarchive.php");

// Admin only page
Secure(true);

if( isset($_REQUEST["action"]) )
{
    $action = $_REQUEST["action"];

    if($action == "Delete")
    {
        $id = $_REQUEST["id"];
        delete_archive($id);
        set_alert("SUCCESS", "Archive deleted.");
    }
    elseif($action == "Archive Game")
    {
        $name = $_REQUEST["name"];
        archive_game($name);
        set_alert("SUCCESS", "Game archived as '$name'.");
    }
    elseif($action == "Reset Game")
    {
        $first = $_REQUEST["first_name"];
        $last = $_REQUEST["last_name"];
        $email = $_REQUEST["email"];

        reset_game($first, $last, $email);
        logout("Game reset. Check the $email account for new admin login information.");
    }
}

page_head();
?>
<h2>Archive Management</h2>
<h3>Create Archive</h3>
<p>
    Archiving a game involves saving all relevant data to backup archive tables which can be accessed by the public.
    You must name your archive entry, suggested name is term and year e.g. Winter 2014.
</p>
<div class="row">
    <div class="col-md-4">
        <form method="post" action="">
            <div class="form-group">
                <label>Archive Name</label>
                <input class="form-control" name="name" value="<?php echo date("F o"); ?>" />
            </div>
            <input class="btn btn-default" type="submit" name="action" value="Archive Game" />
        </form>
    </div>
</div>
<h3>Game Reset</h3>
<p>
    Resetting a game will delete ALL game data and reset the website. The last action will be registering a use of type 'ADMIN' with the given name and e-mail so you can begin again.
    <strong>Ensure you archive the game first before clicking this!</strong>
</p>
<div class="row">
    <div class="col-md-4">
        <form method="post" action="">
            <div class="form-group">
                <label>First Name</label>
                <input class="form-control" name="first_name" />
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input class="form-control" name="last_name" />
            </div>
            <div class="form-group">
                <label>Admin E-Mail</label>
                <input class="form-control" name="email" />
            </div>
            <input class="btn btn-default" type="submit" name="action" value="Reset Game" />
        </form>
    </div>
</div>
<h3>Archives</h3>
<div class="row">
    <div class="col-md-4">
        <table class="table table-striped table-bordered table-condensed">
            <tr><th>Name</th><th>Created On</th><th></th></tr>
            <?php
            $archives = get_archives();
            foreach($archives as $archive)
            {
                $date_str = date("F o", $archive['created']);
                echo "<tr><td>{$archive['name']}</td><td>$date_str</td><td>";
                ?>
                <form method="post" action="">
                    <input type="hidden" name="id" value="<?php echo $archive['id']; ?>" />
                    <input class="btn btn-danger" type="submit" name="action" value="Delete" />
                </form>
                <?php
                echo "</td></tr>";
            }
            ?>
        </table>
    </div>
</div>
<?php
page_foot();
?>