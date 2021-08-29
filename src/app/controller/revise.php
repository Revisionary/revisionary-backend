<?php

use Cocur\BackgroundProcess\BackgroundProcess;

// Force re-internalize
$forceReInternalize = get('redownload') === "";
$leavePins = get('leavepins') === "";
$ssr = $forceReInternalize && get('ssr') === "";
$capture = get('capture') === "";




// USER:
// If not logged in, go login page !!! Change when public revising available
if (!$User) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}
$userInfo = getUserInfo();



// DEVICE:
// If no deviec specified or not numeric, go projects page
if ( !isset($_url[1]) || !is_numeric($_url[1]) ) {
	header('Location: '.site_url('projects?invaliddevice'));
	die();
}
$device_ID = intval($_url[1]);

// If the specified device doesn't exist, go projects page
$deviceData = Device::ID($device_ID);
if ( !$deviceData ) {
	header('Location: '.site_url('projects?devicedoesntexist'));
	die();
}
$device = $deviceData->getInfo();
//die_to_print($device);

// Screenshots
$device_image = $deviceData->getImage();
//die($device_image);

// Screenshots
$device_image_URL = $deviceData->getImageURL();
//die($device_image);

// All my devices
$allMyDevices = $User->getDevices();
//die_to_print($allMyDevices);



// VERSION:
// Get the phase ID
$phase_ID = $device['phase_ID'];

// Current phase data
$phaseData = Phase::ID($phase_ID);
//die_to_print($phaseData);

// If the specified phase doesn't exist, go projects page
if ( !$phaseData ) {
	header('Location: '.site_url('projects?phasedoesntexist'));
	die();
}
$phase = $phaseData->getInfo();
//die_to_print($phase);

$phase_type = $phase['phase_type'];

// All my phases
$allMyPhases = $User->getPhases();
//die_to_print($allMyPhases);



// PAGE:
// Get the page ID
$page_ID = $phase['page_ID'];

// Current page data
$pageData = Page::ID($page_ID, currentUserID());

// Check if page not exists, redirect to the projects page
if ( !$pageData ) {
	header('Location: '.site_url('projects?pagedoesntexist'));
	die();
}
$page = $pageData->getInfo();
//die_to_print($page);

$page_type = $phase_type;

// All my pages
$allMyPages = $pages = $User->getPages(null, null, ''); // Excluding archives and deletes
//die_to_print($allMyPages);



// Force Internalizing
if ($forceReInternalize) {

	// Test the URL and get final URL after redirects
	$final_url = get_redirect_final_target($page['page_url']);
	if ($page['page_url'] != $final_url) {
		$pageData->edit('page_url', $final_url);
	}


	// Remove all pins in this phase when force reinternalizing
	if (!$leavePins) {
		$db->where('phase_ID', $phase_ID);
		$db->delete('pins');
	}

}



// PROJECT:
// Get the project ID
$project_ID = $page['project_ID'];

// Current project data
$projectData = Project::ID($project_ID);

// Check if project not exists, redirect to the projects page
if ( !$projectData ) {
	header('Location: '.site_url('projects?projectdoesntexist'));
	die();
}
$project = $projectData->getInfo();
//die_to_print($project);

// Project Image
$project_image = $project['project_image_device_ID'];
if ($project_image == null) $projectData->edit('project_image_device_ID', $device_ID);

// All my projects
$allMyProjects = $User->getProjects(null, ''); // Excluding archives and deletes
//die_to_print($allMyProjects);



// SCREEN INFO

// Get screen ID
$screen_ID = $device['screen_ID'];

// Get the screen sizes
$width = $device['device_width'] ? $device['device_width'] : $device['screen_width'];
$height = $device['device_height'] ? $device['device_height'] : $device['screen_height'];

// Get screen name
$screen_name = $device['screen_name'];

// Get the screen icon
$screenCatID = $device['screen_cat_ID'];
$screenCatName = $device['screen_cat_name'];
$screenIcon = $device['screen_cat_icon'];
$screenRotateable = $device['screen_rotateable'];



// PROTOCOL REDIRECTIONS:

