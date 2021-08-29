<?php

// FILTERS

function filterString(string $str) {
  return htmlspecialchars(trim($str));
}

function filterArray(array &$array, $filter = true) {

    array_walk_recursive($array, function (&$value) use ($filter) {
        $value = trim($value);
        if ($filter) {
            $value = filter_var($value, FILTER_SANITIZE_STRING);
            $value = filterString($value);
        }
    });

    return $array;
}

function filterRequest($handler, $name) {

	if (isset($handler[$name])) {

		// If Array
		if ( is_array($handler[$name]) ) return filterArray($handler[$name]);

		// If String
		return filterString($handler[$name]);

	}

	return false;
}