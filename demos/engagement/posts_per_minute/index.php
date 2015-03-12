<?php

/***************************************/
$homeDirName = "JanrainDemoSites";

// Finds out where we are in the filesystem and fixes path to home
$paths["home"] = strstr(getcwd(), $homeDirName, TRUE) . $homeDirName;

include $paths["home"] . "/demoGenerator.php";
/***************************************/

// You can uncomment any of these lines and override these values

/**********  TYPE OF DEMO  **************/
// "enterprise" // default, even if you don't enter a value
// "socialAjax"
// "socialRedirect"

$typeOfDemo = "engagement";

/******* Settings for all types of demos *************/
// PAGE TITLE
$configItems["title"] = "<title>Janrain Posts Per Minute</title>";

// PAGE CONTENT
// $configItems["content"] = "";
// The default value for content is determined by $typeOfDemo

/*************** ENTERPRISE DEMO SETTINGS ***************/
// PATH TO janrain-init
// $configItems["janrain-init"] = "<script src = '" . $webPath . "scripts/janrain-init.js'></script>";

// PATH TO janrain-utils
// $configItems["janrain-utils"] = "<script src = '" . $webPath . "scripts/janrain-utils.js'></script>";

// PATH TO LINKS
// $configItems["links"] = $defaultPath . "templates/links.html";

// PATH TO WIDGET SCREENS
// $configItems["widgetScreens"] = $defaultPath . "templates/widgetScreens.html";

/*************** SOCIAL-AJAX *****************/

// $configItems["socialLoginScript"] = $paths["templates"] . "/socialLogin.html";

// $configItems["janrainSettings"] = $paths["default"] . "/scripts/social/ajax/settings.html";

// $configItems["ajaxScript"] = $paths["default"] . "/scripts/social/ajax/ajax.php";

// $configItems["errorChecking"] = $paths["default"] . "/scripts/social/ajax/errorChecking.html";

// $configItems["jwol"] = $paths["default"] . "/scripts/social/ajax/jwol.html";

/************** SOCIAL-REDIRECT **************/

// PATH TO ANY OTHER SCRIPTS YOU WANT TO ADD
// Scripts should all be in a single html file
// enclosed by <script></script> tags.
// Note: default value is empty.
// $configItems["otherScripts"] = "scripts.html";
$configItems["otherScripts"] = "script.html";


/*************** SOCIAL LOGIN SETTINGS ************/

// $configItems["tokenURL"] = $paths["templates"] . "/tokenURL.php";

// $configItems["tokenURL"] = "http://www.sampleTokenUrl.com/not-needed-for-client-side-validation";

showPage($configItems, $typeOfDemo);
