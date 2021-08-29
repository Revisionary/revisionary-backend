<?php

exit;
Mail::ASYNCSEND(
	'bilalltas@gmail.com,bill@twelve12.com',
	urlencode('Bulk mail test'),
	urlencode('Test mail')
);
die();



Mail::SEND(
	'bilalltas@gmail.com,bill@twelve12.com',
	'asdasd',
	'Test mail'
);
die();