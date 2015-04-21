<?php

$postString = file_get_contents('php://input');

$logfileName = "webhook.txt";

if (file_exists($logfileName)) {
    
    if (filesize($logfileName) > 10000) { $mode = "w+"; }
    else { $mode = "r+"; }
    
}
else { $mode = "w+"; }

$filehandle = fopen($logfileName, $mode);

fwrite($filehandle, $postString);