<?php

$config = array('last_update' => '2020-04-27-04:00');



// Environmental settings
$config['env'] = [
	'name' 				 		   => getenv('ENV_NAME') ?? "local-dev",
	'debug'  			 		   => getenv('DEBUG') ?? false,

	'domain'			 		   => getenv('API_DOMAIN') ?? "revisionaryapp.com",
	'subdomain' 		 		   => getenv('API_SUBDOMAIN') ?? "dev",
	'insecure_subdomain' 		   => getenv('API_INSECURE_SUBDOMAIN') ?? "dev",

	'dashboard_domain'			   => getenv('DASHBOARD_DOMAIN') ?? "revisonary.co",
	'dashboard_subdomain' 		   => getenv('DASHBOARD_SUBDOMAIN') ?? "app",
	'dashboard_insecure_subdomain' => getenv('DASHBOARD_INSECURE_SUBDOMAIN') ?? "app",

	'landing_domain'			   => getenv('LANDING_DOMAIN') ?? "revisonary.co",
	'landing_subdomain' 		   => getenv('LANDING_SUBDOMAIN') ?? "app",
	'landing_insecure_subdomain'   => getenv('LANDING_INSECURE_SUBDOMAIN') ?? "app",

	'db_host' 			 		   => getenv('DB_HOST') ?? "revisionary_database",
	'db_port' 			 		   => getenv('DB_PORT') ?? 3306,
	'db_name' 			 		   => getenv('DB_NAME') ?? "revisionaryapp",
	'db_user' 			 		   => getenv('DB_USER') ?? "user",
	'db_pass' 			 		   => getenv('DB_PASSWORD') ?? "test",
	'db_socket' 			 	   => getenv('DB_SOCKET') ?? null,
	
	'db_choice' 			 	   => getenv('DB_CHOICE') ?? "local",
	'ssl_check' 			 	   => getenv('SSL_CHECK') ?? "normal",

	'smtp_user' 		 		   => getenv('SMTP_USER') ?? false,
	'smtp_pass' 		 		   => getenv('SMTP_PASS') ?? false,

	's3_key'					   => getenv('S3_KEY') ?? false,
	's3_secret' 		 		   => getenv('S3_SECRET') ?? false,
	's3_region' 		 		   => getenv('S3_REGION') ?? false,
	's3_bucket' 		 		   => getenv('S3_BUCKET') ?? false,
	
];



// For Digital Ocean
if ($config['env']['name'] == 'remote-dev') {

	// SSL Check (because of CloudFlare)
	$config['env']['ssl_check'] = 'cloudflare';

	// Cloud DB connection
	$config['env']['db_choice'] = 'cloud';

	// Check the mounted Digital Ocean volume
	if ( !file_exists( realpath('.').'/cache/lost+found' ) ) die('V: Please try again in a few minutes...');

}



// SSL Check
if ($config['env']['ssl_check'] == 'cloudflare')
	$_https = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https";
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



// Default Language
$config['default_language'] = 'en';



// Definitions
define('timezone', "UTC");
define('domain', $config['env']['domain']);
define('subdomain', $config['env']['subdomain']);
define('insecure_subdomain', $config['env']['insecure_subdomain']);
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
define('secure_url', "https://".subdomain."." . domain);
define('insecure_url', "http://".insecure_subdomain."." . domain);
define('url', ssl ? secure_url : insecure_url);



// Unset the variables
unset($_https);