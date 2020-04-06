<?php

// Device ID
if ( !is_numeric(request('device_ID')) ) return false;
$device_ID = intval( get('device_ID') );


// Device check
$deviceData = Device::ID($device_ID);
if (!$deviceData) return false;


$device = $deviceData->getInfo();
//die_to_print($device);


$image_url = $deviceData->getImageURL();
//die_to_print($image_url);



$page_title = $device['page_name'];
require view('serve-image');