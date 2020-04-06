<?php

$subpage = isset($_url[1]) && is_string($_url[1]) ? $_url[1] : false;
$user_ID = getUserInfo()['userLevelID'] == 1 && isset($_GET['user']) && is_numeric($_GET['user']) ? intval($_GET['user']) : currentUserID();
$userInfo = getUserInfo($user_ID);
$userInfoDB = getUserInfoDB($user_ID, true, true);
//die_to_print($userInfoDB);


$current_plan_name = $userInfo['trialActive'] ? $userInfo['trialUserLevelName'] : $userInfo['userLevelName'];


// If user not exist
if (!$userInfo) die('User Not Found.');


if ( post('update-submit') == "Update" ) {


	// Check the name field
	if ( empty(post('user_full_name')) ) {

		header('Location: '.current_url("error=name", "successful"));
		die();

	}


	// Full name check
	if ( !preg_match("/^[\p{Latin}[A-Za-z ]+$/u", post('user_full_name')) ) {

		header('Location: '.current_url("error=invalidname", "successful"));
		die();

	}


	// Parse the full name
	$user_full_name = post('user_full_name');
	$firstName = $user_full_name;
	$lastName = "";
	$parsedFullName = explode(' ', $user_full_name);
	if (count($parsedFullName) > 1) {
		$firstName = str_replace(' '.end($parsedFullName), '', $user_full_name);
		$lastName = end($parsedFullName);
	}


	// Update on DB
	$db->where('user_ID', $user_ID);
	$updated = $db->update('users', array(
		"user_first_name" => $firstName,
		"user_last_name" => $lastName,
		"user_job_title" => post('user_job_title'),
		"user_department" => post('user_department'),
		"user_company" => post('user_company')
	));

	if (!$updated) {

		header('Location: '.current_url("error=unknown", "successful"));
		die();

	}


	// INVALIDATE THE CACHE
	$cache->delete('user:'.$user_ID);


	header('Location: '.current_url("successful", "error"));
	die();


} elseif ( post('password-submit') == "Update" ) {


	// Empty fields check
	if ( empty(post('current_password')) || empty(post('new_password')) || empty(post('new_password_confirmation')) ) {

		header('Location: '.current_url("error=fieldsempty", "successful"));
		die();

	}


	// Current password check
	if ( !password_verify( post('current_password') , $userInfoDB["user_password"]) ) {

		header('Location: '.current_url("error=password", "successful"));
		die();

	}


	// Password confirmation check
	if ( post('new_password') != post('new_password_confirmation') ) {

		header('Location: '.current_url("error=passconfirm", "successful"));
		die();

	}


	// Update on DB
	$db->where('user_ID', $user_ID);
	$updated = $db->update('users', array(
		'user_password' => password_hash(post('new_password'), PASSWORD_DEFAULT)
	));

	if (!$updated) {

		header('Location: '.current_url("error=unknown", "successful"));
		die();

	}


	// INVALIDATE THE CACHE
	$cache->delete('user:'.$user_ID);


	header('Location: '.current_url("successful", "error"));
	die();


} elseif ( post('email-submit') == "Update" ) {


	// Empty field check
	if ( empty(post('user_email')) ) {

		header('Location: '.current_url("error=fieldsempty", "successful"));
		die();

	}


	// Email validate
	if ( !filter_var(post('user_email'), FILTER_VALIDATE_EMAIL) ) {

		header('Location: '.current_url("error=invalidemail", "successful"));
		die();

	}


	// Email difference
	if ( $userInfo['email'] != post('user_email') ) {


		// Check current password empty
		if ( empty(post('current_password')) ) {

			header('Location: '.current_url("error=password", "successful"));
			die();

		}


		// Check current password
		if ( !password_verify( post('current_password') , $userInfoDB["user_password"]) ) {

			header('Location: '.current_url("error=password", "successful"));
			die();

		}


		// Check email available
		if ( !checkAvailableEmail(post('user_email')) ) {

			header('Location: '.current_url("error=email", "successful"));
			die();

		}


	}


	// Update on DB
	$db->where('user_ID', $user_ID);
	$updated = $db->update('users', array(
		'user_email' => post('user_email'),
		'user_email_notifications' => post('user_email_notifications') == "yes" ? 1 : 0
	));

	if (!$updated) {

		header('Location: '.current_url("error=unknown", "successful"));
		die();

	}


	// INVALIDATE THE CACHE
	$cache->delete('user:'.$user_ID);


	// Email notification
	if ( $userInfo['email'] != post('user_email') ) {


		// Site log
		$log->info("User #$user_ID Updated Email: ".$userInfo['userName']."(".$userInfo['fullName'].") | Old Email: ".$userInfo['email']." | New Email: ".post('user_email')." | User Level ID #".$userInfo['userLevelID']);


		// Send email
		Notify::ID($userInfo['email'])->mail(
			"Your email has been changed!",
			"Hi ".$userInfo['fullName'].", <br><br>

			Your email address has been changed now. <br>
			Your new email address: ".post('user_email')." <br><br>

			If you haven't done this action, please contact us at info@revisionaryapp.com <br><br>

			Thank you, <br>
			Revisionary App Team",
			true // Important
		);


		// Send email
		Notify::ID(post('user_email'))->mail(
			"Your email has been changed!",
			"Hi ".$userInfo['fullName'].", <br><br>

			Your email address has been changed now. <br>
			Your new email address: ".post('user_email')." (Old Email: ".$userInfo['email'].") <br><br>

			If you haven't done this action, please contact us at info@revisionaryapp.com <br><br>

			Thank you, <br>
			Revisionary App Team",
			true // Important
		);


		// Notify the admin
		Notify::ID(1)->mail(
			"User changed email address ".$userInfo['fullName'],
			"
			<b>User Information</b> <br>
			Old Email: ".$userInfo['email']." <br>
			New Email: ".post('user_email')." <br>
			Full Name: ".$userInfo['fullName']." <br>
			Username: ".$userInfo['userName']."
			"
		);

	}




	header('Location: '.current_url("successful", "error"));
	die();

}



// Additional Scripts and Styles
$additionalCSS = [];

$additionalHeadJS = [
	'common.js'
];

$additionalBodyJS = [];

$page_title = "My Account - Revisionary App";
require view('account');