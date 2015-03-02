<?php session_start(); ?>
<?php 
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rgame.php");

// Admin only page
Secure(true);

page_head();
?>
<h1>Waiver Management</h1>
<p>You can edit the current waiver in the <a href="edit_template.php?edit=waiver">templates editor</a>.</p>
<h2>Waiver Preview</h2>
<div>
<?php
$waiver = file_get_contents( $templates_dir . "waiver.txt" );
echo $waiver
?>
</div>
<h2>Who Hasn't Signed</h2>
<?php
$players = get_players_without_waiver();
$ids = get_ids_sorted_by( array("first_name", "last_name"), TRUE, FALSE, NULL, FALSE );
?>
<p>The following <?php echo count($players); ?> players have not signed the waiver.</p>
<?php
gen_player_table( $players, $namePlayerProperties, $ids, false );
?>

<?php
page_foot();
?>