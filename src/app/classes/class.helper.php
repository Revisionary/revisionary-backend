<?php

class Helper {

	public static function Load() {

		$helperDir = realpath('.') . '/app/helper';
		if ($dh = opendir($helperDir)) {

			while($file = readdir($dh)) {
				if (is_file($helperDir . '/' . $file) && substr($file, -4) == ".php") {
					require $helperDir . '/' . $file;
				}
			}

		}

	}

}