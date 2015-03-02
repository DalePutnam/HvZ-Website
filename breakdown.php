<?php

session_start();

require_once("require/secure.php");
require_once("require/basic.php");
require_once("require/rsupply.php");
require_once("require/rstun.php");
require_once("require/rplayers.php");
require_once("require/rgame.php");

$myID = -1;
$self = false;
if(isset($_REQUEST["id"]))
{
    Secure(true);
    $myID = $_REQUEST["id"];
}
else
{
    Secure(false);
    $myID = ID();
    $self = true;
}

page_head();

$players = get_players();
$tags = get_tags($myID);
$stun_stats = "";

$date_fmt = 'l F jS \a\t g:iA';

function player_name($id)
{
    global $players;
    $player = $players[$id];
    return $player["first_name"] . " " . $player["last_name"];
}

function mini_stun_calc()
{
    global $sql;
    global $stun_stats;
    global $date_fmt;
    global $myID;

    $total = 0;
    $stuns = $sql->query("SELECT killer, victim, helper, time FROM hvz_stuns WHERE ratified IS NOT NULL ORDER BY time");
    $score_table = get_stun_scores();

    $kill_count = array();

    while($stun = $stuns->fetch_assoc())
    {
        $killer = $stun["killer"];
        $victim = $stun["victim"];
        $helper = $stun["helper"];
        $time = php_datetime($stun["time"]);
        $count = 0;

        if(isset($kill_count[$victim]))
        {
            $kill_count[$victim] = clean_kill_count($kill_count[$victim], $time, NULL, $victim);
            $count = count($kill_count[$victim]);
        }
        else
        {
            $kill_count[$victim] = array();
        }

        $delta = get_appropriate_score($score_table, $time, $count);
        if($delta > 0)
        {
            array_push($kill_count[$victim], $time);
        }
        if($helper != NULL && $delta > 0)
        {
            if($helper == $myID)
            {
                $stun_stats .= "1 point for helping " . player_name($killer) . " stun " . player_name($victim) . " on " . $time->format($date_fmt) . "<br/>";
                $total += 1;
            }

            $delta -= 1;
        }

        if($killer == $myID)
        {
            $stun_stats .= "$delta point(s) for stunning " . player_name($victim) . " on " . $time->format($date_fmt) . "<br/>";
            $total += $delta;
        }
    }
    return $total;
}

$stun_total = mini_stun_calc();
$num_supply = get_num_my_supply($myID);
$score_per_supply = get_supply_score();
$score_per_tag = get_tag_score();

$bonus = 0;
if($self) {$bonus = Bonus();}
else $bonus = $players[$myID]["bonus_score"];

$score = 0;
if($self) {$score = Score();}
else $score = $players[$myID]["score"];

?>
<h1>Score Breakdown<?php if(!$self) echo " for <span style='color: red'>" . player_name($myID) . "</span>"; ?> : <?php echo $score ?> point(s)</h1>
<h2>Supply Codes: <?php echo $num_supply * $score_per_supply; ?> point(s)</h2>
<p><?php echo $score_per_supply; ?> point(s) per supply code multiplied by <?php echo $num_supply; ?> supply codes.</p>
<h2>Stuns: <?php echo $stun_total; ?> point(s)</h2>
<p><?php echo $stun_stats; ?></p>
<h2>Tags: <?php echo count($tags) * $score_per_tag; ?> point(s)</h2>
<p>
    <?php
    foreach($tags as $tag)
    {
        echo "$score_per_tag point(s) for killing " . player_name($tag["id"]) . " on " . date($date_fmt, strtotime($tag["time"])) . "<br/>";
    }
    ?>
</p>
<h2>Bonus: <?php echo $bonus ?> point(s)</h2>
Bonus points are awarded to teams based on mission success/failure.
<?php

page_foot();