<?php

// These are the category names to display in the leftNav
$displayNames["enterprise"] = "Enterprise";
$displayNames["socialLogin"] = "Social Login";
$displayNames["socialRedirect"] = "Social Login";
$displayNames["socialAjax"] = "Social Login";
$displayNames["engagement"] = "Engagement";

$navFolder["enterprise"] = "enterprise";
$navFolder["socialRedirect"] = "socialLogin";
$navFolder["socialAjax"] = "socialLogin";
$navFolder["engagement"] = "engagement";

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
    "widget_visible_immediately" => "Widget Visible Immediately"
);

$links["socialLogin"] = array (
    "backplane" => "Backplane",
    "badgeville" => "Badgeville",
    "noPopup" => "FullScreen IDPs",
    "google_analytics" => "Google Analytics",
    "standard-ajax" => "Standard - ajax",
    "standard-redirect" => "Standard - redirect",
);

$links["engagement"] = array (
    "socialSharing" => "Social Sharing",
    "commenting" => "Commenting",
    "live_event_chat" => "Live Event Chat",
    "site_activity" => "Site Activity",
    "curated_social" => "Curated Tweets"
);
