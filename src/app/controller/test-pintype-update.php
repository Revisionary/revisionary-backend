<pre>
<?php

$db->where ('pin_type', 'live');
if ($db->update ('pins', ['pin_type' => 'content']))
    echo $db->count . ' records were updated';
else
    echo 'update failed: ' . $db->getLastError();