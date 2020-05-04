<?php

function userLoggedIn() {
	return isset($_SESSION['user_ID']);
}

function currentUserID() {
	return userLoggedIn() ? $_SESSION['user_ID'] : 0;
}

function getUserInfoDB(int $user_ID = null, bool $nocache = false, bool $full = false) {
	global $db, $cache, $User, $UserInfo;


	$user_ID = $user_ID != null ? $user_ID : currentUserID();


	// If current user info requested
	if ( $user_ID == currentUserID() && !empty($UserInfo) ) return $UserInfo;


	// Check the cache first
	$cached_user_info = $cache->get('user:'.$user_ID);
	if ( $cached_user_info !== false && !$nocache && !$full ) return $cached_user_info;
	else { // If not exist in the cache, pull data from DB


		// Bring the user level info
		$db->join("user_levels l", "l.user_level_ID = u.user_level_ID", "LEFT");


		// Bring the trial user level info
		$db->join("user_levels t", "t.user_level_ID = u.trial_started_for", "LEFT");


		$db->where("u.user_ID", $user_ID);
		$userInfo = $db->get(
			"users u",
			null,
			($full ? null : "
				u.user_ID,
				u.user_name,
				u.user_email,
				u.user_first_name,
				u.user_last_name,
				u.user_job_title,
				u.user_department,
				u.user_company,
				u.user_picture,
				u.user_has_public_profile,
				u.user_email_notifications,
				u.user_registered,
				u.user_IP,
				u.user_level_ID,
				l.user_level_name,
				l.user_level_description,
				l.user_level_max_project,
				l.user_level_max_page,
				l.user_level_max_screen,
				l.user_level_max_live_pin,
				l.user_level_max_comment_pin,
				l.user_level_max_client,
				l.user_level_max_load,
				l.user_level_price,
				l.user_level_color,
				u.trial_started_for,
				u.trial_expire_date,
				u.trial_expired_notified,
				t.user_level_ID as trial_user_level_ID,
				t.user_level_name as trial_user_level_name,
				t.user_level_description as trial_user_level_description,
				t.user_level_max_project as trial_user_level_max_project,
				t.user_level_max_page as trial_user_level_max_page,
				t.user_level_max_screen as trial_user_level_max_screen,
				t.user_level_max_live_pin as trial_user_level_max_live_pin,
				t.user_level_max_comment_pin as trial_user_level_max_comment_pin,
				t.user_level_max_client as trial_user_level_max_client,
				t.user_level_max_load as trial_user_level_max_load,
				t.user_level_price as trial_user_level_price,
				t.user_level_color as trial_user_level_color
			")
		);


		// Set the cache
		if ($userInfo && isset($userInfo[0])) {

			if (!$full) $cache->set('user:'.$user_ID, $userInfo[0]);
			return $userInfo[0];

		}


	}


	return false;

}

function getUserInfo($user_ID = false) {
	global $User;


	// Get the User ID
	$user_ID = !$user_ID ? currentUserID() : $user_ID;


	// If email user
	if ( filter_var($user_ID, FILTER_VALIDATE_EMAIL) ) return
		array(
			'userName' => "",
			'firstName' => "",
			'lastName' => "",
			'fullName' => $user_ID,
			'nameAbbr' => '<i class="fa fa-envelope"></i>',
			'email' => 'Not confirmed yet',
			'userPic' => "",
			'userPicUrl' => null,
			'printPicture' => "",
			'emailNotifications' => "",
			'userLevelName' => "",
			'userLevelID' => "",
			'userLevelMaxProject' => "",
			'userLevelMaxPage' => "",
			'userLevelMaxScreen' => "",
			'userLevelMaxLivePin' => "",
			'userLevelMaxCommentPin' => "",
			'userLevelMaxLoad' => "",
			'trialUserLevelName' => null,
			'trialStartedFor' => null,
			'trialExpireDate' => "",
			'trialExpired' => 1,
			'trialExpiredNotified' => 0,
			'trialAvailable' => 0,
			'trialAvailableDays' => 0,
			'trialActive' => 0
		);


	// If not numeric
	if ( !is_numeric($user_ID) ) return false;
	$user_ID = intval($user_ID);


	// Get user information
	$userInfo = getUserInfoDB($user_ID);
	if ( !$userInfo ) return false;


	// The extended user data
	$extendedUserInfo = array(
		'userName' => $userInfo['user_name'],
		'firstName' => $userInfo['user_first_name'],
		'lastName' => $userInfo['user_last_name'],
		'fullName' => $userInfo['user_first_name']." ".$userInfo['user_last_name'],
		'nameAbbr' => getUserNameAbbr($userInfo['user_first_name'], $userInfo['user_last_name']),
		'email' => $userInfo['user_email'],
		'userPic' => $userInfo['user_picture'],
		'userPicUrl' => getUserPicUrl($userInfo['user_picture'], $userInfo['user_email']),
		'emailNotifications' => $userInfo['user_email_notifications'],
		'userLevelName' => $userInfo['user_level_name'],
		'userLevelID' => $userInfo['user_level_ID'],
		'userLevelMaxProject' => $userInfo['user_level_max_project'],
		'userLevelMaxPage' => $userInfo['user_level_max_page'],
		'userLevelMaxScreen' => $userInfo['user_level_max_screen'],
		'userLevelMaxLivePin' => $userInfo['user_level_max_live_pin'],
		'userLevelMaxCommentPin' => $userInfo['user_level_max_comment_pin'],
		'userLevelMaxLoad' => $userInfo['user_level_max_load']
	);
	$extendedUserInfo['printPicture'] = 'style="background-image: url('.$extendedUserInfo['userPicUrl'].');"';


	// Trial Info
	$extendedUserInfo['trialStartedFor'] = $userInfo['trial_started_for'];
	$extendedUserInfo['trialExpireDate'] = $userInfo['trial_expire_date'];

	$extendedUserInfo['trialExpired'] = isTrialExpired($userInfo['trial_expire_date']);
	$extendedUserInfo['trialExpiredNotified'] = $userInfo['trial_expired_notified'];
	$extendedUserInfo['trialAvailable'] = !$extendedUserInfo['trialExpired'] ? 1 : 0;

	$extendedUserInfo['trialAvailableDays'] = $extendedUserInfo['trialExpired'] ? 0 : 7;
	if ( !$extendedUserInfo['trialExpired'] && $extendedUserInfo['trialExpireDate'] ) {

		$now = new DateTime();
		$later = new DateTime( $extendedUserInfo['trialExpireDate'] );

		$extendedUserInfo['trialAvailableDays'] = $later->diff($now)->d;

	}


	$extendedUserInfo['trialActive'] = $extendedUserInfo['trialAvailable'] && $userInfo['trial_started_for'] != null && !$extendedUserInfo['trialExpired'] && $userInfo['trial_started_for'] != $userInfo['user_level_ID'] ? 1 : 0;

	$extendedUserInfo['trialUserLevelName'] = $userInfo['trial_user_level_name'];


	return $extendedUserInfo;

}

function getUserPicUrl($userPic, $userEmail = "") {

	$userPicUrl = "";

	// Gravatar
	if ( filter_var($userEmail, FILTER_VALIDATE_EMAIL) )
		$userPicUrl = get_gravatar($userEmail, 250);

	// Avatar
	if ( !empty($userPic) )
		$userPicUrl = $userPic;


	return $userPicUrl;

}

function getUserNameAbbr($firstName, $lastName) {

	return mb_substr($firstName, 0, 1).mb_substr($lastName, 0, 1);

}

function isTrialExpired($trial_expire_date) {

	return $trial_expire_date && currentTimeStamp() > $trial_expire_date ? 1 : 0;

}

function checkAvailableEmail($user_email) {
	global $db;

	$db->where("user_email", $user_email);
	$user = $db->getOne("users", "user_ID");

	return $user ? false : true;
}

function checkAvailableUserName($user_name) {
	global $db;

	$db->where("user_name", $user_name);
	$user = $db->getOne("users", "user_ID");

	return $user ? false : true;
}



/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mp | identicon | monsterid | wavatar | blank ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source https://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'blank', $r = 'g', $img = false, $atts = array() ) {
	$url = 'https://www.gravatar.com/avatar/';
	$url .= md5( strtolower( trim( $email ) ) );
	$url .= "?s=$s&d=$d&r=$r";
	if ( $img ) {
		$url = '<img src="' . $url . '"';
		foreach ( $atts as $key => $val )
			$url .= ' ' . $key . '="' . $val . '"';
		$url .= ' />';
	}
	return $url;
}