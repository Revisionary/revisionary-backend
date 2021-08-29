<?php

// REQUESTS

function get($name, $noFilter = false) {

	if ($noFilter) return $_GET[$name];

	return filterRequest($_GET, $name);

}

function post($name, $noFilter = false) {

	if ($noFilter) return $_POST[$name];

	return filterRequest($_POST, $name);

}

function request($name, $noFilter = false) {

	if ($noFilter) return $_REQUEST[$name];

	return filterRequest($_REQUEST, $name);

}