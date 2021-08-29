<?php

$error_type = isset($_url[1]) && is_numeric($_url[1]) ? $_url[1] : "";


// Correct the messages !!!
if ( $error_type == "400" ) {

	$page_title = "We couldn't find the page you are looking for.";

} elseif ( $error_type == "401" ) {

	$page_title = "We couldn't find the page you are looking for.";

} elseif ( $error_type == "403" ) {

	$page_title = "We couldn't find the page you are looking for.";

} elseif ( $error_type == "404" ) {

	$page_title = "We couldn't find the page you are looking for.";

} elseif ( $error_type == "500" ) {

	$page_title = "We couldn't find the page you are looking for.";

} else {

	$page_title = "We couldn't find the page you are looking for.";

}


if ($debug_mode && !empty($error_type)) {
	$page_title = $error_type." - ".$page_title;
}


// Additional Scripts and Styles
$additionalCSS = [];

$additionalHeadJS = [
	'common.js'
];


require view('errors');