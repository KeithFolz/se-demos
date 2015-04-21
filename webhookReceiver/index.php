<?php

echo "<h2>Janrain webhook receiver page.</h2>";

echo "<p>Here's what we've received from the webhook post: ";

$postString = file_get_contents('php://input');

echo $postString;