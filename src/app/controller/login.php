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


$userName = "";
$nonceError = $emptyError = $usernameError = $userError = $passwordError = $dbError = false;
$errors = [];


// If submitted
if ( post('login-submit') == "Login" ) {


	// // Check the nonce
	// if ( post("nonce") !== $_SESSION["login_nonce"] ) {
	// 	$nonceError = true;
	// 	$errors[] = "Please try again";
	// }


	$userName = post('username');
	$password = post('password');
	$redirect_to = !empty(post('redirect_to')) ? htmlspecialchars_decode(post('redirect_to')) : site_url('projects');


	// Check if any empty field
	if ( !$nonceError && (empty($userName) || empty($password)) ) {
		$emptyError = true;
		$errors[] = "Please don't leave fields blank";
	}



	// Username / Email validation
	if (!$nonceError && !$emptyError && !filter_var($userName, FILTER_VALIDATE_EMAIL) ) {

		if (!preg_match('/^[A-Za-z][A-Za-z0-9]*(?:-[A-Za-z0-9\-]+)*$/', $userName)) {
			$usernameError = true;
			$errors[] = "Invalid username or email format";
		}

	}


	// User check
	if (!$nonceError && !$emptyError && !$usernameError ) {

		// Username check
		$db->where("user_name", $userName);
		$db->orWhere("user_email", $userName);
		$user = $db->getOne("users");

		if ($user === null) {
			$userError = true;
			$errors[] = "Your username or password is wrong.";
		}

	}


	// Password check
	if (!$nonceError && !$emptyError && !$usernameError && !$userError && $user && !password_verify($password, $user["user_password"]) ) {

		$passwordError = true;
		$errors[] = "Your username or password is wrong";

	}


	// If no error above
	if ($errors == []) {


		// Sign in !!!
		$_SESSION['user_ID'] = $user["user_ID"];


		// Site Log
		$log->info("User #".currentUserID()." Logged In: ".getUserInfo()['userName']."(".getUserInfo()['fullName'].") | Typed: $userName | Email: ".getUserInfo()['email']." | User Level ID #".getUserInfo()['userLevelID']."");


		// Notify the admin
		if (getUserInfo()['email'] != "bilaltas@me.com" && getUserInfo()['email'] != "bill@twelve12.com") {

			Notify::ID(1)->mail(
				"New login by ".$user['user_first_name']." ".$user['user_last_name'],
				"
				<b>User Information (Typed: $userName)</b> <br>
				Email: ".$user['user_email']." <br>
				Full Name: ".$user['user_first_name']." ".$user['user_last_name']." <br>
				Username: ".$user['user_name']."
				"
			);

		}


		if ( getUserInfo()['trialActive'] ) $redirect_to = queryArg("trialreminder", $redirect_to);


		header("Location: $redirect_to");
		die();

	}


}


// Generate new nonce for form
$_SESSION["login_nonce"] = uniqid(mt_rand(), true);


$page_title = "Login - Revisionary App";
require view('login');