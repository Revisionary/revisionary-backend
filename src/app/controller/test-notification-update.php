<pre>
<?php

$db->where('object_type', 'pin');
$notifications = $db->get('notifications');
//print_r($notifications);


foreach($notifications as $i => $notification) {


    $pinData = Pin::ID($notification['pin_ID']);
    
    $device_ID = $pinData->device_ID;
    echo "$i - Device ID: $device_ID<br>";

    $phase_ID = $pinData->phase_ID;
    echo "$i - Phase ID: $phase_ID<br>";

    $page_ID = $pinData->page_ID;
    echo "$i - Page ID: $page_ID<br>";

    $project_ID = $pinData->project_ID;
    echo "$i - Project ID: $project_ID<br><br>";


    $db->where('notification_ID', $notification['notification_ID']);
    $result = $db->update('notifications', array(
        "device_ID" => $device_ID,
        "phase_ID" => $phase_ID,
        "page_ID" => $page_ID,
        "project_ID" => $project_ID
    ));

    if (!$result) {
        echo "$i - ".$db->getLastError()."<br><br><br>";
    }
}


