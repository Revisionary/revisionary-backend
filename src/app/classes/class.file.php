<?php

class File {

	private $file_path;
	private $file_location;
	private $mime_type;


	// SETTERS:
	public function __construct(
		string $file_path,
		string $file_location = "local" // Or use "s3"
	) {
		global $s3;


		// Selected file path
		$this->file_path = $file_path;


		// Selected file location
		$this->file_location = $file_location;


		// Check the file if exists
		if ( !$this->fileExists() ) return false;


		// Check for the mime type
		if ($file_location == "local") {

			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$this->mime_type = finfo_file($finfo, $file_path);
			finfo_close($finfo);

		}

		if ($file_location == "s3") {

			$this->mime_type = $s3->GetObject($file_path)['ContentType'];

		}


		return $this->mime_type;

	}
	

	// ACTIONS:
	public function fileExists(
		string $file_path = null,
		string $file_location = null // Or use "s3"
	) {
		global $s3;


		// By default, use initial file info
		if ($file_path == null) $file_path = $this->file_path;
		if ($file_location == null) $file_location = $this->file_location;


		return $file_location == "s3" ? $s3->DoesObjectExist($file_path) : file_exists($file_path);

	}


	// This can upload a file
	public function upload(
		string $file_destination = null, // This also includes the file name
		string $destination_location = "local", // Or use "s3"
		bool $overwrite = false
	) {
		global $s3;


		// Early exit if no file
		if ( !$this->fileExists() ) return false;


		// File name detection if not destination entered
		if ($file_destination == null) $file_destination = basename($this->file_path);


		// Check if file exist if not force upload
		if ( !$overwrite && $this->fileExists($file_destination, $destination_location) ) return false;


		// Do action
		if ($destination_location == "s3") {

			$result = $s3->UploadFile($this->file_path, "public", $file_destination, $this->mime_type)['ObjectURL'];

		}
		
		if ($destination_location == "local") {

			// Create destination folder if not exists
			$destination_directory = dirname($file_destination);
			if ( !file_exists($destination_directory) ) mkdir($destination_directory, 0755, true);

			$result = move_uploaded_file($this->file_path, $file_destination);

		}


		return $result;

	}


	// This can delete file or folder
	public function delete(
		string $file_path = null,
		string $file_location = null // Or use "s3"
	) {
		global $s3;


		// By default, use initial file info
		if ($file_path == null) $file_path = $this->file_path;
		if ($file_location == null) $file_location = $this->file_location;


		// Early exit if no file
		if ( !$this->fileExists($file_path, $file_location) && $file_location != "s3" ) return false;


		// Do action
		if ($file_location == "local") {

			$result = $this->deleteLocal($file_path);

		}

		if ($file_location == "s3") {

			$delete = $s3->DeleteObject($file_path, true);
			$result = $delete || $delete === null;

		}


		return $result;

	}


	public function deleteLocal(
		string $file_path = null // Can be file or directory path
	) {

		
		// By default, use initial file info
		if ($file_path == null) $file_path = $this->file_path;
	

		// Early exit if no file
		if ( !file_exists($file_path) ) return true;
	

		// Directly delete if this is a file
		if ( !is_dir($file_path) ) return unlink($file_path);
	

		// Check the sub directories
		foreach (scandir($file_path) as $item) {
	
			if ($item == '.' || $item == '..') continue;
	
			if ( !$this->deleteLocal($file_path . DIRECTORY_SEPARATOR . $item) ) return false;
	
		}
	
		return rmdir($file_path);
	}


}