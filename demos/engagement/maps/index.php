<?php

/***************************************/
$params["homeDir"] = "JanrainDemoSites";

// Finds out where we are in the filesystem and fixes path to fsHome

$params["fsHome"] = strstr(getcwd(), $params["homeDir"], TRUE) . $params["homeDir"];

include $params["fsHome"] . "/includes/includes.php";

/***************************************/

$params["typeOfDemo"] = "engagement";
$params["title"] = "Janrain Engagement: Maps";
$params["header"] = $params["title"];

$demo = new demo($params);

$demo->addCSS("http://cdn.arktan.com/jqplot/css/jquery.jqplot.css", "fileRef");
$demo->addCSS("http://cdn.arktan.com/qtip/css/jquery.qtip.css", "fileRef");

$demo->show();