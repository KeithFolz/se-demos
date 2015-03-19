<?php

/***************************************/
$homeDir = "JanrainDemoSites";

// Finds out where we are in the filesystem and fixes path to fsHome

$fsHome = strstr(getcwd(), $homeDir, TRUE) . $homeDir;

include $fsHome . "/includes/includes.php";

/***************************************/

// This section is where you set all your parameters
// 
// To get a look at all available parameters, set the demo type and run this
// page with the parameter mode=debug
// 
// Key elements you should set
// 
// typeOfDemo = engagement || enterprise || socialAjax || socialRedirect
// 
// $params["typeOfDemo"] = "engagement";
//
// title = title for the demo, enclosed in <title> tags
// 
// $params["title"] = "<title>Janrain Demo Site</title>";

$params["title"] = "<title>Janrain Enterprise with Widget Visible Immediately</title>";

// End of section where you set parameters

// This command builds the demo and sends the html page to the browser
showDemo($params, $fsHome);