// Http to Https Redirection
if ( (substr($phaseData->remoteUrl, 0, 8) == "https://" || $phaseData->remoteUrl == "image") && !ssl) {

	header( 'Location: '.current_url("", "", true) ); // Force HTTPS
	die();

}

// Https to Http Redirection
elseif ( substr($phaseData->remoteUrl, 0, 7) == "http://" && ssl) {

	header( 'Location: '.current_url("", "", false, true) ); // Force HTTP
	die();

}



// LOG:

// Create the log folder if not exists
if ( !file_exists($phaseData->logDir) )
	mkdir($phaseData->logDir, 0755, true);
@chmod($phaseData->logDir, 0755);



// QUEUE CHECK:

// Check if queue is already working
$db->where('queue_type', 'internalize');
$db->where('queue_object_ID', $phase_ID);
$db->where("(queue_status = 'working' OR queue_status = 'waiting')");
$existing_queue = $db->connection('slave')->get('queues');
//die_to_print($existing_queue);


$queue_ID = "";
$process_ID = "";
$process_status = "";



/*
die_to_print( $phaseData->phaseDir, false ); // Folder is exist
die_to_print( $phaseData->phaseFile, false ); // HTML is downloaded
die_to_print( $phaseData->logDir."/browser.log", false ); // No error on Browser
die_to_print( $phaseData->logDir."/html-filter.log", false ); // No error on HTML filtering
die_to_print( $phaseData->logDir."/css-filter.log" ); // No error on CSS filtering
*/


// If already working queue exists !!!
if (
	!$forceReInternalize &&
	$existing_queue != null
) {


	// Error catch
	if ( !is_array($existing_queue) || count($existing_queue) > 1  || count($existing_queue) == 0 )
		die('Error #Q21. Please try again...');


	// Set the queue
	$existing_queue = $existing_queue[0];


	$queue_PID = $existing_queue['queue_PID'];
	$queue_ID = $existing_queue['queue_ID'];
	$queue_status = $existing_queue['queue_status'];


	$process_status = "Phase #$phase_ID Queue Found: Queue #$queue_ID '".ucfirst($queue_status)."' | Page #$page_ID | Device #$device_ID | Process #$queue_PID | User #".currentUserID();


	// Site log
	$log->info($process_status);


	// Set the process ID to check
	$process_ID = $queue_PID;
	$process = BackgroundProcess::createFromPID( $process_ID );


	$process_status .= " BackgroundProcess::getPid() -> ". $process->getPid();


// If no need to re-internalize
} elseif (

	!$forceReInternalize &&
	($phase['phase_internalized'] > 0 || $phase['phase_type'] == "capture" || $phase['phase_type'] == "image" || $capture)

) {


	$process_status = "Device #$device_ID Opening: Phase #$phase_ID (Already Downloaded) | Page #$page_ID | User #".currentUserID();


	// Site log
	$log->info($process_status);



	// Device screenshot check
	if ( !file_exists( $device_image ) || $capture ) {



		if ($capture) {


			$phase_type = $page_type = "capture";
			$phaseData->edit('phase_type', $phase_type);
	
	
			// Remove the existing and wrong files
			if ( file_exists($phaseData->phaseDir) )
				deleteDirectory($phaseData->phaseDir);
	
	
			// Re-Create the log folder if not exists
			if ( !file_exists($phaseData->logDir) )
				mkdir($phaseData->logDir, 0755, true);
			@chmod($phaseData->logDir, 0755);
	
	
		}



		// Logger
		$logger = new Katzgrau\KLogger\Logger($phaseData->logDir, Psr\Log\LogLevel::DEBUG, array(
			'filename' => 'screenshot',
		    'extension' => $phaseData->logFileExtension, // changes the log file extension
		));


		// NEW QUEUE
		// Add a new job to the queue
		$queue = new Queue();
		$queue_results = $queue->new_job('screenshot', $phase_ID, $page_ID, $device_ID, "Waiting other works to be done.", $phase_type);
		$process_ID = $queue_results['process_ID'];
		$queue_ID = $queue_results['queue_ID'];


		// Site log
		$log->info("Device #$device_ID Screenshot Taking: Phase #$phase_ID | Page #$page_ID | Project #$project_ID | Queue #$queue_ID | Process ID #".$process_ID." | User #".currentUserID());


	}



// Needs to be completely internalized
} elseif ($forceReInternalize || $phase['phase_type'] == "url") {


	// Remove the existing and wrong files
	if ( file_exists($phaseData->phaseDir) )
		deleteDirectory($phaseData->phaseDir);


	// Re-Create the log folder if not exists
	if ( !file_exists($phaseData->logDir) )
		mkdir($phaseData->logDir, 0755, true);
	@chmod($phaseData->logDir, 0755);


	// Logger
	$logger = new Katzgrau\KLogger\Logger($phaseData->logDir, Psr\Log\LogLevel::DEBUG, array(
		'filename' => $phaseData->logFileName,
	    'extension' => $phaseData->logFileExtension, // changes the log file extension
	));



	// Update the phase type
	$phase_type = $page_type = $ssr ? "ssr" : "url";
	$phaseData->edit('phase_type', $phase_type);



	// NEW QUEUE
	// Add a new job to the queue
	$queue = new Queue();
	$queue_results = $queue->new_job('internalize', $phase_ID, $page_ID, $device_ID, "Waiting other works to be done.", $phase_type);
	$process_ID = $queue_results['process_ID'];
	$queue_ID = $queue_results['queue_ID'];


	// Site log
	$log->info("Device #$device_ID Internalizing: Phase #$phase_ID | Page #$page_ID | Queue #$queue_ID | Process ID #".$process_ID." | User #".currentUserID());


}

