<?php

function model($name){
	return model . '/' . $name . '.php';
}

function view($name){
	return view . '/' . $name . '.php';
}

function controller($name){
	return controller . '/' . $name . '.php';
}

function __($langCode){
	global $lang;

	if(isset($lang[strtolower($langCode)]))
		return $lang[strtolower($langCode)];

	return $langCode;
}
