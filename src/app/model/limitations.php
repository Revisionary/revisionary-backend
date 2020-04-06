<?php

$limit_user_ID = currentUserID();
//$limit_user_ID = 1;
$limitUser = User::ID($limit_user_ID);

$trial_prefix = "";
if ( getUserInfo($limit_user_ID)['trialExpired'] == 0 && getUserInfo($limit_user_ID)['trialStartedFor'] != null ) $trial_prefix = "trial_";


// PROJECTS
$maxProjects = $limitUser->getInfo($trial_prefix.'user_level_max_project');
$myProjects = array_filter($limitUser->getProjects(), function($projectFound) use ($limit_user_ID) {
	return $projectFound['user_ID'] == $limit_user_ID;
});
$projectsCount = count( $myProjects );
$projectsPercentage = intval((100 * $projectsCount) / $maxProjects);
$maxProjects = $maxProjects == 99999 ? "∞" : $maxProjects;
$projectsLeft = $maxProjects == "∞" ? "Unlimited" : $maxProjects - $projectsCount;


// PAGES (PHASES)
$maxPhases = $limitUser->getInfo($trial_prefix.'user_level_max_page');
$myPhases = array_filter($limitUser->getPhases(), function($phaseFound) use ($limit_user_ID) {
	return $phaseFound['user_ID'] == $limit_user_ID;
});
$phasesCount = count( $myPhases );
$phasesPercentage = intval((100 * $phasesCount) / $maxPhases);
$maxPhases = $maxPhases == 99999 ? "∞" : $maxPhases;
$phasesLeft = $maxPhases == "∞" ? "Unlimited" : $maxPhases - $phasesCount;


// SCREENS
$maxScreens = $limitUser->getInfo($trial_prefix.'user_level_max_screen');
$myScreens = array_filter($limitUser->getDevices(), function($screenFound) use ($limit_user_ID) {
	return $screenFound['user_ID'] == $limit_user_ID;
});
$screensCount = count( $myScreens );
$screensPercentage = intval((100 * $screensCount) / $maxScreens);
$maxScreens = $maxScreens == 99999 ? "∞" : $maxScreens;
$screensLeft = $maxScreens == "∞" ? "Unlimited" : $maxScreens - $screensCount;


// LIVE PINS
$maxPins = $limitUser->getInfo($trial_prefix.'user_level_max_live_pin');
$myPins = array_filter($limitUser->getPins(), function($pinFound) use ($limit_user_ID) {
	return $pinFound['user_ID'] == $limit_user_ID && ($pinFound['pin_type'] == 'live' || $pinFound['pin_type'] == 'style');
});
$pinsCount = count( $myPins );
$pinsPercentage = intval((100 * $pinsCount) / $maxPins);
$maxPins = $maxPins == 99999 ? "∞" : $maxPins;
$pinsLeft = $maxPins == "∞" ? "Unlimited" : $maxPins - $pinsCount;


// LIVE PINS
$maxCommentPins = $limitUser->getInfo($trial_prefix.'user_level_max_comment_pin');
$myCommentPins = array_filter($limitUser->getPins(), function($pinFound) use ($limit_user_ID) {
	return $pinFound['user_ID'] == $limit_user_ID && $pinFound['pin_type'] == 'comment';
});
$commentPinsCount = count( $myCommentPins );
$commentPinsPercentage = intval((100 * $commentPinsCount) / $maxCommentPins);
$maxCommentPins = $maxCommentPins == 99999 ? "∞" : $maxCommentPins;
$commentPinsLeft = $maxCommentPins == "∞" ? "Unlimited" : $maxCommentPins - $commentPinsCount;


// LOAD
$maxLoad = $limitUser->getInfo($trial_prefix.'user_level_max_load');
$cached_userLoad = $cache->get('userload:'.$limit_user_ID);
if ( $cached_userLoad !== false ) $loadCount = $cached_userLoad;
else {

	$filesLoadMb = 0;
	foreach ($myPhases as $phaseFound) {

		$phaseDirectory = Phase::ID($phaseFound['phase_ID'])->phaseDir;

		$sizeMb = getDirectorySize($phaseDirectory, true); // True for the MB conversion
		$filesLoadMb += $sizeMb;

	}
	$loadCount = $filesLoadMb;

	// Set the cache
	$cache->set('userload:'.$limit_user_ID, $loadCount);

}
$loadPercentage = intval((100 * $loadCount) / $maxLoad);
$maxLoad = $maxLoad == 99999 ? "∞" : $maxLoad;
$loadLeft = $maxLoad == "∞" ? "Unlimited" : $maxLoad - $loadCount;

?>