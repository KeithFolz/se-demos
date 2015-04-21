<?php

echo "<h2>Janrain webhook receiver page</h2>";

echo "<p>Here's what we've received recently from the webhook post: ";

$logfileName = "webhook.txt";

$content = "<p>" . file_get_contents($logfileName);

echo $content;