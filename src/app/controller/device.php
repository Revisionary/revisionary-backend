<?php


// SECURITY CHECKS

// If no page specified or not numeric, go projects page
if ( !isset($_url[1]) || !is_numeric($_url[1]) ) {
	header('Location: '.site_url('projects?invaliddevice'));
	die();
}


// Get the device ID
$device_ID = $_url[1];


// If nothing goes wrong, open the first device
header( 'Location: '.site_url("revise/$device_ID") );
die();