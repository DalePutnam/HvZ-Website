<?php
require_once(dirname(__FILE__) . "/sql.php");
require_once(dirname(__FILE__) . "/rplayers.php");
require_once(dirname(__FILE__) . "/rgame.php");
require_once(dirname(__FILE__) . "/config.php");

// gets the value of the next stun for the zombie $id
function get_stun_value($id)
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$result = $sql->query("SELECT COUNT(`id`) FROM `hvz_stuns` WHERE `ratified` IS NULL AND `victim` = '$id'");
	$row = $result->fetch_row();
	$count = $row[0];
	
	return get_score_for_zed( $count );
}
function get_num_stuns($id, $idother)
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$idother = $sql->real_escape_string($idother);
	$result = $sql->query("SELECT COUNT(`id`) FROM `hvz_stuns` WHERE `ratified` IS NULL AND `victim` = '$idother' AND `killer` = '$id'");
	$row = $result->fetch_row();
	return $row[0];
}
function stun_player($id, $idother, $datetime, $comment, $idhelper = NULL)
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$idother = $sql->real_escape_string($idother);
	$datetime = $sql->real_escape_string($datetime);
	$comment = $sql->real_escape_string($comment);
	
	// Can't stun self
	if( $id == $idother ) return "You can't stun yourself.";
	
	$player = get_player($idother);
	if( $player == NULL ) return "Player does not exist.";
	if( $player["type"] != "ZOMBIE" ) return "Player must be a zombie.";
	
	// compute date
	$phpdate = strtotime( $datetime );
	if( $phpdate == FALSE ) return "Not a valid date.";
	$mysqldate = date( 'Y-m-d H:i:s', $phpdate );
	
	if( $phpdate < php_game_start() ) return "Time is before game start.";
	if( $phpdate > php_game_end() ) return "Time is after game end.";
	if( $phpdate > time() ) return "Can not report stun from the future!";
	
	// get last ratification time
	/*$result = $sql->query("SELECT MAX(`ratified`) FROM `hvz_stuns`");
	if( $result->num_rows > 0 )
	{
		$row = $result->fetch_row();
		$maxtime = strtotime($row[0]);
		if( $maxtime != FALSE )
		{
			if( $phpdate <= $maxtime ) return "You are past the cut-off time to report that stun.";
		}
	}*/
	
	$query = "INSERT INTO `hvz_stuns` (`killer`, `victim`, `time`, `comment`, `helper`) VALUES ('$id', '$idother', '$mysqldate', '$comment', ";
	if( $idhelper !== NULL )
	{
		$idhelper = $sql->real_escape_string($idhelper);
		$query .= "'$idhelper'";
	}
	else
	{
		$query .= "NULL";
	}
	$query .= ")";
	
	$result = $sql->query($query);
	if( !$result ) return "Internal error. Please contact a moderator.";
	return TRUE;
}
function delete_stun( $id )
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$sql->query("DELETE FROM `hvz_stuns` WHERE `id`='$id'");
}
function get_stuns_on_me( $id )
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$result = $sql->query("SELECT COUNT(victim) FROM hvz_stuns WHERE victim='$id' AND ratified IS NOT NULL");
	$row = $result->fetch_row();
	return $row[0];

}
function get_stuns_from_me( $id )
{
	global $sql;
	$id = $sql->real_escape_string($id);
	$result = $sql->query("SELECT COUNT(victim) FROM hvz_stuns WHERE killer='$id' AND ratified IS NOT NULL");
	$row = $result->fetch_row();
	return $row[0];

}

function remove_stun_scores($id)
{
    global $sql;
    $id = $sql->real_escape_string($id);

    $sql->query("DELETE FROM hvz_zombie_stun_scores WHERE id='$id'");
}

function add_stun_scores($csv, $date)
{
    global $sql;
    $csv = $sql->real_escape_string($csv);
    $date = $sql->real_escape_string($date);

    $date = sql_date(strtotime($date));

    $sql->query("INSERT INTO hvz_zombie_stun_scores (score_per_zed, date) VALUES ('$csv', '$date')");
}

function get_stun_scores()
{
    global $sql;
    $scores = $sql->query("SELECT id, score_per_zed, date FROM hvz_zombie_stun_scores ORDER BY date ASC");
    $result = array();
    while($score = $scores->fetch_assoc())
    {
        array_push($result, array("id" => $score["id"], "scores" => array_map("intval", explode(",", $score["score_per_zed"]))
            , "date" => php_datetime($score["date"])));
    }

    return $result;
}

/**
 * @param $kill_array
 * @param $time DateTime
 */
function clean_kill_count($kill_array, $time, $file, $logid)
{
    $eight_before = clone $time;
    $eight_before->sub(new DateInterval("PT8H"));
    $i = 0;
    for(; $i < count($kill_array); $i++)
    {
        if($kill_array[$i] >= $eight_before)
            break;
    }

    if($i > 0)
    {
        if($file != NULL)
        {
            fwrite($file, "Shaving $i previous entries from zombie $logid that occurred before " . $eight_before->format("Y-m-d H:i:s") . "\n");
        }
        array_splice($kill_array, 0, $i);
    }
    return $kill_array;
}

