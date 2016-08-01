<?php

/***************************************/

// Update this setting for your local environment
$paths["localRoot"] = "/Applications/MAMP/htdocs";

// You shouldn't need to change this
$paths["serverRoot"] = "/var/www/html";

// This gets tacked on to $localHostWebRoot or $serverWebRoot
$paths["webPath"] = "/JanrainDemoSites/default/";

$paths = updatePaths($paths);

include $paths["defaultPath"] . "includes/globals.php";

$configItems = array(); // initializing just to avoid warnings
/***************************************/

// You can uncomment any of these lines and override these values

/**********  TYPE OF DEMO  **************/
// "enterprise" // default, even if you don't enter a value
// "socialAjax"
// "socialRedirect"

$typeOfDemo = "socialRedirect";

/******* Settings for all types of demos *************/
// PAGE TITLE
$configItems["title"] = "<title>Janrain Social Login - IDP permissions in full screen (no popups)</title>";

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

// PATH TO ANY OTHER SCRIPTS YOU WANT TO ADD
// Scripts should all be in a single html file
// enclosed by <script></script> tags.
// Note: default value is empty. 
// $configItems["otherScripts"] = "script.html";

/*************** SOCIAL-AJAX *****************/

// $configItems["socialLoginScript"] = $paths["templates"] . "/socialLogin.html";

// $configItems["janrainSettings"] = $paths["default"] . "/scripts/social/ajax/settings.html";

// $configItems["ajaxScript"] = $paths["default"] . "/scripts/social/ajax/ajax.php";

// $configItems["errorChecking"] = $paths["default"] . "/scripts/social/ajax/errorChecking.html";

// $configItems["jwol"] = $paths["default"] . "/scripts/social/ajax/jwol.html";

/************** SOCIAL-REDIRECT **************/



/*************** SOCIAL LOGIN SETTINGS ************/

// $configItems["tokenURL"] = $paths["templates"] . "/tokenURL.php";

// $configItems["tokenURL"] = "http://www.sampleTokenUrl.com/not-needed-for-client-side-validation";

showPage($configItems, $paths, $typeOfDemo);

/*********** This function figures out whether you're local or remote ********/
// so that the correct "includes" file can be loaded.

function updatePaths($paths) {

    // figure out whether we're on localhost or the server
    if (is_readable($paths["localRoot"] . $paths["webPath"])) { 
        $defaultPath = $paths["localRoot"] . $paths["webPath"];
    }
    else { $defaultPath = $paths["serverRoot"] . $paths["webPath"]; }
    
    $paths["defaultPath"] = $defaultPath;
    
    // current working directory comes in handy
    $paths["cwd"] = getcwd();
    
    return $paths;
}