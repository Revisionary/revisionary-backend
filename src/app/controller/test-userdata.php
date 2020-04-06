<pre><?php

var_dump( currentTimeStamp('+1 week') );

$flush = true;
if ($flush) $cache->flush();
echo "
<details>
	<summary><h2 style='display: inline;'>CACHES (".count($cache->getAllKeys()).")</h2></summary>";
echo "<p style='padding-left: 20px;'>Array <br>(<br>";
$count = 0;
foreach ($cache->getAllKeys() as $key) {

	echo "    [$key] => ";
	echo !is_array($cache->get($key)) ? print_r( $cache->get($key), true) : "Array(...)";
	echo "<br>";

	$count++;
}
echo ")";
echo "</p>
</details>";


// If logged in
$user = User::ID();
if ($user) {


	// USER
	echo "
	<details>
		<summary><h2 style='display: inline;'>USER</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getInfo(), true )."</p>
	</details>";


	// USER DATA
	echo "
	<details open>
		<summary><h2 style='display: inline;'>USER DATA</h2></summary>
		<p style='padding-left: 20px;'>".print_r( getUserInfo(), true )."</p>
	</details>";


	// NOTIFICATIONS
	$notifications = $user->getNotifications();
	echo "
	<details>
		<summary><h2 style='display: inline;'>NOTIFICATIONS (".$notifications['totalCount'].")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $notifications['notifications'], true )."</p>
	</details>";


	// PROJECT CATEGORIES
	echo "
	<details>
		<summary><h2 style='display: inline;'>PROJECT CATEGORIES (".count($user->getProjectCategories()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getProjectCategories(), true )."</p>
	</details>";


	// PROJECTS
	echo "
	<details>
		<summary><h2 style='display: inline;'>PROJECTS (".count($user->getProjects()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getProjects(), true )."</p>
	</details>";


	// PAGE CATEGORIES
	echo "
	<details>
		<summary><h2 style='display: inline;'>PAGE CATEGORIES (".count($user->getPageCategories()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getPageCategories(), true )."</p>
	</details>";


	// PAGES
	echo "
	<details>
		<summary><h2 style='display: inline;'>PAGES (".count($user->getPages()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getPages(), true )."</p>
	</details>";


	// PHASES
	echo "
	<details>
		<summary><h2 style='display: inline;'>PHASES (".count($user->getPhases()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getPhases(), true )."</p>
	</details>";


	// DEVICES
	echo "
	<details>
		<summary><h2 style='display: inline;'>DEVICES (".count($user->getDevices()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getDevices(), true )."</p>
	</details>";


	// PINS
	echo "
	<details>
		<summary><h2 style='display: inline;'>PINS (".count($user->getPins()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getPins(), true )."</p>
	</details>";


	// SCREENS
	echo "
	<details>
		<summary><h2 style='display: inline;'>SCREENS (".count($user->getScreens()).")</h2></summary>
		<p style='padding-left: 20px;'>".print_r( $user->getScreens(), true )."</p>
	</details>";


}
echo "<br><br>";
if ($flush) $cache->flush();