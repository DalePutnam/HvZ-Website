<?php
require_once(dirname(__FILE__) . "/config.php");
$sql = mysqli_connect($sql_host, $sql_user, $sql_pass, $sql_database);

function php_datetime( $sql_date )
{
    return new DateTime($sql_date);
}

function sql_date( $time )
{
	return date( 'Y-m-d H:i:s', $time );
}

function sql()
{
    global $sql;
    return $sql;
}