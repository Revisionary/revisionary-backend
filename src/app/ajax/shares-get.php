<?php

$status = "initiated";



// NONCE CHECK !!! Disabled for now!
// if ( request("nonce") !== $_SESSION["pin_nonce"] ) return;


// Is the object ID number?
if ( !is_numeric(request('id')) ) return;
$object_ID = intval(request('id'));


// Data type options check
if ( request('dataType') != "project" && request('dataType') != "page" ) return;
$dataType = request('dataType');



// DO THE SECURITY CHECKS !!!



$shared_users = array();



// 1. Find the owner
$objectData = $dataType::ID($object_ID);
$owner_ID = intval($objectData->getInfo('user_ID'));
$ownerInfo = getUserInfo($owner_ID);


if ($ownerInfo) {

	$status = "Owner added";


	// Add the owner
	$shared_users[] = array(
		'mStatus' => "owner",
		'type' => $dataType,
		'object_ID' => $object_ID,
		'email' => $ownerInfo['email'],
		'fullName' => $ownerInfo['fullName'],
		'nameAbbr' => $ownerInfo['nameAbbr'],
		'userImageUrl' => $ownerInfo['userPicUrl'],
		'user_ID' => $owner_ID,
		'sharer_user_ID' => 0
	);

}


// 2. Find the project shares (if only the type is page)
if ( $dataType == "page" ) {

	$status = "Project owner added";

	$project_ID = $objectData->getInfo('project_ID');
	$project_owner_ID = Project::ID($project_ID)->getInfo('user_ID');
	$projectOwnerInfo = getUserInfo($project_owner_ID);


	// Add the owner
	$shared_users[] = array(
		'mStatus' => "projectowner",
		'type' => 'project',
		'object_ID' => $project_ID,
		'email' => $projectOwnerInfo['email'],
		'fullName' => $projectOwnerInfo['fullName'],
		'nameAbbr' => $projectOwnerInfo['nameAbbr'],
		'userImageUrl' => $projectOwnerInfo['userPicUrl'],
		'user_ID' => $project_owner_ID,
		'sharer_user_ID' => 0
	);


	// Get Project Shares
	$db->where('share_type', 'project');
	$db->where('shared_object_ID', $project_ID);
	$project_shares = $db->connection('slave')->get('shares', null, "share_to, sharer_user_ID");


	if ($project_shares) {

		$status = "Project shares added";

		// Add the shared users
		foreach ($project_shares as $sharedUser) {

			$shredUserInfo = getUserInfo( $sharedUser['share_to'] );

			$shared_users[] = array(
				'mStatus' => "project",
				'type' => 'project',
				'object_ID' => $project_ID,
				'email' => $shredUserInfo['email'],
				'fullName' => $shredUserInfo['fullName'],
				'nameAbbr' => $shredUserInfo['nameAbbr'],
				'userImageUrl' => $shredUserInfo['userPicUrl'],
				'user_ID' => $sharedUser['share_to'],
				'sharer_user_ID' => $sharedUser['sharer_user_ID']
			);

		}

	}

}


// 3. Find the object shares
$db->where('share_type', $dataType);
$db->where('shared_object_ID', $object_ID);
$object_shares = $db->connection('slave')->get('shares', null, "share_to, sharer_user_ID");

if ($object_shares) {

	$status = "Object shares added";

	// Add the shared users
	foreach ($object_shares as $sharedUser) {

		$shredUserInfo = getUserInfo( $sharedUser['share_to'] );
		$shared_users[] = array(
			'mStatus' => "shared",
			'type' => $dataType,
			'object_ID' => $object_ID,
			'email' => $shredUserInfo['email'],
			'fullName' => $shredUserInfo['fullName'],
			'nameAbbr' => $shredUserInfo['nameAbbr'],
			'userImageUrl' => $shredUserInfo['userPicUrl'],
			'user_ID' => $sharedUser['share_to'],
			'sharer_user_ID' => $sharedUser['sharer_user_ID']
		);

	}

}



$status = count($shared_users) > 0 ? "Users received" : "No Users Found";


// CREATE THE RESPONSE
$info = array(

	'status' => $status,
	'dataType' => $dataType,
	'object_ID' => $object_ID,
	'nonce' => request('nonce'),
	'S_nonce' => $_SESSION['js_nonce']

);

echo json_encode(array(
  'info' => $info,
  'users' => $shared_users
));
