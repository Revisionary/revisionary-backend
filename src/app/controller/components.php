<?php


$additionalHeadJS = [
	'process.js',
	'revise-globals.js',
	'revise-functions.js',
	'vendor/jquery-ui.min.js',
	'vendor/Autolinker.min.js',
	'vendor/autosize.min.js',
	'vendor/diff.js',
	'vendor/spectrum.js'
];

$additionalBodyJS = [
	'common.js',
	'revise-page.js'
];

$page_title = "Site Components";
require view('components');