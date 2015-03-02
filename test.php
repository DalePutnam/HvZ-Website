<?php
error_reporting(E_ALL);
function sendit($to, $subject, $body)
{
	file_get_contents("http://watsfic.uwaterloo.ca/hvz/mail.php?to=$to&subject=$subject&body=$body");
}

sendit("brwarner2@gmail.com", "Brook", "Brooksy");
?>