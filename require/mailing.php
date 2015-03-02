<?php
require_once(dirname(__FILE__) . "/Mailman.php");
require_once(dirname(__FILE__) . "/config.php");

function makeRequest()
{
	$request = new HTTP_Request2();
	$request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
	return $request;
}

function setup_subscription_by_type($email, $type, $subbit=TRUE)
{
	if($type == "HUMAN") setup_subscription($email, $subbit, TRUE, FALSE);
	if($type == "ZOMBIE") setup_subscription($email, $subbit, FALSE, TRUE);
	if($type == "ADMIN") setup_subscription($email, $subbit, TRUE, TRUE);
	if($type == "BANNED") setup_subscription($email, FALSE, FALSE, FALSE);
	if($type == "NONE") setup_subscription($email, $subbit, FALSE, FALSE);
	if($type == "SPECTATE") setup_subscription($email, $subbit, TRUE, TRUE);
}

function remove_subscription($email)
{
	setup_subscription($email, FALSE, FALSE, FALSE);
}

function setup_subscriptions($emails, $all, $human, $zombie, $unsub = true)
{
	$email = implode( "\n", $emails );
	setup_subscription($email, $all, $human, $zombie, $unsub);
}

function clear_subscriptions()
{
    if( !use_mailing_lists() ) return;

    global $mailing_list_password;
    global $mailing_list_root;

	$request = makeRequest();
	$password = $mailing_list_password;
	$site = $mailing_list_root;
	$mm = new Services_Mailman($site, "hvz", $password, $request);
	$members = $mm->members();
	if( count($members[0]) > 0 )
		$mm->unsubscribe( implode( "\n", $members[0] ) );
	$mm = new Services_Mailman($site, "hvz-humans", $password, $request);
	$members = $mm->members();
	if( count($members[0]) > 0 )
		$mm->unsubscribe( implode( "\n", $members[0] ) );
	$mm = new Services_Mailman($site, "hvz-zombies", $password, $request);
	$members = $mm->members();
	if( count($members[0]) > 0 )
		$mm->unsubscribe( implode( "\n", $members[0] ) );
}

function fix_subscriptions($all, $admins, $humans, $zombies, $spectators)
{
	clear_subscriptions();
	if( count($all) > 0 )
		setup_subscriptions($all, true, false, false, false);
	if( count($humans) > 0 )
		setup_subscriptions(array_merge($spectators, $humans), false, true, false, false);
	if( count($zombies) > 0 )
		setup_subscriptions(array_merge($admins, $zombies, $spectators), false, false, true, false);
}

function setup_subscription($email, $all, $human, $zombie, $unsub = true)
{
    if( !use_mailing_lists() ) return;

    global $mailing_list_password;
    global $mailing_list_root;

	$request = makeRequest();
	$password = $mailing_list_password;
	$site = $mailing_list_root;
	$mm = new Services_Mailman($site, "hvz", $password, $request);
	try
	{
		if( $all ) $mm->subscribe($email);
		elseif( $unsub ) $mm->unsubscribe($email);
	}
	catch(Services_Mailman_Exception $e) {}
	$mm = new Services_Mailman($site, "hvz-humans", $password, $request);
	try
	{
		if( $human ) $mm->subscribe($email);
		elseif( $unsub ) $mm->unsubscribe($email);
	}
	catch(Services_Mailman_Exception $e) { }
	$mm = new Services_Mailman($site, "hvz-zombies", $password, $request);
	try
	{
		if( $zombie ) $mm->subscribe($email);
		elseif( $unsub ) $mm->unsubscribe($email);
	}
	catch(Services_Mailman_Exception $e) {}
}
