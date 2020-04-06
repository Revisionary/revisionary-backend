<?php

use MemcachedTags\MemcachedTags;

class Cache {


	private static $mc;
	private static $mcTags;
	private static $default_timeout = 60 * 60; // In seconds (1 hour)


	public function __construct($mc, $mcTags) {

		self::$mc = $mc;
		self::$mcTags = $mcTags;

    }


	public function get($key) {

		return self::$mc->get($key);

	}


	public function set($key, $value, $timeout = null) {


		// Get timeout
		$timeout = $timeout == null ? self::$default_timeout : $timeout;


		// Add tags
		$key_split = explode(':', $key);
		if ( count($key_split) > 1 && !is_numeric( $key_split[0] ) ) {
			$tag = $key_split[0];

			if ( !self::$mc->set($key, $value, $timeout) ) return false;
			return self::$mcTags->addTagsToKeys($tag, $key);
		}


		// Standard set
		return self::$mc->set($key, $value, $timeout);


	}


	public function getAllKeys() {

		return self::$mc->getAllKeys();

	}


	public function delete($key) {

		return self::$mc->delete($key);

	}


	public function deleteMulti(array $keys) {

		return self::$mc->deleteMulti($keys);

	}


	public function deleteKeysByTag($tag) {

		return self::$mcTags->deleteKeysByTag($tag);

	}


	public function flush() {

		return self::$mc->flush();

	}


}


// MEMCACHED
$mc = new Memcached();
$mc->addServer("memcached", 11211);

$mcTags = new MemcachedTags($mc);
$cache = new Cache($mc, $mcTags);