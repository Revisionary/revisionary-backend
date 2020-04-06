<?php

class Space {



}

// Configure a client using Spaces
$s3 = new SpacesConnect($_ENV['S3_KEY'], $_ENV['S3_SECRET'], $_ENV['S3_BUCKET'], $_ENV['S3_REGION']);
