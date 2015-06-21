<?php
/*
	Anti-Hammer Test Page.
*/
require("anti-hammer.php");
echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Anti-Hammer Test Page</title>
<meta name="description" content="anti-hammer test Page" />
</head>
<body>
<h1>
	<a href="http://corz.org/serv/tools/anti-hammer/"
	title="Protect your site from hammers, spammers, h4x0rz and more.." id="link-Anti-Hammer">Anti-Hammer</a> Test Page
</h1>
<h2>Hit REFRESH (usually &lt;F5&gt;) a few times, quickly..</h2>
<br />

<pre>
<strong title="The User Agent string your browser used to identify itself">User Agent</strong>: ',$_SERVER['HTTP_USER_AGENT'],'

<strong title="Two requests within this time triggers Anti-Hammer">Hammer Time</strong>: ',$anti_hammer['hammer_time'] / 100,' seconds

<strong title="The number of times you have activated Anti-Hammer">Hammer Count</strong>: ',@$anti_hammer['session']['hammer'],@$session['hammer'],'

<strong title="The waiting time increases at each of the trigger levels (number of times you have tripped the Anti-Hammer mechanism)">Trigger Levels</strong>: ',
$anti_hammer['trigger_levels'][0],', ',
$anti_hammer['trigger_levels'][1],', ',
$anti_hammer['trigger_levels'][2],', ',
$anti_hammer['trigger_levels'][3],'

<strong title="How long, in seconds, you must wait before trying again. This increases at each trigger level (above)">Waiting Times</strong>: ',
$anti_hammer['waiting_times'][0],', ',
$anti_hammer['waiting_times'][1],', ',
$anti_hammer['waiting_times'][2],', ',
$anti_hammer['waiting_times'][3],'

</pre><br />
</body>
</html>';
?>