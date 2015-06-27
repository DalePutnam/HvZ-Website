<?php session_start(); ?>
<?php 
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rgame.php");

// Admin only page
Secure(true);

page_head();
?>
<h2>Waiver Management</h2>
<p>You can edit the current waiver in the <a href="edit_template.php?edit=waiver">templates editor</a>.</p>
<h3>Waiver Preview</h3>
<div>
<?php
$waiver = file_get_contents( $templates_dir . "waiver.txt" );
echo $waiver
?>
</div>
<h3>Who Hasn't Signed</h3>
<?php
$players = get_players_without_waiver();
$ids = get_ids_sorted_by( array("first_name", "last_name"), TRUE, FALSE, NULL, FALSE );
?>
<p>The following <?php echo count($players); ?> players have not signed the waiver.</p>

<div class="row">
    <div class="col-md-4">
        <?php gen_player_table( $players, $namePlayerProperties, $ids, false ); ?>
    </div>
</div>

<?php
page_foot();
?>