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
        if (!IsAdmin())
        {
            $title = "";
            $body = "";

            if (IsHuman()) {
                $title = htmlentities($active[0]["human_title"]);
                $body = htmlentities($active[0]["human_body"]);
            } elseif (IsZombie()) {
                $title = htmlentities($active[0]["zombie_title"]);
                $body = htmlentities($active[0]["zombie_body"]);
            }

            $body = preg_replace('/{/', '<a href="', $body);
            $body = preg_replace('/\|/', '">', $body);
            $body = preg_replace('/}/', '</a>', $body);
            $body = preg_replace('/\n(\s*\n)+/', '</p><p>', $body);
            $body = preg_replace('/\n/', '<br>', $body);
            $body = '<p>' . $body . '</p>';

            echo "<strong>$title</strong><br/>";
            echo "<div>$body</div>";
        }
        else
        {
            $human_title = htmlentities($active[0]["human_title"]);
            $human_body = htmlentities($active[0]["human_body"]);
            $zombie_title = htmlentities($active[0]["zombie_title"]);
            $zombie_body = htmlentities($active[0]["zombie_body"]);

            $human_body = preg_replace('/{/', '<a href="', $human_body);
            $human_body = preg_replace('/\|/', '">', $human_body);
            $human_body = preg_replace('/}/', '</a>', $human_body);
            $human_body = preg_replace('/\n(\s*\n)+/', '</p><p>', $human_body);
            $human_body = preg_replace('/\n/', '<br>', $human_body);
            $human_body = '<p>' . $human_body . '</p>';

            $zombie_body = preg_replace('/{/', '<a href="', $zombie_body);
            $zombie_body = preg_replace('/\|/', '">', $zombie_body);
            $zombie_body = preg_replace('/}/', '</a>', $zombie_body);
            $zombie_body = preg_replace('/\n(\s*\n)+/', '</p><p>', $zombie_body);
            $zombie_body = preg_replace('/\n/', '<br>', $zombie_body);
            $zombie_body = '<p>' . $zombie_body . '</p>';

            echo "<strong>$human_title</strong><br/>";
            echo "<div>$human_body</div>";
            echo "<strong>$zombie_title</strong><br/>";
            echo "<div>$zombie_body</div>";
        }
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
