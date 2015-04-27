<?php session_start();
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rgame.php");
require_once("require/rstun.php");

Secure(false);
if( isset($_GET["reload"]) )
{
	reload_self();
}
if( isset($_REQUEST["action"]) )
{
	$action = $_REQUEST["action"];
	if( $action == "Show Score" )
	{
		$sql->query("UPDATE `hvz_players` SET `show_score`=1 WHERE `id`='" . ID() . "'");
		reload_self("Your score is now visible to other players.");
	}
	elseif($action == "Hide Score")
	{
		$sql->query("UPDATE `hvz_players` SET `show_score`=0 WHERE `id`='" . ID() . "'");
		reload_self("Score hidden.");
	}
	elseif($action == "Set My Score" && IsAdmin())
	{
		$score = $_REQUEST["score"];
		$score = intval($sql->real_escape_string($score));
		$sql->query("UPDATE `hvz_players` SET `score`='$score', `bonus_score`='$score' WHERE `id`='" . ID() . "'");
		reload_self("Score updated.");
	}
	elseif($action == "Unsubscribe")
	{
		update_player_sub(ID(), false);
		setup_subscription_by_type(Email(), Type(), false);
		reload_self("Unsubscribed.");

	}
	elseif($action == "Subscribe")
	{
		update_player_sub(ID(), true);
		setup_subscription_by_type(Email(), Type(), true);
		reload_self("Subscribed.");
	}
}
page_head();?>
<div class="row">
<div class='col-md-3'><h3>Welcome, <?php echo First(); ?> <?php echo Last(); ?></h3>
<?php
if( IsAdmin() ) {
	?>
    <form method="post" action="">
        <div class="form-group">
            <label>Set Your Score: </label>
            <input class="form-control" name="score" value="<?php echo Score(); ?>" />
        </div>
        <input class="btn btn-default" type="submit" name="action" value="Set My Score" /><br/>
    </form>
	<?php
}
else
{
	echo "<div class='row'>";
    echo "<div class='col-md-12'>";
	if( is_game_started() )
	{
		echo "The game is currently <strong>active</strong>.";
	}
	else
	{
        $date = date('l F jS \a\t g:iA', strtotime(get_game_start()));
		echo "<div>The game has not yet begun. It will start $date</div>";
	}
	echo "</div></div>";
	?>
	<?php if( IsPlayer() ) { ?>
	<p>To send an e-mail to all players, e-mail <a href="mailto:hvz@csclub.uwaterloo.ca">hvz@csclub.uwaterloo.ca</a>.
	<?php } ?>
	<?php if( IsZombie() ) { ?>
	To send an e-mail to your fellow zombies, e-mail <a href="mailto:hvz-zombies@csclub.uwaterloo.ca">hvz-zombies@csclub.uwaterloo.ca</a>.
	<?php } ?>
	</p>
	<?php if( IsPlayer() || IsSpectator() ) { 
		$text = "subscribed";
		$button = "Unsubscribe";
		if( !IsSubscribed() ) {
			$text = "unsubscribed";
			$button = "Subscribe";
		}
		?>
		<p>You are currently <?php echo $text; ?> from the all players mailing list. <form method="post" action=""><input type="submit" class="btn btn-primary" name="action" value="<?php echo $button; ?>" /></form></p>
	<?php }
    // Inventory visible for humans or spectators
    if( IsHuman() || IsSpectator() ) {
        echo "<strong>Human Inventory:</strong>";
        echo "<p>";
        $text = get_inventory();
        if(is_null($text) || trim($text) == "") echo "Empty";
        else echo nl2br($text);
        echo "</p>";
    }
	echo "<strong>Team:</strong> " . Team() . "<br/>";
	if( IsPlayer() )
	{
		echo "<strong>Player Code:</strong> " . Code() . "<br/>";
		echo "<strong>Total Team Score:</strong> " . get_team_score(Type()) . "<br/>";
		echo "<strong>My Score:</strong> " . Score() . "<br/>";
		if( IsHuman() )
		{
			echo "<strong>Stuns (Ratified) Against Zombies:</strong> " . get_stuns_from_me(ID()) . "<br/>";
		}
		elseif( IsZombie() )
		{
			echo "<strong>Stuns (Ratified) Against Me:</strong> " . get_stuns_on_me(ID()) . "<br/>";
		}
		if( ShowScore() )
		{
		?>
			Your score is currently <strong>visible</strong> to other players.<br/>
			<form method="post" action=""><input type="submit" class="btn btn-primary" name="action" value="Hide Score" /></form>
		<?php
		}
		else
		{
		?>
			Your score is currently <strong>hidden</strong> from other players.<br/>
			<form method="post" action=""><input type="submit" class="btn btn-primary" name="action" value="Show Score" /></form>
		<?php
		}
	}
}
page_foot();
?>
</div>
</div>
