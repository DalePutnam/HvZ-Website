<?php
session_start();

require_once("require/secure.php");
Secure( false );
header("Location: panel.php");
exit();
?>