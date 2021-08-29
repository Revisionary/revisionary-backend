<?php

exit;
$notifications = $User->getNotifications();
die_to_print($notifications);

var_dump( $notifications );
exit;

$i = 0;
while ($i < 10) { $i++;


	//var_dump( User::ID($i)->canAccess(33, "pin") );

	Notify::ID(6)->web("sent test notification $i", "pin", 1);
	sleep(2);

}

exit;


$sent = Notify::ID([1, 5])->mail(
	'Konu test',
	$message
);
if ($sent) echo "SENT! $sent";