<?php

$postString = file_get_contents('php://input');

$logfileName = "webhook.txt";

if (file_exists($logfileName)) {
    
    if (filesize($logfileName) > 10000) { $mode = "w+"; }
    else { $mode = "a+"; }
    
}
else { $mode = "w+"; }

$filehandle = fopen($logfileName, $mode);

$dateString = date(DATE_RFC822);

fwrite($filehandle, "this page was loaded on " . $dateString . "\n");

fwrite($filehandle, $postString);

echo "<p>the date is: " . $dateString;