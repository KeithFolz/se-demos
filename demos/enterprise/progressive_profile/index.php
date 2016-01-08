<?php
// Janrain Demo generator
// v0.3
// 2014-10-28 tsmith@janrain.com

/***************************************/
$homeDirName = "JanrainDemoSites";

// Finds out where we are in the filesystem and fixes path to home
$paths["home"] = strstr(getcwd(), $homeDirName, TRUE) . $homeDirName;

include $paths["home"] . "/demoGenerator.php";
/***************************************/

// You can uncomment any of these lines and override these values
// Note: values of type "osHome" should be defined relative to the OS root
// values for type "webHome" should be defined relative to the web root

/**********  TYPE OF DEMO  **************/
// "enterprise"
// "socialAjax"
// "socialRedirect"
// use "enterprise" for ugc

$typeOfDemo = "enterprise";

/******* Settings for all types of demos *************/
// PAGE TITLE
// $configItems["title"] = "<title>Janrain Demo Site</title>";

// PAGE HEADING
// $configItems["header"] = "<h1>Janrain $thisName Demos</h1>";
// $thisName is determined by $typeOfDemo

// PAGE CONTENT
// $configItems["content"] = $paths["osHome"] . "/content.html";

// PATH TO ANY OTHER SCRIPTS YOU WANT TO ADD
// Scripts should all be in a single html file enclosed by 
// <script></script> tags. Note: default value is empty. 
$configItems["otherScripts"] = "script.html";

/*************** ENTERPRISE & UGC DEMO SETTINGS ***************/
// PATH TO janrain-init
// $configItems["janrain-init"] = $paths["webHome"] . "/janrain-init.js";

// PATH TO janrain-utils
// $configItems["janrain-utils"] = $paths["webHome"] . "/janrain-utils.js";

// PATH TO LINKS
// $configItems["signinlinks"] = $paths["osHome"] . "/signinlinks.html";

// PATH TO WIDGET SCREENS
// $configItems["widgetScreens"] = $paths["osHome"] . "/widgetScreens.html";

/*************** SOCIAL DEMO SETTINGS ***************/
// Applicable for both socialAjax and socialRedirect

// PATH TO SOCIAL LOGIN SCRIPT
// $configItems["socialLogin"] = $paths["osHome"] . "/socialLogin.html";

// PATH TO JWOL SCRIPT // JWOL == janrainWidgetOnLoad()
// $configItems["jwol"] = $paths["osHome"] . "jwol.html";

// PATH TO JANRAIN SETTINGS // janrain.settings.*
// $configItems["janrainSettings"] = $paths["osHome"] . "/janrainSettings.html";

    /**************** SOCIAL-AJAX SETTINGS ***********/
    // PATH TO AJAX SCRIPT // handles the request coming from browser
    // $configItems["ajaxScript"] = $paths["webHome"] . "/ajaxScript.php";
    
    // PATH TO ERROR CHECKING SCRIPT // sends output to console 
    // $configItems["errorChecking"] = $paths["osHome"] . "/errorChecking.html";

    /************** SOCIAL-REDIRECT **************/
    // PATH TO TOKEN URL
    // $configItems["tokenURL"] = $paths["webHome"] . "/tokenURL.php";

/*************** END OF SETTINGS ************/

showPage($configItems, $typeOfDemo);