<strong>Navigation</strong><br/>
<a href="panel.php">Home</a><br/>
<a href="players2.php">Player List</a><br/>
<a href="password.php">Change Password</a><br/>
<a href="mods.php">Contact Moderators</a><br/>
<a href="graphs.php">Graph</a><br/>
<a href="<?php global $rules_file; echo $rules_file; ?>"><strong>View Rules</strong></a><br/>
<a href="<?php global $map_file; echo $map_file; ?>">View Game Map</a><br/>
<?php if( is_game_started() ) { ?>
<?php if( IsHuman() || IsZombie() ) { ?>
<a href="tag.php">Report Killing a Human</a><br/>
<?php } ?>
<?php if( IsHuman() ) { ?>
<a href="stun.php">Report Stunning a Zombie</a><br/>
<a href="supply.php">Cash in Supply Code</a><br/>
<?php } }?>
<?php if( IsZombie() || IsAdmin() || IsSpectator() ) { ?>
<a href="ztree.php">The Zombie Family Tree</a><br/>
<?php } ?>
<br/>
<a href="breakdown.php">Score Breakdown</a><br/>
<br/>
<?php if( IsAdmin() ) { ?>
<div><strong>Administration</strong><br/>
<a href="add_player.php">Add Player</a><br/>
<a href="oz2.php">Manage OZ List</a><br/>
<a href="startgame.php">Start Game Wizard</a><br/>
<a href="game.php">Game Settings</a><br/>
<a href="manage_inventory.php">Inventory Management</a><br/>
<a href="subscriptions.php">Mailing Lists</a><br/>
<a href="manage_stuns.php">Stun Management</a><br/>
<a href="milestones.php">View Milestone Report</a><br/>
<a href="manage_supply.php">Generate Supply Codes</a><br/>
<a href="view_mail.php">View Mail Logs</a><br/>
<a href="edit_template.php">Edit E-Mail Templates</a><br/>
<a href="manage_waiver.php">Waiver Information</a><br/>
<a href="archive.php">Archive Management</a>
<a href="csv.php">CSV</a><br/>
</div>
<br/>
<?php } ?>
<a href="login.php?logout=">Logout</a>