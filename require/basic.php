<?php
require_once(dirname(__FILE__) . "/config.php");

$alert_level = "NONE";
$alert_message = "";

function page_archive_head()
{
    require_once(dirname(__FILE__) . "/../anti-hammer/anti-hammer.php");
    ?>
    <html>
    <head>
        <title>Humans vs. Zombies</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
        <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>-->
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script src="../js/jquery-ui-timepicker-addon.js"></script>
        <script src="../js/players.js"></script>
        <script src="../js/players2.js"></script>
        <script src="../js/graph.js"></script>
        <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">-->
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="../style.css">
    </head>
    <body>
    <?php
    require_once(dirname(__FILE__) . "/rarchive.php");
    $archive = -1;
    if(isset($_REQUEST["archive"]))
    {
        $archive = intval($_REQUEST["archive"]);
        if(!is_archive($archive)) $archive = -1;
    }

    if($archive == -1)
    {
        ?>
        <strong style="red">Invalid archive.</strong>
    <?php
        page_archive_foot();
        exit();
    }
    return $archive;
}

function page_archive_foot()
{
    page_unsecure_foot();
}

function page_unsecure_head()
{
	require_once(dirname(__FILE__) . "/../anti-hammer/anti-hammer.php");
	?>
	<html>
	<head>
	<title>Humans vs. Zombies</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>-->
    <script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="js/jquery-ui-timepicker-addon.js"></script>
	<script src="js/players.js"></script>
	<script src="js/players2.js"></script>
	<script src="js/graph.js"></script>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">-->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <?php include("header.php"); ?>
        </div>
    </nav>

    <div class="container-fluid">
    <div class="row">
    <div class="col-sm-2 col-md-2 sidebar">
        <?php include("sidebar.php"); ?>
    </div>
    <div class="col-xs-12 col-sm-10 col-md-10 col-sm-offset-2 col-md-offset-2 main">

	<?php
    write_response();
    show_alert();
}
function page_unsecure_foot()
{
	?>
    </div>
    </div>
    </div>
    </body>
    </html>
	<?php
}
function page_head()
{
	require_once("anti-hammer/anti-hammer.php");
	?>
	<html>
	<head>
	<title>Humans vs. Zombies</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>-->
    <script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="js/jquery-ui-timepicker-addon.js"></script>
	<script src="js/players.js"></script>
	<script src="js/players2.js"></script>
	<script src="js/graph.js"></script>
    <script src="js/table.js"></script>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">-->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>

	<!--<table>
	<tr>
	<td colspan="2">-->
	<!-- Header -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
    <?php include("header.php"); ?>
    </div>
    </nav>
	<!--</td>
	</tr>
	<tr>
	<td style="width:150px;vertical-align:top;">-->
	<!-- Sidebar -->
    <div class="container-fluid">
    <div class="row">
    <div class="col-sm-2 col-md-2 sidebar">
    <?php include("sidebar.php"); ?>
    </div>
    <div class="col-xs-12 col-sm-10 col-md-10 col-sm-offset-2 col-md-offset-2 main">
    <?php if( is_maintenance() && !Unsecure() ) { ?>
    <p style='margin-top: 15px; height: 25px; padding: 0; color: red'>MAINTENANCE MODE ENABLED. SEE <a style='padding: 0; display: inline' href='game.php'>GAME SETTINGS</a> PAGE FOR MORE INFO</p>
    <?php } ?>
	<!--</td>
	<td style="vertical-align:top;width=100%;">-->
	<?php
	//write_response();
    show_alert();
}

function write_response()
{
	if( isset( $_REQUEST["response"] ) )
	{
		$type = "red";
		if( isset( $_REQUEST["response_type"] ) )
		{
			$type = $_REQUEST["response_type"];
		}
		echo "<div class='response'><strong style='color:$type;'>" . $_REQUEST["response"] . "</strong></div>";
	}
}

function page_foot()
{
	?>
	<!--</td></tr></table>-->
	</div>
    </div>
    </div>
	</body>
	</html>
	<?php
}

function reload_self($response=NULL)
{
	if( $response == NULL)
	{
		header("Location: " . $_SERVER["PHP_SELF"]);
	}
	elseif( is_string($response) )
	{
		$type = substr($response, 0, 1);
		if( $type == "&" )
		{
			$response = substr($response, 1);
			$type = "red";
		}
		else $type = "green";
		header("Location: " . $_SERVER["PHP_SELF"] . "?response=" . urlencode($response) . "&response_type=$type");
	}
	elseif( is_array($response) )
	{
		header("Location: " . $_SERVER["PHP_SELF"] . "?" . http_build_query($response));
	}
	exit();
}

function to_panel($response=NULL)
{
	if( $response == NULL)
	{
		header("Location: panel.php");
	}
	else
	{
		$type = substr($response, 0, 1);
		if( $type == "&" )
		{
			$response = substr($response, 1);
			$type = "red";
		}
		else $type = "green";
		header("Location: panel.php?response=" . urlencode($response) . "&response_type=$type");
	}
	exit();
}

function to_waiver()
{
    header("Location: waiver.php");
}

function logout($response=NULL)
{
    unset($_SESSION["user"]);
    if( $response == NULL )
    {
        header("Location: login.php?logout=");
    }
    else
    {
        $type = substr($response, 0, 1);
        if( $type == "&" )
        {
            $response = substr($response, 1);
            $type = "red";
        }
        else $type = "green";
        header("Location: login.php?logout=&response=" . urlencode($response) . "&response_type=$type");
    }
    exit();
}

function set_alert($level, $message)
{
    global $alert_level;
    global $alert_message;

    $alert_level = $level;
    $alert_message = $message;
}

function show_alert()
{
    global $alert_level;
    global $alert_message;

    if ($alert_level == "SUCCESS")
    {
    ?>
        <div style="margin-bottom: 0; margin-top: 10px" class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Success!</strong> <?php echo $alert_message; ?>
        </div>
    <?php
    }
    elseif ($alert_level == "ERROR")
    {
    ?>
        <div style="margin-bottom: 0; margin-top: 10px" class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Error!</strong> <?php echo $alert_message; ?>
        </div>
    <?php
    }
}
