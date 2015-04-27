<?php
require_once(dirname(__FILE__) . "/rgame.php");

function stun_value_editor($stun_scores)
{
    echo "<table>";
    echo "<tr><th>Scores</th><th>Date of Effect</th></tr>";
    foreach($stun_scores as $entry)
    {
        $id = $entry["id"];
        $scores = implode(",", $entry["scores"]);
        $date = $entry["date"]->format('l F jS \a\t g:iA');
        echo "<tr><td>$scores</td><td>$date</td>";
        ?>
            <td>
                <form method='post' action=''>
                    <input type="hidden" name="id" value="<?php echo $id; ?>" />
                    <input type='submit' name="action" value="Delete Score Entry" />
                </form>
            </td>
        <?php
        echo "</tr>";
    }
    echo "</table>";

    ?>
    <form method="post" action="">
        <input name="scores" value="5,4,3,2,1,0" />&nbsp;
        <input name="date" class="dateme" />&nbsp;
        <input type="submit" name="action" value="Add Points Per Stun Entry" />
    </form>
    <script type="text/javascript">
        $(".dateme").datetimepicker();
    </script>
    <?php
}

function team_select($name, $def)
{
    $selected = "selected='false'";
    ?>
    <select class="form-control" required="true" name="<?php echo $name; ?>">
        <option value="NONE" <?php if($def == "NONE") echo $selected; ?>>None</option>
        <option value="HUMAN" <?php if($def == "HUMAN") echo $selected; ?>>Human</option>
        <option value="ZOMBIE" <?php if($def == "ZOMBIE") echo $selected; ?>>Zombie</option>
        <option value="ADMIN" <?php if($def == "ADMIN") echo $selected; ?>>Admin</option>
        <option value="BANNED" <?php if($def == "BANNED") echo $selected; ?>>Banned</option>
        <option value="SPECTATE" <?php if($def == "SPECTATE") echo $selected; ?>>Spectator</option>
    </select>
    <?php
}

function player_form($data = NULL, $showtype = TRUE)
{
	$first = ""; $last = ""; $email=""; $code=""; $type=""; $id="";
	
	// Setup default type as human after the game begins, but NONE beforehand
	if( is_game_started() ) { $type="HUMAN"; }
	else { $type = "NONE"; }
	if(!is_null($data))
	{
		$first = $data['first_name'];
		$last = $data['last_name'];
		$email = $data['email'];
		$code = $data['code'];
		$type = $data['type'];
		$id = $data["id"];
	}
	$selected = "selected='true'";
	?>
	<form class="form-horizontal" action="" method="post">
	<input name="id" type="hidden" value="<?php echo $id;?>" />
	<div class="form-group">
        <div class="col-md-12"><input class="form-control" placeholder="First Name" name="first_name" required="true" value="<?php echo $first;?>"/></div>
    </div>
	<div class="form-group">
        <div class="col-md-12"><input class="form-control" placeholder="Last Name" name="last_name" required="true" value="<?php echo $last;?>"/></div>
    </div>
	<div class="form-group">
        <div class="col-md-12"><input class="form-control" placeholder="Email" name="email" required="true" value="<?php echo $email;?>"/></div>
    </div>
	<?php if( !is_null($data) ) { ?>
        <div class="form-group">
            <div class="col-md-12"><input class="form-control" placeholder="Code" name="code" required="true" value="<?php echo $code;?>"/></div>
        </div>
	<?php } 
	if( $showtype ) 
	{
	?>
        <div class="form-group">
            <div class="col-md-12"><?php team_select("type", $type); ?></div>
        </div>
		<?php
	}
	else
	{
		?>
		<input type="hidden" name="type" value="NONE" />
		<?php
	}
	?>
    <div class="form-group">
        <div class="col-md-4">
            <input class="btn btn-default btn-block" type="submit" value="<?php if(is_null($data)) { echo "Add"; } else { echo "Commit"; }?>" name="action" />
        </div>
        <?php if(is_null($data) && !is_game_started()) { ?>
            <div class="col-md-8 checkbox">
                <label><input type="checkbox" name="oz" value="yes" /> Enter Original Zombie Lottery</label>
            </div>
        <?php } ?>
        <?php if(!is_null($data)) { ?>
            <div class="col-md-4">
                <input class="btn btn-default btn-block" type="submit" value="Cancel" name="action" />
            </div>
        <?php } ?>
    </div>
	</form>
	<?php
}
