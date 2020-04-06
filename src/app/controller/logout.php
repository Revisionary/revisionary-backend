<?php

// If not logged in, go home
if ( !userLoggedIn() ) {
	header('Location: '.site_url());
	die();
}

// Log out and go home
if( session_destroy() ) {
	header('Location: '.site_url());
	die();
}

// Go back if any problem
header("Location: ".$_SERVER['HTTP_REFERER']);
die();