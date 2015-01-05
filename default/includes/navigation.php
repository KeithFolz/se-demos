<?php

// These are the category names to display in the leftNav
$displayNames["enterprise"] = "Enterprise";
$displayNames["socialLogin"] = "Social Login";
$displayNames["socialAjax"] = "Social Login";

// Value on left is the folder name
// Value on right is the name displayed in the UI
$links["enterprise"] = array (
    "adobe_analytics" => "Adobe Analytics",
    "adsense" => "Adsense",
    "adsense_dfp" => "AdSense / DFP",
    "backplane" => "Backplane",
    "badgeville" => "Badgeville",
    "federate" => "Federate (SSO)",
    "noPopup" => "FullScreen IDPs",
    "google_analytics" => "Google Analytics",
    "standard" => "Standard",
    "widget_visible_immediately" => "Widget Visible Immediately",
    "commenting" => "Commenting"
);

$links["socialLogin"] = array (
    "backplane" => "Backplane",
    "badgeville" => "Badgeville",
    "client_side_validation" => "Client Side Validation",
    "noPopup" => "FullScreen IDPs",
    "google_analytics" => "Google Analytics",
    "standard-ajax" => "Standard - ajax",
    "standard-redirect" => "Standard - redirect"
);

// For the HTML <h1> value
function getDisplayName($typeOfDemo) {
    if ($typeOfDemo === "enterprise") {
        $displayName = "Enterprise";
    }
    elseif ($typeOfDemo === "socialLogin") {
        $displayName = "Social Login";
    }
}
