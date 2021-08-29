<pre>
<?php

// $res = $db->rawQuery("
// 	SELECT
// 		(SELECT COUNT(*) FROM projects pr) as projectsCount, 
// 		(SELECT COUNT(*) FROM pages pg) as pagesCount,
// 		(SELECT COUNT(*) FROM phases ph) as phasesCount,
// 		(SELECT COUNT(*) FROM devices d) as devicesCount,
// 		(SELECT COUNT(*) FROM pins p) as contentPinsCount,
// 		(SELECT COUNT(*) FROM pins) as commentPinsCount
// ");
$res = User::ID()->usage();
print_r($res);


// $db->join('projects pr', 'pr.user_ID = 6', 'left');
// $db->join('pages pg', 'pg.user_ID = 6', 'left');
// $db->join('phases ph', 'ph.user_ID = 6', 'left');


// $db->where('u.user_ID', 6);
// $res = $db->get("users u", null, "
// 	COUNT(DISTINCT pr.project_ID) as projectsCount,
// 	COUNT(DISTINCT pg.page_ID) as pagesCount
// ");
// print_r($res);

//echo "Last executed query was ". $db->getLastQuery();