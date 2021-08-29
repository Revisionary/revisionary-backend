<?php

function die_to_print($array, $die = true) {

	echo "<pre>".print_r($array, true)."</pre>";
	if ($die) die();

}
