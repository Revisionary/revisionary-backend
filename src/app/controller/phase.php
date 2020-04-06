<?php


// SECURITY CHECKS

// If not logged in, go login page !!! Change when public revising available
if (!$User) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}


// If no page specified or not numeric, go projects page
if ( !isset($_url[1]) || !is_numeric($_url[1]) ) {
	header('Location: '.site_url('projects?invalidphase'));
	die();
}



// THE VERSION INFO

// Get the phase ID
$phase_ID = intval($_url[1]);

// All my phases
$phaseData = Phase::ID($phase_ID, currentUserID());

// If the specified device doesn't exist, go projects page
if ( !isset($phaseData) ) {
	header('Location: '.site_url("project/$project_ID?phasedoesntexist"));
	die();
}




// THE DEVICE INFO

// All my devices
$allMyDevices = $User->getDevices($phase_ID);
//die_to_print($allMyDevices);


if ( is_numeric(get('screen')) ) {

	$screen_ID = get('screen');
	$foundDevices = array_filter($allMyDevices, function($deviceFound) use ($screen_ID) {

		return $deviceFound['screen_ID'] == $screen_ID;

	});
	$choosenDevice = reset($foundDevices);
	//die_to_print($choosenDevice);


	// Create a new device if not found
	if (!$choosenDevice) {


		// Check screen limit here!!! Do it on revise page before changing page
		if($screensPercentage >= 100) {
			header('Location: '.site_url("projects?limit-warning"));
			die();
		}


		// Add the Devices
		$device_ID = Device::ID()->addNew(
			$phase_ID,
			array(get('screen')),
			request('page_width') != "" && is_numeric(request('page_width')) ? request('page_width') : null,
			request('page_height') != "" && is_numeric(request('page_height')) ? request('page_height') : null
		);

		// Check the result
		if(!$device_ID) {
			header('Location: '.site_url("project/$project_ID?adddeviceerror")); // If unsuccessful
			die();
		}


	} else {

		$device_ID = $choosenDevice['device_ID'];

	}


} else {


	// The first device
	$choosenDevice = reset($allMyDevices);
	//die_to_print($choosenDevice);

	// If the specified device doesn't exist, go projects page
	if ( !isset($choosenDevice) ) {
		header('Location: '.site_url("projects?devicedoesntexist"));
		die();
	}

	$device_ID = $choosenDevice['device_ID'];


}

//die_to_print($device_ID);


$url_to_redirect = site_url('revise/'.$device_ID);

// Pin modes
if ( get('pinmode') == "style" || get('pinmode') == "browse" ) $url_to_redirect = queryArg('pinmode='.get('pinmode'), $url_to_redirect);
if ( get('privatepin') == "1" ) $url_to_redirect = queryArg('privatepin=1', $url_to_redirect);

// Pin Filters
if ( get('filter') == "incomplete" || get('filter') == "complete" ) $url_to_redirect = queryArg('filter='.get('filter'), $url_to_redirect);

// Is new page?
if ( get('new') == "page" ) $url_to_redirect = queryArg('new=page', $url_to_redirect);

//die_to_print($url_to_redirect);


// If nothing goes wrong, open the first device
header('Location: '.$url_to_redirect);
die();