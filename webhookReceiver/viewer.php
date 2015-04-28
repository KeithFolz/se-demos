<?php

echo "<h2>Janrain webhook receiver page</h2>";

echo "<p>Here's what we've received recently from the webhook post: ";

$logfileName = "webhook.txt";

$content = "";

// $content = "<p>" . file_get_contents($logfileName);

$lines = file($logfileName);

for ($i = 1; $i <= 10; $i++) {
    if (array_key_exists($i, $lines)) {
        $content .= "<p>" . $lines[$i] . "</p>\n";
    }
    else { break; }
    
}

echo $content;