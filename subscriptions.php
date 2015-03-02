<?php session_start(); ?>
<?php 
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rplayers.php");
require_once("require/mailing.php");

// Admin only page
Secure(true);

if( isset( $_REQUEST["action"] ) )
{
	$action = $_REQUEST["action"];
	if( $action == "Fix Subscriptions" )
	{
		fix_emails();
		reload_self("Subscriptions have been fixed.");
	}
	else if( $action == "Clear Subscriptions" )
	{
		clear_subscriptions();
		reload_self("Subscriptions have been cleared.");
	}
}

page_head();
?>

<h1>Subscription Helpers</h1>

<h2>Mass Mailing List Operations</h2>
<div><strong>Fix Subscriptions</strong> will unregister all e-mails from all mailing lists, then re-add all players to the appropriate mailing list. If for some reason the mailing lists are no longer properly sync'd with the player database on this site, this button should fix all problems. Despite what it may sound like, this operation is pretty quick and should only require a few seconds (requires 3 SQL queries and 6 queries on the mailing list).<br/>
<form method="post" action=""><input type="submit" name="action" value="Fix Subscriptions"/></form>
</div>

<div><strong>Clear Subscriptions</strong> will unregister all e-mails from all mailing lists. If you click this and then became sad, <strong>Fix Subscriptions</strong> will make life a lot better for you.
<form method="post" action=""><input type="submit" name="action" value="Clear Subscriptions"/></form>
</div>

<h2>Unsubscription</h2>
<?php
$result = $sql->query("SELECT COUNT(`id`) FROM `hvz_players` WHERE `subscribe`=0");
$row = $result->fetch_row();
?>
<p>There are currently <?php echo $row[0]; ?> players opting-out of the all players mailing list.</p>

<h2>Mailing List Links</h2>
<h3>Emails</h3>
<p>Use these links to quickly open up your e-mail client</p>
<a href="mailto:hvz@csclub.uwaterloo.ca">All Players</a> - hvz@csclub.uwaterloo.ca<br/>
<a href="mailto:hvz-humans@csclub.uwaterloo.ca">Humans</a> - hvz-humans@csclub.uwaterloo.ca<br/>
<a href="mailto:hvz-zombies@csclub.uwaterloo.ca">Zombies</a> - hvz-zombies@csclub.uwaterloo.ca<br/>
<h3>Admin Pages</h3>
<p>Use these to administer the settings for each mailing list</p>
<a href="https://mailman.csclub.uwaterloo.ca/admin/hvz">All Players Mailinglist Admin Page</a><br/>
<a href="https://mailman.csclub.uwaterloo.ca/admin/hvz-humans">Human Mailinglist Admin Page</a><br/>
<a href="https://mailman.csclub.uwaterloo.ca/admin/hvz-zombies">Zombie Mailinglist Admin Page</a>


<?php
page_foot();
?>
