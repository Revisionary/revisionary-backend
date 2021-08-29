<?php

$data = array();
$status = "initiated";


// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["js_nonce"] ) return;


// Valid e-mail?
if (!filter_var(post("email"), FILTER_VALIDATE_EMAIL)) {

	$data['status'] = "invalid-email";


	// CREATE THE ERROR RESPONSE
	die(json_encode(array(
	  'data' => $data
	)));
}
$email = post("email");



// DB Check for existing user
$db->where('user_email', $email);
$user = $db->getOne('users');


// If found !!!
if ( $user !== null ) {


	// If current user
	if ($user['user_ID'] == currentUserID()) {

		$data['status'] = "invalid-email";


		// CREATE THE ERROR RESPONSE
		echo json_encode(array(
		  'data' => $data
		));

		return;
	}





	$data = array(
		'status' => 'found',
		'share_to' => $user['user_ID'],
		'user_ID' => $user['user_ID'],
		'user_photo' => getUserInfo($user['user_ID'])['printPicture'],
		'user_name' => '<span>'.(getUserInfo($user['user_ID'])['nameAbbr']).'</span>',
	);


} else { // Not found


	$data = array(
		'status' => 'not-found',
		'share_to' => $email
	);


}



// CREATE THE RESPONSE
echo json_encode(array(
  'data' => $data
));
