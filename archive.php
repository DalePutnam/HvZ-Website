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
        reload_self("Archive deleted.");
    }
    elseif($action == "Archive Game")
    {
        $name = $_REQUEST["name"];
        archive_game($name);
        reload_self("Game archived as '$name'.");
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
<h1>Archive Management</h1>
<h2>Create Archive</h2>
<p>Archiving a game involves saving all relevant data to backup archive tables which can be accessed by the public. You must name your archive entry, suggested name is term and year e.g. Winter 2014.</p>
<form method="post" action="">
    Archive Name:&nbsp;<input name="name" value="<?php echo date("F o"); ?>" /><br/>
    <input type="submit" name="action" value="Archive Game" />
</form>
<h2>Game Reset</h2>
<p>Resetting a game will delete ALL game data and reset the website. The last action will be registering a use of type 'ADMIN' with the given name and e-mail so you can begin again. <strong>Ensure you archive the game first before clicking this!</strong></p>
<form method="post" action="">
    First Name:&nbsp;<input name="first_name" /><br/>
    Last Name:&nbsp;<input name="last_name" /><br/>
    Admin E-Mail:&nbsp;<input name="email" /><br/>
    <input type="submit" name="action" value="Reset Game" />
</form>
<h2>Archives</h2>
<table>
    <tr><th>Name</th><th>Created On</th></tr>
    <?php
    $archives = get_archives();
    foreach($archives as $archive)
    {
        $date_str = date("F o", $archive['created']);
        echo "<tr><td>{$archive['name']}</td><td>$date_str</td><td>";
        ?>
        <form method="post" action="">
            <input type="hidden" name="id" value="<?php echo $archive['id']; ?>" />
            <input type="submit" name="action" value="Delete" />
        </form>
        <?php
        echo "</td></tr>";
    }
    ?>
</table>
<?php
page_foot();
?>