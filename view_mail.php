<?php
session_start();
require_once("require/secure.php");
require_once("require/basic.php");
Secure(true);

page_head();
?>
<h2>E-Mail Log</h2>
<p>This page records every e-mail sent by the website</p>
<a id="showall" href='#'>Show All Comments</a>&nbsp;<a id="hideall" href='#'>Hide All Comments</a>
<div class="row">
<div class="col-md-12">
<table class="mail table table-bordered table-condensed">
<tr><th>To</th><th>Time</th><th>Subject</th><th style="width: 120px;">Body</th></tr>
<?php
$result = $sql->query("SELECT `to`, `subject`, `body`, `date` FROM `hvz_mail` ORDER BY `date` ASC");
$num = 0;
while( $row = $result->fetch_assoc() )
{
	$body = str_replace("\n", "<br/>", $row['body']);
    //<p class='body'>{$body}</p>
	echo "<tr><td>{$row['to']}</td><td>{$row['date']}</td><td>{$row['subject']}</td><td class='body'><a id='body$num' href='#'>Show/Hide</a></td></tr>";
    echo "<tr><td style='padding: 0' colspan='4'>" ;
    echo "<div style='margin: 5px' id='collapse$num' class='collapse'><div style='margin-bottom: 0' class='well'>{$body}</div></div>";
    echo "</td></tr>";
    $num = $num + 1;
}
?>
</table>
</div>
</div>
<script type="text/javascript">
$("td.body a").click( function() {
    var idNum = $(this).attr('id').substr(4);
    $("#collapse" + idNum).collapse("toggle");
	return false;
});
//$("p.body").hide();
$("a#showall").click( function() {
	$("div.collapse").collapse("show");
	return false;
});
$("a#hideall").click( function() {
	$("div.collapse").collapse("hide");
	return false;
});
</script>
<?php
page_foot();
?>