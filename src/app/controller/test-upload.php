<pre><?php


// $pin_ID = 38;
// $file_path = "pin-images/pin-$pin_ID";
// $file = new File($file_path, "s3");
// //var_dump( $s3->DoesObjectExist($file_path) );
// var_dump( $file );
// die();


$url = "https://revisionary.sfo2.digitaloceanspaces.com/pin-images/pin-40/attachments/ijufd8m4uh.jpg";
$file_path = substr(parseUrl($url)['path'], 1);

$file = new File($file_path, "s3");
var_dump( $file->fileExists() );
//var_dump( $file->delete() );

die_to_print( $file_path );


// if(extension_loaded('gd')) {
//     print_r(gd_info());
// }
// else {
//     echo 'GD is not available.';
// }

// if(extension_loaded('imagick')) {
//     $imagick = new Imagick();
//     print_r($imagick->queryFormats());
// }
// else {
//     echo 'ImageMagick is not available.';
// }

// exit;


// // Resizer
// $img = resize_image(cache."/bill.png", 1920, 1920);
// die($img);


//die_to_print( getimagesize(cache."/bill.png") );


if( isset($_FILES['image']) && $User->getInfo('user_level_ID') === 1 ) {

	// print_r( $_FILES );
	// exit;

	$temp_file_location = $_FILES['image']['tmp_name'];	
	$file_name = str_replace(' ', '-', basename($_FILES['image']['name'])); // Update the name

	//die_to_print([$file_name, $temp_file_location, basename($temp_file_location)]);
	//die_to_print( dirname(cache."/$file_name") );

	resize_image("$temp_file_location", 250, 250);

	//die_to_print($img);

	$file = new File($temp_file_location);
	$result = $file->upload(
		cache."/$file_name", "local",
		//"avatars/$file_name", "s3",
		true
	);

	//echo parse_url($result, PHP_URL_PATH)."<br>";

	var_dump($result);

}

?></pre>
<form action="" method="POST" enctype="multipart/form-data">
	<input type="file" name="image" data-max-size="3145728">
	<button>Send</button>
</form>

<pre>
<?php



$client = $s3;


// List all files and folders
$objects = $s3->ListObjects();
die_to_print( $objects );


// Listing all Spaces in the region
$spaces = $s3->ListSpaces();
//die_to_print( $spaces );


// foreach ($spaces['Buckets'] as $space){
//     echo $space['Name']."\n";
// }



// Delete a file/folder.
//$result = $s3->DeleteObject("test");
//die_to_print( $result );


// Delete a file/folder recursive
// $s3->DeleteObject("asd/", true);


//var_dump($result);
exit;