/*
echo "<pre>"; print_r($existing_queue); echo "</pre>";
echo $process_status."<br>";
echo $process_ID;
die();
*/



// PINS IN THIS PROJECT
$allMyPins = $User->getPins();
//die_to_print($allMyPins);



// FILTERS
$pin_filter = "all";
if (
	get('filter') == "complete" ||
	get('filter') == "incomplete" ||
	get('filter') == "hide" ||
	get('filter') == "public" ||
	get('filter') == "private"
) $pin_filter = get('filter');



// PIN MODE
$pin_mode = "content";
if (
	get('pinmode') == "content" ||
	get('pinmode') == "style" ||
	get('pinmode') == "browse"
) $pin_mode = get('pinmode');

$pin_private = "0";
if (
	get('privatepin') == "1"
) $pin_private = get('privatepin');



// Revising Notification for the admin
if ($userInfo['email'] != "bilaltas@me.com" && $userInfo['email'] != "bill@twelve12.com") {

	Notify::ID(1)->mail(
		$userInfo['fullName']." revising a page now.",
		"
		<b>User Information</b> <br>
		Email: ".$userInfo['email']." <br>
		Full Name: ".$userInfo['fullName']." <br>
		Username: ".$userInfo['userName']." <br><br>


		<b>Project Name:</b> <a href='".site_url('project/'.$project_ID)."'>".$project['project_name']."</a> <br>
		<b>Page Name:</b> <a href='".site_url('page/'.$page_ID)."'>".$page['page_name']."</a> <br>
		<b>Device:</b> $screen_name: $width x $height <br>
		"
	);

}


$additionalCSS = [
	'vendor/spectrum.css',
	'revise.css'
];

$additionalHeadJS = [
	'vendor/jquery-ui.min.js',
	'vendor/Autolinker.min.js',
	'vendor/autosize.min.js',
	'vendor/diff.js',
	'vendor/spectrum.js',
	'vendor/tinymce.min.js',
	'process.js',
	'revise-globals.js',
	'revise-functions.js'
];

$additionalBodyJS = [
	'common.js',
	'revise-page.js'
];



// Generate new nonce for add new screens
//$_SESSION["new_screen_nonce"] = uniqid(mt_rand(), true);


// Generate new nonce for pin actions
//$_SESSION["pin_nonce"] = uniqid(mt_rand(), true);
//error_log("SESSION CREATED: ".$_SESSION["pin_nonce"]);



$page_title = "Page Opening...";
require view('revise');