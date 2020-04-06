<?php

$type = request('type');

if ($type){
	if (file_exists(dir . '/app/ajax/' . $type . '.php')) {
		require dir . '/app/ajax/' . $type . '.php';
	}
}