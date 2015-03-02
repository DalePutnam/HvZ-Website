<?php
require_once(dirname(__FILE__) . "/rplayers.php");
require_once(dirname(__FILE__) . "/rgame.php");
require_once(dirname(__FILE__) . "/basic.php");

function First() { return $_SESSION["user"]["first_name"]; }
function Last() { return $_SESSION["user"]["last_name"]; }
function Email() { return $_SESSION["user"]["email"]; }
function Code() { return $_SESSION["user"]["code"]; }
function Type() { return $_SESSION["user"]["type"]; }
function Name() { return First() . " " . Last(); }
function ID() { return $_SESSION["user"]["id"]; }
function PasswordCrypt() { return $_SESSION["user"]["password_crypt"]; }
function Score() { return $_SESSION["user"]["score"]; }
function Bonus() { return $_SESSION["user"]["bonus_score"]; }
function ShowScore() { return $_SESSION["user"]["show_score"] == 1; }
function HasSignedWaiver() { return $_SESSION["user"]["waiver"] == 1; }
function IsSubscribed() { return $_SESSION["user"]["subscribe"] == 1; }

function IsPlayer() { return Type() == "HUMAN" || Type() == "ZOMBIE" || Type() == "NONE"; }
function IsAdmin() { return Type() == "ADMIN"; }
function NotBanned() { return Type() != "BANNED"; }
function IsZombie() { return Type() == "ZOMBIE"; }
function IsHuman() { return Type() == "HUMAN"; }
function IsSpectator() { return Type() == "SPECTATE"; }

// Used for print-friendly names
function Team()
{
	$type = Type();
    return TeamName($type);
}

/**
 * @param $type
 * @return string
 */
function TeamName($type)
{
    if ($type == "ZOMBIE") return "Zombie";
    elseif ($type == "HUMAN") return "Human";
    elseif ($type == "NONE") return "None";
    elseif ($type == "ADMIN") return "Admin";
    elseif ($type == "BANNED") return "Banned";
    elseif ($type == "SPECTATE") return "Spectator";
}

function Impersonate( $id )
{
	$player = get_player($id);
	if( $player == NULL ) return FALSE;
	$_SESSION["impersonate"] = $_SESSION["user"];
	$_SESSION["user"] = $player;
	to_panel("You are now impersonating {$player['first_name']} {$player['last_name']}.");
}

function GetImpersonate( )
{
	if( isset( $_SESSION["impersonate"] ) ) return $_SESSION["impersonate"];
	else return NULL;
}

function StopImpersonate( )
{
	$_SESSION["user"] = GetImpersonate();
	unset( $_SESSION["impersonate"] );
}

if( isset( $_GET["imp_end"] ) )
{
	StopImpersonate();
	to_panel("Impersonation ended.");
}

function Unsecure()
{
	if( !isset( $_POST["user"] ) && !isset( $_SESSION["user"] ) )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function Secure( $admin, $type = NULL, $is_waiver = false )
{
	if( isset( $_POST ) && isset( $_POST["user"] ) && isset( $_POST["pass"] ) )
	{
		// Ensure impersonation is killed on log-in
		unset($_SESSION["impersonate"]);
		
		$login = find_login(trim($_POST["user"]), trim($_POST["pass"]));
		if( $login == FALSE )
		{
            unset( $_SESSION["user"] );
            logout("&Invalid login.");
		}
		$_SESSION["user"] = $login;
		if( !IsAdmin() && $admin )
		{
            to_panel("&Requires administrative privileges.");
		}
		if( !IsAdmin() && is_maintenance() )
		{
            logout("&The website is currently under maintenance. Please try again later or contact a moderator.");
		}
		if( $type != NULL && Type() != $type )
		{
            to_panel("&Only a $type is allowed on the requested page");
		}
		if( !NotBanned() )
		{
            logout("&You are banned, please contact a moderator.");
		}
		if( !HasSignedWaiver() && !$is_waiver )
		{
            to_waiver();
		}
	}
	else if( isset( $_SESSION ) && isset( $_SESSION["user"] ) )
	{
		$login = find_login($_SESSION["user"]["email"], $_SESSION["user"]["password_crypt"], TRUE);
		if( $login == FALSE )
		{
            unset( $_SESSION["user"] );
            logout("&Invalid login.");
		}
		else if( !IsAdmin() && $admin )
		{
            to_panel("&Requires administrative privileges.");
		}
		if( !IsAdmin() && is_maintenance() && !isset($_SESSION["impersonate"]) )
		{
            logout("&The website is currently under maintenance. Please try again later or contact a moderator.");
		}
		else if( $type != NULL && Type() != $type )
		{
            to_panel("&Only a $type is allowed on the requested page");
		}
		else if( !NotBanned() )
		{
            logout("&You are banned, please contact a moderator.");
		}
		else
		{
			$_SESSION["user"] = $login;
		}
		if( !HasSignedWaiver() && !$is_waiver && !isset($_SESSION["impersonate"]) )
		{
			to_waiver();
		}
	}
	else
	{
        logout();
	}
}
