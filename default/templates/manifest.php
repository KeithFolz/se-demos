<?php

function getManifest ($typeOfDemo) {
    
    $manifest = array(); // just to avoid warnings about being uninitialized
    
    $manifest["title"]["type"] = "htmlString";
    $manifest["title"]["html"] = "<title>Janrain Demo Site</title>";
    
    $manifest["header"]["type"] = "htmlString";
    $manifest["header"]["html"] = getHeaderString();

    $manifest["content"]["type"] = "inlineHTML";
    $manifest["content"]["extension"] = "html";
        
    $manifest["janrainSettings"]["type"] = "inlineHTML";
    $manifest["janrainSettings"]["extension"] = "html";

    $manifest["otherScripts"]["type"] = "inlineHTML";
    $manifest["otherScripts"]["extension"] = "html";
    $manifest["otherScripts"]["optional"] = TRUE;
    
    /*
    $manifest["cssBlock"]["type"] = "inlineHTML";
    $manifest["cssBlock"]["extension"] = "html";
    */

    if ($typeOfDemo === "enterprise") {
        
        $manifest["janrainSettings"]["optional"] = TRUE;

        $manifest["janrain-init"]["type"] = "scriptRef";
        $manifest["janrain-init"]["extension"] = "js";

        $manifest["janrain-utils"]["type"] = "scriptRef";
        $manifest["janrain-utils"]["extension"] = "js";

        $manifest["signinlinks"]["type"] = "inlineHTML";
        $manifest["signinlinks"]["extension"] = "html";

        $manifest["widgetScreens"]["type"] = "inlineHTML";
        $manifest["widgetScreens"]["extension"] = "html";    
    }

    // settings for Social demos
    elseif ($typeOfDemo === "socialAjax" || $typeOfDemo === "socialRedirect") {

        $manifest["jwol"]["type"] = "inlineHTML";
        $manifest["jwol"]["extension"] = "html";

        $manifest["socialLogin"]["type"] = "inlineHTML";
        $manifest["socialLogin"]["extension"] = "html";

        if ($typeOfDemo === "socialAjax") {
            $manifest["ajaxScript"]["type"] = "path";
            $manifest["ajaxScript"]["extension"] = "php";

            $manifest["errorChecking"]["type"] = "inlineHTML";
            $manifest["errorChecking"]["extension"] = "html";            
        }
        else {
            $manifest["tokenURL"]["type"] = "path";
            $manifest["tokenURL"]["extension"] = "php";
        }
    }
    
    elseif ($typeOfDemo === "engagement") {
        $manifest["janrainSettings"]["optional"] = TRUE;
    }
    
    return $manifest;
}