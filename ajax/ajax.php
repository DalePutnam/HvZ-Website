<?php
function result( $success, $data = NULL )
{
	$result = array( "success" => $success, "data" => $data );
	echo json_encode( $result );
	exit();
}

if( !isset( $_GET ) ) result( false );
if( !isset( $_GET["action"] ) result( false );

// todo ensure permissions
// todo enable actions here
?>