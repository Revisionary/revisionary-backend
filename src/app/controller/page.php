<?php


// SECURITY CHECKS

// If not logged in, go login page !!! Change when public revising available
if (!$User) {
	header('Location: '.site_url('login?redirect='.urlencode( current_url() )));
	die();
}


// If no page specified or not numeric, go projects page
if ( !isset($_url[1]) || !is_numeric($_url[1]) ) {
	header('Location: '.site_url('projects?invalidpage'));
	die();
}



// THE PAGE INFO:
// Get the page ID
$page_ID = intval($_url[1]);

// Current page data
$pageData = Page::ID($page_ID);

// Check if page not exists, redirect to the projects page
if ( !$pageData ) {
	header('Location: '.site_url('projects?pagedoesntexist'));
	die();
}
$page = $pageData->getInfo();
//die_to_print($page);

// All my pages
$allMyPages = $User->getPages(null, null, '');
//die_to_print($allMyPages);

// Get project ID
$project_ID = $page['project_ID'];



// THE PHASE INFO:

// All my phases
$allMyPhases = $User->getPhases($page_ID);
//die_to_print($allMyPhases);

// The last phase
$lastPhase = end($allMyPhases);
//die_to_print($lastPhase);

// If the specified device doesn't exist, go projects page
if ( !isset($lastPhase) ) {
	header('Location: '.site_url("project/$project_ID?phasedoesntexist"));
	die();
}


$url_to_redirect = site_url('phase/'.$lastPhase['phase_ID']);

// Pin modes
if ( get('pinmode') == "style" || get('pinmode') == "browse" ) $url_to_redirect = queryArg('pinmode='.get('pinmode'), $url_to_redirect);
if ( get('privatepin') == "1" ) $url_to_redirect = queryArg('privatepin=1', $url_to_redirect);

// Pin filters
if ( get('filter') == "incomplete" || get('filter') == "complete" ) $url_to_redirect = queryArg('filter='.get('filter'), $url_to_redirect);

// Is new Page?
if ( get('new') == "page" ) $url_to_redirect = queryArg('new=page', $url_to_redirect);

// Requested screen size
if ( is_numeric(get('screen')) ) $url_to_redirect = queryArg('screen='.get('screen'), $url_to_redirect);
if ( is_numeric(get('page_width')) ) $url_to_redirect = queryArg('page_width='.get('page_width'), $url_to_redirect);
if ( is_numeric(get('page_height')) ) $url_to_redirect = queryArg('page_height='.get('page_height'), $url_to_redirect);

//die_to_print($url_to_redirect);


// If nothing goes wrong, open the first device
header('Location: '.$url_to_redirect);
die();