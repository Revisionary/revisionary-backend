<?php
use \DrewM\MailChimp\MailChimp;

// If already logged in, go Projects page
if ( userLoggedIn() ) {

	if (get('redirect') != "") {
		header("Location: ".htmlspecialchars_decode(get('redirect'))); // !!! Check security
		die();
	}

	header('Location: '.site_url('projects'));
	die();

}


$eMail = "";
$fullName = "";
$nonceError = $emptyError = $mailError = $mailExistError = $nameError = $dbError = false;
$errors = [];

// If submitted
if ( post('user-submit') == "Register" ) {


	// if ( post("nonce") !== $_SESSION["signup_nonce"] ) {
	// 	$nonceError = true;
	// 	$errors[] = "Please try again";
	// }


	$eMail = post('email');
	$fullName = post('full_name');
	$password = post('password');
	$trial = post('trial');
	$redirect_to = !empty(post('redirect_to')) ? htmlspecialchars_decode(post('redirect_to')) : site_url('projects?welcome');


	// Check if any empty field
	if (!$nonceError && (empty($eMail) || empty($fullName) || empty($password)) ) {
		$emptyError = true;
		$errors[] = "Please don't leave fields blank";
	}


	// Email check
	if (!$nonceError && !$emptyError && !filter_var($eMail, FILTER_VALIDATE_EMAIL)) {
		$mailError = true;
		$errors[] = "Invalid email format";
	}


	// Check if e-mail already exists
	if (!$nonceError && !$emptyError && !$mailError) {
		if ( !checkAvailableEmail($eMail) ) {
			$mailExistError = true;
			$errors[] = "This e-mail address is already registered, please login or <a href='".site_url('lost-password')."'>reset</a> your password.";
		}
	}


	// Full name check
	if (!$nonceError && !$emptyError && !$mailError && !$mailExistError && !preg_match("/^[\p{Latin}[A-Za-z ]+$/u", $fullName)) {
		$nameError = true;
		$errors[] = "Only letters and white space allowed on your name";
	}


	// Password check !!!


	// If no errors
	if( $errors == [] ) {


		$trial_level_ID = null;
		if ( !empty($trial) ) {

			$db->where('user_level_name', $trial);
			$db->where('user_level_ID', [1, 2], 'NOT IN');
			$trial_level = $db->getOne('user_levels');

			if ($trial_level) {
				
				$trial_level_ID = $trial_level['user_level_ID'];
				$redirect_to = removeQueryArg("welcome", $redirect_to);
				$redirect_to = queryArg("trialstarted", $redirect_to);
			
			}

		}


		// Add the user
		$user_ID = User::ID('new')->addNew(
		    $eMail,
		    $fullName,
			$password,
			$trial_level_ID
		);


		// If successful
		if ($user_ID) {


			// Create the session
			$_SESSION['user_ID'] = $user_ID;


			// Update the shares
			$db->where('share_to', $eMail);
			$db->update ('shares', array(
				'share_to' => $user_ID
			));


			// Do the subscription
			$MailChimp = new MailChimp('cf46d8fcb49efd7ebbe51f02aec3ec58-us17');
			$list_id = '74f14f8baf'; // Global List

			$result = $MailChimp->post("lists/$list_id/members", [
				'email_address' => $eMail,
				'status'        => 'subscribed'
			]);


			header("Location: $redirect_to");
			die();

		}


		// If not inserted
		$dbError = true;
		$errors[] = "Registration failed: ".$db->getLastError();

	}

}


// Generate new nonce for form !!!
$_SESSION["signup_nonce"] = uniqid(mt_rand(), true);


$page_title = "Signup - Revisionary App";
require view('signup');