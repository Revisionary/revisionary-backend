<pre>
<?php

$db->where('user_ID', NULL, 'IS');
$phases = $db->get('phases');
//print_r($phases);

foreach($phases as $i => $phase) {

    $phase_ID = $phase['phase_ID'];
    $page_ID = $phase['page_ID'];

    $db->where('page_ID', $page_ID);
    $pageInfo = $db->getOne('pages');

    if (!$pageInfo) {
        echo "<b>$i - Page #$page_ID NOT FOUND for Phase #$phase_ID <br><br><br><br>";
        continue;
    }
    $user_ID = $pageInfo['user_ID'];


    $db->where('phase_ID', $phase_ID);
    $result = $db->update('phases', array(
        "user_ID" => $user_ID
    ));

    if (!$result) {
        echo "$i - Phase #$phase_ID will be assigned to User #$user_ID -> <b>NOT DONE</b> - ".$db->getLastError()." <br><br>";
    } else {
        echo "$i - Phase #$phase_ID will be assigned to User #$user_ID -> DONE <br>";
    }
}


