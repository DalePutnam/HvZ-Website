<?php
session_start();
require_once("require/secure.php");
require_once("require/basic.php");
Secure(true);

page_head();
?>
<h1>E-Mail Log</h1>
<p>This page records every e-mail sent by the website</p>
<a id="showall" href='#'>Show All Comments</a>&nbsp;<a id="hideall" href='#'>Hide All Comments</a>
<table class="mail">
<tr><th>To</th><th>Time</th><th>Subject</th><th style="width: 120px;">Body</th></tr>
<?php
$result = $sql->query("SELECT `to`, `subject`, `body`, `date` FROM `hvz_mail` ORDER BY `date` ASC");
while( $row = $result->fetch_assoc() )
{
	$body = str_replace("\n", "<br/>", $row['body']);
	echo "<tr><td>{$row['to']}</td><td>{$row['date']}</td><td>{$row['subject']}</td><td class='body'><a href='#'>Show/Hide</a><p class='body'>{$body}</p></td></tr>";
}
?>
</table>
<script type="text/javascript">
$("td.body a").click( function() {
	$("~p.body", this).toggle();
	return false;
});
$("p.body").hide();
$("a#showall").click( function() {
	$("p.body").show();
	return false;
});
$("a#hideall").click( function() {
	$("p.body").hide();
	return false;
});
</script>
<?php
page_foot();
?>