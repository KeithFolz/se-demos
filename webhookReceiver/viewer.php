<?php

echo "<h2>Janrain webhook receiver page.</h2>";

echo "<p>Here's what we've received recently from the webhook post: ";

$logfileName = "webhook.txt";

$filehandle = fopen($logfileName, "r");

echo $filehandle;