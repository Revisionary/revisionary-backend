<?php

// Defaults
$data = array();
$data['status'] = "initiated";
$shareTo = "";


// VALIDATE DATA
$add_type = post("add-type"); // !!! Validate


// Valid e-mail?
if (!filter_var(post("email"), FILTER_VALIDATE_EMAIL)) {

	$data['status'] = "invalid-email";
	$data['email'] = post("email");


	// CREATE THE ERROR RESPONSE
	die(json_encode(array(
	  'data' => $data
	)));
}
$email = post("email");


// Valid type?
if ( post('data-type') != "project" && post('data-type') != "page" ) {

	$data['status'] = "invalid-process";


	// CREATE THE ERROR RESPONSE
	die(json_encode(array(
	  'data' => $data
	)));
}
$type = post('data-type');


// Valid ID?
if ( !is_numeric(post("object_ID")) ) {

	$data['status'] = "invalid-id";


	// CREATE THE ERROR RESPONSE
	die(json_encode(array(
	  'data' => $data
	)));
}
$object_ID = intval( post("object_ID") );



/*
// NONCE CHECK !!! Disabled for now!
if ( post("nonce") !== $_SESSION["js_nonce"] ) {

	$data['status'] = "security-error";
	$data['posted_nonce'] = post("nonce");
	$data['session_nonce'] = $_SESSION["js_nonce"];


	// CREATE THE ERROR RESPONSE
	echo json_encode(array(
	  'data' => $data
	));

	return;
}
*/



// Is this object exist?
$object = $type::ID($object_ID);
if (!$object) {

	$data['status'] = "invalid-obj";


	// CREATE THE ERROR RESPONSE
	die(json_encode(array(
	  'data' => $data
	)));
}
$objectInfo = $object->getInfo();



// Do I access this object?
if ( ! $User->canAccess($object_ID, $type) ) {

	$data['status'] = "no-access";


	// CREATE THE ERROR RESPONSE
	die(json_encode(array(
	  'data' => $data
	)));

}



// DB Check for existing user
$db->where('user_email', $email);
$user = $db->getOne('users');

// If found
if ( $user !== null ) {


	// If current user
	if ( $user['user_ID'] == currentUserID() ) {

		$data['status'] = "invalid-email";


		// CREATE THE ERROR RESPONSE
		die(json_encode(array(
		  'data' => $data
		)));
	}



	$shareTo = intval($user['user_ID']);
	$shareToUserInfo = getUserInfo($shareTo);


	$data = array(
		'status' => 'found',
		'user_ID' => $shareTo,
		'user_fullname' => $shareToUserInfo['fullName'],
		'user_nameabbr' => $shareToUserInfo['nameAbbr'],
		'user_photo' => $shareToUserInfo['printPicture'],
		'user_avatar' => $shareToUserInfo['userPicUrl'],
		'user_name' => '<span>'.$shareToUserInfo['nameAbbr'].'</span>',
	);


} else { // Not found

	$data = array(
		'status' => 'not-found'
	);


	// Change the share type
	$shareTo = $email;


}



// If different type selected
if ( $type == "page" && $add_type == "project" ) {

	// Find the project ID
	$object_ID = intval($objectInfo['project_ID']);
	$type = "project";

}



// Check if already shared
$db->where( 'share_to', $shareTo );
$db->where( 'share_type', $type );
$db->where( 'shared_object_ID', $object_ID );
$shares = $db->connection('slave')->get('shares');

// Check the project shares if the current type is page
if ($type == "page") {

	$project_ID = Page::ID( $object_ID )->getInfo('project_ID');


	// Check if the project is already shared
	$db->where( 'share_to', $shareTo );
	$db->where( 'share_type', 'project' );
	$db->where( 'shared_object_ID', $project_ID );
	$project_shares = $db->connection('slave')->get('shares');
	$shares = array_merge($shares, $project_shares);

}

if ( count($shares) > 0 ) {

	$data['status'] = "already-exist";


	// CREATE THE ERROR RESPONSE
	die(json_encode(array(
	  'data' => $data
	)));
}



// Add the share to DB
$share_ID = $db->insert('shares', array(

	"share_to" => $shareTo,
	"share_type" => $type,
	"shared_object_ID" => $object_ID,
	"sharer_user_ID" => currentUserID()

));

if ($share_ID) { // If successful

	$data['status'] = "added";


	// INVALIDATE THE CACHES
	$cache->deleteKeysByTag(['projects', 'pages', 'phases', 'devices', 'pins']);
	


	// Site log
	$log->info(ucfirst($type)." #$object_ID shared to: User #$shareTo | Sharer User #".currentUserID());


	// Notify the user
	if ( $type == "page" || $type == "project" ) {


		$object_link = site_url($type.'/'.$object_ID);
		$objectData = ucfirst($type)::ID($object_ID, currentUserID());
		$objectName = $objectData->getInfo($type."_name");

		$projectName = "";
		if ($type == "page") {
			$projectName = " [".Project::ID( intval($objectData->getInfo("project_ID")) )->getInfo('project_name')."]";
		}


		// Web notification
		if ( is_integer($shareTo) )
			Notify::ID($shareTo)->web("share", $type, $object_ID, null, $objectData->getInfo("project_ID"), ($type == "page" ? $object_ID : null) );


		// Email notification
		Notify::ID($shareTo)->mail(
			getUserInfo()['fullName']." shared the \"$objectName".$projectName."\" $type with you.",

			"Hello, ".
			getUserInfo()['fullName']." shared the \"$objectName".$projectName."\" $type with you from Revisionary App. Here is the link to access this $type: <br>

<a href='$object_link' target='_blank'>$object_link</a>",
			true // Important
		);


	}


} else $data['status'] = "not-added";



// CREATE THE RESPONSE
die(json_encode(array(
	'info' => array(
		'type' => $type,
		'object_ID' => $object_ID,
		'email' => $email,
		'add_type' => $add_type
	),
	'data' => $data
)));