function get_appropriate_score($score_table, $time, $count)
{
    $i = 0;
    for(; $i < count($score_table); $i++)
    {
        if($score_table[$i]["date"] > $time)
            break;
    }
    if($i == 0)
    {
        return 0;
    }

    $list = $score_table[$i-1]["scores"];
    $max = count($list);
    if($count < $max) return $list[$count];
    else return $list[$max-1];
}

function recalculate_stuns()
{
    global $sql;
    global $logs_dir;

    $file = fopen($logs_dir . "recalculate.log", "w");
    if($file === FALSE) return "Calculation already in progress.";
    fwrite($file, "Recalculation beginning at " . date("Y-m-d H:i:s") . "\n");

    // get every stun that has already been ratified
    $stuns = $sql->query("SELECT S.killer, S.victim, S.helper, S.time, T.type FROM hvz_stuns AS S INNER JOIN hvz_players AS T ON T.id = S.killer WHERE S.ratified IS NOT NULL ORDER BY S.time");
    fwrite($file, "Found " . $stuns->num_rows . " ratified stuns.\n");

    // create a table that stores the decreasing score values (per stun) with the time they are valid
    $score_table = get_stun_scores();

    // arrays to hold calculation results

    // Map of human IDs to human scores
    $human_scores = array();

    // Map of zombie IDs to a sorted array of dates of the last times the zombie has been caught in the past eight hours
    $kill_count = array();

    while($stun = $stuns->fetch_assoc()) {
        $killer = $stun["killer"];
        $victim = $stun["victim"];
        $helper = $stun["helper"];
        $time = php_datetime($stun["time"]);
        $count = 0;

        if (isset($kill_count[$victim])) {
            $kill_count[$victim] = clean_kill_count($kill_count[$victim], $time, $file, $victim);
            $count = count($kill_count[$victim]);
        } else {
            $kill_count[$victim] = array();
        }

        $score = 0;
        if (isset($human_scores[$killer])) {
            $score = $human_scores[$killer];
        }
        $delta = get_appropriate_score($score_table, $time, $count);
        if ($delta > 0) {
            array_push($kill_count[$victim], $time);
        }
        if ($helper != NULL && $delta > 0) {
            $delta -= 1;
            $hscore = 0;
            if (isset($human_scores[$helper])) {
                $hscore = $human_scores[$helper];
            }
            $hscore = $hscore + 1;
            $human_scores[$helper] = $hscore;
        }

        if ($stun["type"] != "ZOMBIE") {
            fwrite($file, "Giving player $killer $delta points for killing $victim at " . $time->format("Y-m-d H:i:s") . ", marking $helper as helped\n");
            $score = $score + $delta;
            $human_scores[$killer] = $score;
        } else fwrite($file, "Ignoring player $killer getting $delta points for killing $victim at " . $time->format("Y-m-d H:i:s") . ", marking $helper as helped because they are a ZOMBIE\n");
    }

    // Quickly insert all the tag scores
    $tags = $sql->query("SELECT killer, victim FROM hvz_tags WHERE killer IS NOT NULL");
    while($tag = $tags->fetch_assoc())
    {
        $killer = $tag["killer"];
        $count = 0;
        if(isset($human_scores[$killer]))
        {
            $count = $human_scores[$killer];
        }
        $delta = get_tag_score();
        $count = $count + $delta;
        $human_scores[$killer] = $count;

        fwrite($file, "Giving player $killer $delta points for killing a human\n");
    }

    // And the supply code scores
    $supplies = $sql->query("SELECT player FROM hvz_supply_codes WHERE player IS NOT NULL");
    while($supply = $supplies->fetch_assoc())
    {
        $player = $supply["player"];
        $count = 0;
        if(isset($human_scores[$player]))
        {
            $count = $human_scores[$player];
        }
        $delta = get_supply_score();
        $count = $count + $delta;
        $human_scores[$player] = $count;

        fwrite($file, "Giving player $player $delta points for getting supply code\n");
    }

    if(count($human_scores) > 0) {
        // Insert all score changes into a temporary table
        $query = "INSERT INTO hvz_score_copy (id, score) VALUES ";
        foreach ($human_scores as $key => $value) {
            $query .= "('$key', '$value'),";
        }
        $query = substr($query, 0, strlen($query) - 1);
        $sql->query($query);

        fwrite($file, "\n\nFinal Query:\n$query\n");
    } else {
        fwrite($file, "No stuns to ratify...\n");
    }
    fclose($file);

    // Copy changes into actual player table
    $sql->query("UPDATE hvz_players SET score = 0");
    $sql->query("UPDATE hvz_players INNER JOIN hvz_score_copy ON hvz_players.id = hvz_score_copy.id SET hvz_players.score = hvz_score_copy.score;");
    $sql->query("UPDATE hvz_players SET score = score + bonus_score");

    // Delete temp data
    $sql->query("DELETE FROM hvz_score_copy WHERE 1");

    return TRUE;
}

