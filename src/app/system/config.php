<?php

$config = array('last_update' => '2021-08-29-01:14');



// Environmental settings
$config['env'] = [
	'name' 				 		   => getenv('ENV_NAME') ?? "local-dev",
	'debug'  			 		   => getenv('DEBUG') ?? false,

	'domain'			 		   => getenv('API_DOMAIN') ?? "revisionary.co",
	'subdomain' 		 		   => getenv('API_SUBDOMAIN') ?? "api",

	'dashboard_domain'			   => getenv('DASHBOARD_DOMAIN') ?? "revisonary.co",
	'dashboard_subdomain' 		   => getenv('DASHBOARD_SUBDOMAIN') ?? "app",

	'landing_domain'			   => getenv('LANDING_DOMAIN') ?? "revisionaryapp.com",
	'landing_subdomain' 		   => getenv('LANDING_SUBDOMAIN') ?? "www",

	'db_host' 			 		   => getenv('DB_HOST') ?? "revisionary_database",
	'db_port' 			 		   => getenv('DB_PORT') ?? 3306,
	'db_name' 			 		   => getenv('DB_NAME') ?? "revisionaryapp",
	'db_user' 			 		   => getenv('DB_USER') ?? "user",
	'db_pass' 			 		   => getenv('DB_PASSWORD') ?? "test",
	'db_socket' 			 	   => getenv('DB_SOCKET') ?? null,

	'db_slave_host'		 		   => getenv('DB_SLAVE_HOST') ?? "",
	'db_slave_port'		 		   => getenv('DB_SLAVE_PORT') ?? "",
	'db_slave_name'		 		   => getenv('DB_SLAVE_NAME') ?? "",
	'db_slave_user'		 		   => getenv('DB_SLAVE_USER') ?? "",
	'db_slave_pass'		 		   => getenv('DB_SLAVE_PASSWORD') ?? "",
	'db_slave_socket'		 	   => getenv('DB_SLAVE_SOCKET') ?? "",

	'db_choice' 			 	   => getenv('DB_CHOICE') ?? "local",

	'smtp_user' 		 		   => getenv('SMTP_USER') ?? false,
	'smtp_pass' 		 		   => getenv('SMTP_PASS') ?? false,

	's3_key'					   => getenv('S3_KEY') ?? false,
	's3_secret' 		 		   => getenv('S3_SECRET') ?? false,
	's3_region' 		 		   => getenv('S3_REGION') ?? false,
	's3_bucket' 		 		   => getenv('S3_BUCKET') ?? false,

	'chrome_url'				   => getenv('CHROME_URL') ?? "http://chrome:3000",

	'jwt_secret_key'	 		   => getenv('JWT_SECRET_KEY') ?? "test123samplekey",

];



// For Digital Ocean
if ($config['env']['name'] == 'remote-dev') {

	// Check the mounted Digital Ocean volume
	if ( !file_exists( realpath('.').'/cache/volume_mounted' ) ) die('V: Please try again in a few minutes...');

}



// SSL Check
if ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) )
	$_https = $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https";
else
	$_https = !empty(@$_SERVER['HTTPS']) && @$_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && @$_SERVER['SERVER_PORT'] == 443);



// Cloud DB Choice
if ($config['env']['db_choice'] == 'cloud') {

	$config['env']['db_host'] = getenv('DB_CLOUD_HOST');
	$config['env']['db_port'] = getenv('DB_CLOUD_PORT');
	$config['env']['db_name'] = getenv('DB_CLOUD_NAME');
	$config['env']['db_user'] = getenv('DB_CLOUD_USER');
	$config['env']['db_pass'] = getenv('DB_CLOUD_PASSWORD');

}



// Cloud Socket Check
if ( getenv('CLOUD_SQL_CONNECTION_NAME') ) {

	$config['env']['db_socket'] = getenv('CLOUD_SQL_CONNECTION_NAME');

}



// Database Info
$config['db'] = [
	'host'   => $config['env']['db_host'],
	'port'   => $config['env']['db_port'],
	'name'   => $config['env']['db_name'],
	'user'   => $config['env']['db_user'],
	'pass'   => $config['env']['db_pass'],
	'socket' => $config['env']['db_socket']
];


// Slave Database Info
$config['db_slave'] = [
	'host'   => $config['env']['db_slave_host'],
	'port'   => $config['env']['db_slave_port'],
	'name'   => $config['env']['db_slave_name'],
	'user'   => $config['env']['db_slave_user'],
	'pass'   => $config['env']['db_slave_pass'],
	'socket' => $config['env']['db_slave_socket']
];


// Same with the main DB if no slave info !!! Slave disabled for now
//if ( $config['env']['db_choice'] != 'cloud' && $config['db_slave']['host'] == "" )
	$config['db_slave'] = [
		'host'   => $config['env']['db_host'],
		'port'   => $config['env']['db_port'],
		'name'   => $config['env']['db_name'],
		'user'   => $config['env']['db_user'],
		'pass'   => $config['env']['db_pass'],
		'socket' => $config['env']['db_socket']
	];



// Default Language
$config['default_language'] = 'en';



// Definitions
define('timezone', "UTC");
define('domain', $config['env']['domain']);
define('subdomain', $config['env']['subdomain']);
define('host', subdomain . "." . domain);
define('dir', realpath('.'));
define('backdir', realpath('..'));
define('logdir', dir . '/app/logs');
define('session_name', 'revisionary_session');
define('sessiondir', backdir."/sessions");
define('session_lifetime', 99999);
define('model', dir . '/app/model');
define('view', dir . '/app/view');
define('controller', dir . '/app/controller');
define('cache', dir . '/cache');
define('port', (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : "") );
define('ssl', $_https);
define('protocol', ssl ? "https" : "http");
define('secure_url', "https://".$_SERVER["SERVER_NAME"]);
define('insecure_url', "http://".$_SERVER["SERVER_NAME"]);
define('url', ssl ? secure_url : insecure_url);



// Unset the variables
unset($_https);
