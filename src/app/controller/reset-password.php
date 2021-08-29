<?php


// If already logged in, go projects page
if ( userLoggedIn() ) {

	if (get('redirect') != "") {
		header("Location: ".htmlspecialchars_decode(get('redirect'))); // !!! Check security
		die();
	}

	header('Location: '.site_url('projects'));
	die();

}



// Check for tokens
$email = get('email');
$token = get('token');



// Token and email type check
if ( false === ctype_xdigit( $token ) || !filter_var($email, FILTER_VALIDATE_EMAIL) ) {

	die('There was an error processing your request. Error Code: 001');

}



// Reset errors
$nonceError = $emptyError = $confirmationError = $userError = $tokenError = $validationError = $dbError = false;
$errors = [];



// If submitted
if ( post('reset-password-submit') == "Update Password" ) {


/*
	// Check the nonce
	if ( post("nonce") !== $_SESSION["login_nonce"] ) {
		$nonceError = true;
		$errors[] = "Please try again";
	}
*/


	$password1 = post('password');
	$password2 = post('password_confirm');


	// Check if any empty field
	if ( !$nonceError && (empty($password1) || empty($password2)) ) {
		$emptyError = true;
		$errors[] = "Please don't leave the field blank";
	}



	// Confirmation check
	if (!$nonceError && !$emptyError && $password1 != $password2) {
		$confirmationError = true;
		$errors[] = "Passwords don't match. Please re-enter:";
	}



	// User check
	if (!$nonceError && !$emptyError && !$confirmationError) {


		// Get the user
		$db->where("user_email", $email);
		$user = $db->getOne("users");


		// User info
		$userInfo = $user;
		$user_ID = $userInfo["user_ID"];


		if ($user === null) {
			$userError = true;
			$errors[] = "We couldn't find your account here. Would you like to <a href='".site_url("signup")."'><b>signup</b></a>?";
		}


	}



	// Token check
	if (!$nonceError && !$emptyError && !$confirmationError && !$userError) {


		// Get tokens
		$db->where( "user_ID = $user_ID AND pass_reset_expires >= ".time() );
		$result = $db->getOne("password_reset");


		// Find the
		if ( $result === false ) {
			$tokenError = true;
		    $errors[] = "There was an error processing your request. Error Code: 002";
		}


	}



	// Validate token
	if (!$nonceError && !$emptyError && !$confirmationError && !$userError && !$tokenError) {


		$auth_token = $result['pass_reset_token'];
		$calc = hash('sha256', hex2bin($token));


		if ( !hash_equals($calc, $auth_token) )  {
			$validationError = true;
		    $errors[] = "There was an error processing your request. Error Code: 003";
		}


	}



	// If no error above
	if ($errors == []) {


		// Update password
		$db->where('user_ID', $user_ID);
		$update_password = $db->update('users', array(
			'user_password' => password_hash($password1, PASSWORD_DEFAULT)
		));


		// Delete any existing tokens for this user
		$db->where('user_ID', $user_ID);
		$db->delete('password_reset');


		// If successful
		if ($update_password) {


			// Signout
			//session_destroy();


			// Sign in again
			$_SESSION['user_ID'] = $user_ID;


			// Send email
			Notify::ID($user_ID)->mail(
				"Your password changed!",
				"Hi ".getUserInfo($user_ID)['fullName'].", <br><br>

				Your password has been changed now. If you haven't done this action, please contact us at info@revisionaryapp.com <br><br>

				Thank you, <br>
				Revisionary App Team",
				true // Important
			);


			// Site Log
			$log->info("User #$user_ID Changed Password: ".$userInfo["user_name"]."(".getUserInfo($user_ID)['fullName'].") | Email: ".$userInfo["user_email"]." | User Level ID #".$userInfo["user_level_ID"]."");


			// Notify admin
			Notify::ID(1)->mail(
				"Password Changed for ".$userInfo['user_first_name']." ".$userInfo['user_last_name'],
				"
				<b>User Information</b> <br>
				Email: ".$userInfo['user_email']." <br>
				Full Name: ".$userInfo['user_first_name']." ".$userInfo['user_last_name']." <br>
				Username: ".$userInfo['user_name']."
				"
			);


			// Redirect to message
			header("Location: ".site_url('projects?password-changed'));
			die();

		}

		// If not inserted
		$dbError = true;
		$errors[] = "Password reset failed: ".$db->getLastError();

	}

}


// Generate new nonce for form
$_SESSION["login_nonce"] = uniqid(mt_rand(), true);


$page_title = "Reset Password - Revisionary App";
require view('reset-password');