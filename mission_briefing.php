<?php  session_start();
require_once("require/basic.php");
require_once("require/secure.php");
require_once("require/rbriefing.php");

Secure(false);

$active = get_active_briefings();
$queued;
$message;
$status;

if (count($active) > 1)
{
    $message = "Error: More than one currently active briefing";
    $status = "ERROR";
}
elseif (count($active) == 1)
{
    $status = "ACTIVE";
}
else
{
    $queued = get_queued_briefings();
    if (count($queued) > 0)
    {
        $status = "QUEUED";
    }
    else
    {
        $status = "NONE";
    }
}

page_head();
?>
<h2>Mission Briefing</h2>
<?php
    if ($status == "ERROR")
    {
        echo "<strong>$message</strong>";
    }
    elseif ($status == "ACTIVE")
    {
        $title = htmlentities($active[0]["title"]);
        $body = htmlentities($active[0]["body"]);

        $body = preg_replace('/\n(\s*\n)+/', '</p><p>', $body);
        $body = preg_replace('/\n/', '<br>', $body);
        $body = '<p>'.$body.'</p>';

        echo "<strong>$title</strong><br/>";
        echo "<div>$body</div>";
    }
    elseif ($status == "QUEUED")
    {
        $datetime = $queued[0]["release_time"];
        $date = substr($datetime, 0, 11);
        $time = substr($datetime, 11, 5);
        echo "<strong>The next briefing will be released at $time on $date</strong>";
    }
    else
    {
        echo "<strong>There are currently no mission briefings scheduled. Check back later</strong>";
    }
?>
<?php
page_foot();
?>
