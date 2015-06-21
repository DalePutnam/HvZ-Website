<?php
require_once(dirname(__FILE__) . "/sql.php");
require_once(dirname(__FILE__) . "/rgame.php");

// Global email replacement array
$email_replace = array(
	"hvz_site" => "http://watsfic.uwaterloo.ca/hvz",
	"now" => date('l F jS \a\t g:iA'),
	"game_start" => date('l F jS \a\t g:iA', strtotime(get_game_start())),
	"game_end" => date('l F jS \a\t g:iA', strtotime(get_game_end())),
	"reg_start" => date('l F jS \a\t g:iA', strtotime(get_reg_start())),
	"reg_end" => date('l F jS \a\t g:iA', strtotime(get_reg_end())),
);

function add_mail($to, $subject, $body)
{
	global $sql;
	$to = $sql->real_escape_string($to);
	$subject = $sql->real_escape_string($subject);
	$body = $sql->real_escape_string($body);
	
	$sql->query("INSERT INTO `hvz_mail` (`to`, `subject`, `body`, `date`) VALUES ('$to', '$subject', '$body', NOW())");
}

function hvzmail($to, $subject, $body)
{
	// keep SQL record for admin purposes
	add_mail($to, $subject, $body);
	
	// temp: send to watsfic for delivery
	/*$to = urlencode($to);
	$subject = urlencode($subject);
	$body = urlencode($body);
	file_get_contents("http://watsfic.uwaterloo.ca/hvz/mail.php?to=$to&subject=$subject&body=$body");*/
	mail($to, $subject, $body, "From: uwhumansvszombies@gmail.com\r\nReply-To: uwhumansvszombies@gmail.com");
}
function hvzmailf($to, $template, $args)
{
	global $templates_dir;
	global $email_replace;
	
	// load template
	$body = file_get_contents($templates_dir . $template . ".txt");
	
	// load in common replacement strings
	$args = array_merge( $args, $email_replace );
	
	// replace all format strings
	foreach( $args as $key=>$value )
	{
		$body = str_replace( "{" . $key . "}", $value, $body );
	}
	
	// Use first line as subject
	$firstnl = strpos($body, "\n");
	$subject = substr($body, 0, $firstnl);
	$body = substr($body, $firstnl+1);
	
	// send
	hvzmail($to, $subject, $body);
}
/*
function hvzmail($to, $subject, $body)
{
}
*/
