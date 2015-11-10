<?php

/***************************************/
$params["homeDir"] = "JanrainDemoSites";

// Finds out where we are in the filesystem and fixes path to fsHome

$params["fsHome"] = strstr(getcwd(), $params["homeDir"], TRUE) . $params["homeDir"];

include $params["fsHome"] . "/includes/includes.php";

/***************************************/
$params["typeOfDemo"] = "socialRedirect";
$params["title"] = "Janrain Demo Site: Social Login - Standard w/Redirect";

$demo = new demo($params);

$demo->show();