<?php

if ( !isset($_REQUEST['env']) ) die('ENTER ENV');

var_dump(
    getenv($_REQUEST['env'])
);
