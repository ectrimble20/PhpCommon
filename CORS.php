<?php
//Set the time-zone to you're designed time-zone.
//See http://us1.php.net/manual/en/timezones.php for details as to what time-zones are supported.
date_default_timezone_set('America/New_York');
//Headers to allow CORS, or Cross-Origin Resource Sharing
//This is required to handle REST like functionality
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: text/html");