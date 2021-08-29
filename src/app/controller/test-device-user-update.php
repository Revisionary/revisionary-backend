<pre>
<?php

$db->where('user_ID', NULL, 'IS');
$devices = $db->get('devices');
//print_r($devices);

foreach($devices as $i => $device) {

    $device_ID = $device['device_ID'];
    $phase_ID = $device['phase_ID'];

    $db->where('phase_ID', $phase_ID);
    $phaseInfo = $db->getOne('phases');

    if (!$phaseInfo) {
        echo "<b>$i - Phase #$phase_ID NOT FOUND for Device #$device_ID <br><br><br><br>";
        continue;
    }
    $user_ID = $phaseInfo['user_ID'];


    $db->where('device_ID', $device_ID);
    $result = $db->update('devices', array(
        "user_ID" => $user_ID
    ));

    if (!$result) {
        echo "$i - Device #$device_ID will be assigned to User #$user_ID -> <b>NOT DONE</b> - ".$db->getLastError()." <br><br>";
    } else {
        echo "$i - Device #$device_ID will be assigned to User #$user_ID -> DONE <br>";
    }
}


