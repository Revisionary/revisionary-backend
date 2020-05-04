<?php

// Composer Autoloader
$autoload_file = realpath('.')."/vendor/autoload.php";
if ( !file_exists($autoload_file) ) die('C: Please try again in a few minutes...');
require $autoload_file;


// Config file
require 'system/config.php';


// Language file
require 'language/' . $config['default_language'] . '/lang.php';


// Bring the classes
$classesDir = realpath('.') . '/app/classes';
if ($dh = opendir($classesDir)){
	while($file = readdir($dh)){
		if (is_file($classesDir . '/' . $file) && substr($file, -4) == ".php"){
			require $classesDir . '/' . $file;
		}
	}
}


// Bring the helpers functions
Helper::Load();


// Connect to DB
$db = new MysqliDb(array(
	'host' 		=> $config['db']['host'],
	'username' 	=> $config['db']['user'],
	'password' 	=> $config['db']['pass'],
	'db'		=> $config['db']['name'],
	'port'		=> $config['db']['port'],
	'socket'	=> $config['db']['socket'],
	'prefix' 	=> '',
	'charset' 	=> 'utf8mb4'
));


// PHP timezone update
date_default_timezone_set(timezone);


// Start the session
load_session();


// Debug Mode
$debug_mode = $config['env']['debug'] == "TRUE";
//$debug_mode = false;
if ($debug_mode) $db->setTrace(true);


// If logged in and user not found
$User = User::ID();
$UserInfo = $User ? $User->getInfo() : [];
if ( userLoggedIn() && !$User ) {


	// Log out and go home
	if( session_destroy() ) {
		header('Location: '.site_url());
		die();
	}


}


// Default Limitations
$loadPercentage = $projectsPercentage = $phasesPercentage = $screensPercentage = $pinsPercentage = $commentPinsPercentage = null;


// If logged in
if ($User) {


	// Hard user change for admins
	if ( $UserInfo['user_level_ID'] === 1 && is_numeric(get('login_to')) ) {
		$_SESSION['user_ID'] = intval(get('login_to'));
		header('Location: '.removeQueryArg("login_to", current_url()));
		die();
	}


	// Bring the limitations data
	require model('limitations');

}