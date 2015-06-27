<ul class="nav nav-sidebar sidebar-spacing">
<?php if( !Unsecure() ) { ?>
<li><a href="panel.php">Home</a></li>
<li><a href="players2.php">Player List</a></li>
<li><a href="mission_briefing.php">Mission Briefing</a></li>
<li><a href="password.php">Change Password</a></li>
<li><a href="mods.php">Contact Moderators</a></li>
<li><a href="graphs.php">Graph</a></li>
<li><a href="<?php global $rules_file; echo $rules_file; ?>"><strong>View Rules</strong></a></li>
<li><a href="<?php global $map_file; echo $map_file; ?>">View Game Map</a></li>
<?php if( is_game_started() ) { ?>
<?php if( IsHuman() || IsZombie() ) { ?>
<li><a href="tag.php">Report Killing a Human</a></li>
<?php } ?>
<?php if( IsHuman() ) { ?>
<li><a href="stun.php">Report Stunning a Zombie</a></li>
<li><a href="supply.php">Cash in Supply Code</a></li>
<?php } }?>
<?php if( IsZombie() || IsAdmin() || IsSpectator() ) { ?>
<li><a href="ztree.php">The Zombie Family Tree</a></li>
<?php } ?>
<li><a href="breakdown.php">Score Breakdown</a></li>
</ul>
<br/>
<ul class="nav nav-sidebar sidebar-spacing">
<?php if( IsAdmin() ) { ?>
<li><strong>Administration</strong></li>
<li><a href="add_player.php">Add Player</a></li>
<li><a href="schedule_briefing.php">Schedule Briefing</a></li>
<li><a href="signup_locations.php">Sign Up Locations</a></li>
<li><a href="oz2.php">Manage OZ List</a></li>
<li><a href="startgame.php">Start Game Wizard</a></li>
<li><a href="game.php">Game Settings</a></li>
<li><a href="manage_inventory.php">Inventory Management</a></li>
<li><a href="subscriptions.php">Mailing Lists</a></li>
<li><a href="manage_stuns.php">Stun Management</a></li>
<li><a href="milestones.php">View Milestone Report</a></li>
<li><a href="manage_supply.php">Generate Supply Codes</a></li>
<li><a href="view_mail.php">View Mail Logs</a></li>
<li><a href="edit_template.php">Edit E-Mail Templates</a></li>
<li><a href="manage_waiver.php">Waiver Information</a></li>
<li><a href="archive.php">Archive Management</a></li>
<li><a href="csv.php">CSV</a></li>
<?php } ?>
<?php if ( GetImpersonate() ){ ?>
<li><a href='panel.php?imp_end='>End Impersonation</a></li>
<?php } else { ?>
<li><a href="login.php?logout=">Logout</a></li>
<?php } ?>
<?php } else { ?>
<li><a href="mods.php">Contact Moderators</a></li>
<li><a href="<?php global $rules_file; echo $rules_file; ?>"><strong>Game Rules</strong></a></li>
<li><a href="graphs.php">Game Graphs</a></li>
<li><a href="password_reset.php">Forgot/Reset Password</a></li>
<br/>
<ul class="nav nav-sidebar sidebar-spacing">
<li><a href="login.php">Login</a></li>
</ul>
<?php } ?>
</ul>