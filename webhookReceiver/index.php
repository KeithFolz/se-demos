<?php

// This gets the json from the webhook
$postString = file_get_contents('php://input');

$logfileName = "webhook.txt";

// Check to see if the logfile exists
// If it does, check its size. If it's more than 1 MB,
// erase it and start over
if (file_exists($logfileName)) {
    
    if (filesize($logfileName) > 1000) { $mode = "w+"; }
    else { $mode = "a+"; }
    
}
else { $mode = "w+"; }

$filehandle = fopen($logfileName, $mode);

// $dateString = date(DATE_RFC822);

// fwrite($filehandle, "this page was loaded on " . $dateString . "\n");

fwrite($filehandle, $postString);
fwrite($filehandle, "\n");

// echo "<p>the date is: " . $dateString;