function ratify_stuns()
{
    // Ratify non-ratified stuns
    global $sql;
    $sql->query("UPDATE `hvz_stuns` SET `ratified`=NOW() WHERE `ratified` IS NULL");

    recalculate_stuns();
}

function give_team_points($team, $points)
{
    global $sql;

    $points = intval($points);
    if($points == 0) return "Invalid number of points";
    $team = $sql->real_escape_string($team);

    $sql->query("UPDATE hvz_players SET bonus_score = bonus_score + $points WHERE type='$team'");

    recalculate_stuns();

    return TRUE;
}

/*function ratify_stuns( )
{
	global $sql;
	
	$result = $sql->query("SELECT `killer`, `victim`, `helper` FROM `hvz_stuns` WHERE `ratified` IS NULL ORDER BY `time` ASC;");
	
	$kill_count = array();
	$human_scores = array();
	$scores = get_score_per_zed();
	
	// Process data
	while( $row = $result->fetch_assoc() )
	{
		$killer = $row["killer"];
		$victim = $row["victim"];
		$helper = $row["helper"];
		$count = 0;
		if( isset($kill_count[$victim]) )
		{
			$count = $kill_count[$victim];
		}
		$kill_count[$victim] = $count + 1;
		
		$score = 0;
		if( isset($human_scores[$killer]) )
		{
			$score = $human_scores[$killer];
		}
		$delta = get_score_for_zed($count);
		if( $helper !== NULL && $delta > 0 )
		{
			$delta -= 1;
			$hscore = 0;
			if( isset($human_scores[$helper]) )
			{
				$hscore = $human_scores[$helper];
			}
			$hscore = $hscore + 1;
			$human_scores[$helper] = $hscore;
		}
		
		$score = $score + $delta;
		$human_scores[$killer] = $score;
	}
	
	// Insert all score changes into a temporary table
	$query = "INSERT INTO `hvz_score_copy` (`id`, `score`) VALUES ";
	foreach( $human_scores as $key => $value )
	{
		$query .= "('$key', '$value'),";
	}
	$query = substr($query, 0, strlen($query)-1);
	$sql->query($query);
	
	// Copy changes into actual player table
	$sql->query("UPDATE `hvz_players` INNER JOIN `hvz_score_copy` ON `hvz_players`.`id` = `hvz_score_copy`.`id` SET `hvz_players`.`score` = `hvz_players`.`score` + `hvz_score_copy`.`score`;");
	
	// Delete temp data
	$sql->query("DELETE FROM `hvz_score_copy` WHERE 1");
	
	// Ratify those scores
	$sql->query("UPDATE `hvz_stuns` SET `ratified`=NOW() WHERE `ratified` IS NULL");
}*/

function get_active_stuns( )
{
	global $sql;
	$result = $sql->query("SELECT `hvz_stuns`.`id`, `time`, `comment`, A.`first_name` AS `killer_first`, A.`last_name` AS `killer_last`, B.`first_name` AS `victim_first`, B.`last_name` AS `victim_last`, C.`first_name` as `helper_first`, C.`last_name` AS `helper_last` FROM `hvz_stuns` INNER JOIN `hvz_players` AS A ON A.`id` = `hvz_stuns`.`killer` INNER JOIN `hvz_players` AS B ON B.`id` = `hvz_stuns`.`victim` LEFT JOIN `hvz_players` AS C ON C.`id` = `hvz_stuns`.`helper` WHERE `hvz_stuns`.`ratified` IS NULL ORDER BY `hvz_stuns`.`time` ASC");
	
	$results = array();
	while( $row = $result->fetch_assoc() )
	{
		array_push($results, $row);
	}
	return $results;
}

function add_half_day( $stunid )
{
	global $sql;
	$stunid = $sql->real_escape_string($stunid);
	$sql->query("UPDATE `hvz_stuns` SET `time`=ADDTIME(`time`,MAKETIME(12,0,0)) WHERE `id`='$stunid'");
}

function h_sline($line)
{
	// get each comma separated entry
	$entry = explode(",", $line);
	
	// Create a comma separated string of values
	return "'" . implode("','", $entry) . "'";
}

function process_stun_csv($data)
{
	// get each line
	$lines = explode("\n", $data);
	
	// Process entries into SQL insert entries
	$entries = array_map("h_sline", $lines);
	
	// make a string combining them all
	$str = "(" . implode("), (", $entries) . ")";
	
	global $sql;
	
	$result = $sql->query("INSERT INTO `hvz_stuns` (`killer`, `victim`, `time`, `comment`) VALUES " . $str . ";");
	if( !$result ) return FALSE;
	return TRUE;
}
