<?php

/***************************************/
$params["homeDir"] = "JanrainDemoSites";

// Finds out where we are in the filesystem and fixes path to fsHome

$params["fsHome"] = strstr(getcwd(), $params["homeDir"], TRUE) . $params["homeDir"];

include $params["fsHome"] . "/includes/includes.php";

/***************************************/

$params["title"] = "<title>Janrain Enterprise - Webhooks</title>";

$demo = new demo($params);

$params["signinLinks"] = " ";

$params["content"] = getWebhookLog();

$demo->show();