<?php


function deleteDirectory($dir) {
    
    if ( !file_exists($dir) ) return true;

    if ( !is_dir($dir) ) return unlink($dir);

    foreach (scandir($dir) as $item) {

        if ($item == '.' || $item == '..') continue;

        if ( !deleteDirectory($dir . DIRECTORY_SEPARATOR . $item) ) return false;

    }

    return rmdir($dir);
}


function getDirectorySize($path, $inMB = false){
    $bytestotal = 0;
    $path = realpath($path);

    // Method 2
    if($path!==false && $path!='' && file_exists($path)){
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
            $bytestotal += $object->getSize();
        }
    }

    // MB Format
    if ($inMB) return number_format($bytestotal / 1048576, 1); 

    // Byte Format
    return $bytestotal;
}


function formatBytes($bytes, $precision = 2, $putUnits = true) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    // $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    $result = round($bytes, $precision);
    if ($putUnits) $result = ' '.$units[$pow];

    return $result; 
}