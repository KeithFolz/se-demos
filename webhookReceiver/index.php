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

// If the filehandle is valid, then echo a message and 
// write the input
// to the logfile. If the filehandle is not valid, echo a message.
if ($filehandle) {
    echo "<p>The log file is writeable.</p>";
    fwrite($filehandle, $postString);
    fwrite($filehandle, "\n");
}
else { echo "<p>the log file is not writeable.</p>"; }