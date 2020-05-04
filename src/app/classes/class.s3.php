<?php

class Space {



}

// Configure a client using Spaces
if ($config['env']['s3_key']) 
	$s3 = new SpacesConnect($config['env']['s3_key'], $config['env']['s3_secret'], $config['env']['s3_bucket'], $config['env']['s3_region']);
