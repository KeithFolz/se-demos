<?php

class demo {
    public $params;
    public $fsHome;
    public $homeDir;
        
    private $paths;
    private $components;
    
    public function setDemoType() {
        if (empty($this->params["typeOfDemo"])) {
            $this->params["typeOfDemo"] = "enterprise";
        }
    }

    // sets the list of fields that are needed for this demo type
    // some may be optional
    public function setComponents() {
        
        // HEAD elements

        // <title>Janrain Demo Sites</title>
        $this->components["title"]["type"] = "file";
        $this->components["title"]["ext"] = "html";
        $this->components["title"]["parent"] = "head";
        $this->components["title"]["required"] = TRUE;
        
        $this->components["stylesheet"]["type"] = "css";
        $this->components["stylesheet"]["ext"] = "css";
        $this->components["stylesheet"]["parent"] = "head";
        $this->components["stylesheet"]["required"] = TRUE;
        
        $this->components["navigation"]["type"] = "fileRef";
        $this->components["navigation"]["ext"] = "js";
        $this->components["navigation"]["parent"] = "head";
        $this->components["navigation"]["required"] = TRUE;
        
        // <script>
        //      janrain.settings.width = '330';
        // </script>
        $this->components["janrainSettings"]["type"] = "file";
        $this->components["janrainSettings"]["ext"] = "html";
        $this->components["janrainSettings"]["parent"] = "head";
        $this->components["janrainSettings"]["required"] = FALSE;
        
        // <script>some random code</script>
        // <script>another script</script>
        $this->components["otherScripts"]["type"] = "file";
        $this->components["otherScripts"]["ext"] = "html";
        $this->components["otherScripts"]["parent"] = "head";
        $this->components["otherScripts"]["required"] = FALSE;
        
        // BODY elements
                
        // <h1>Janrain Social Login Demos</h1>
        $this->components["heading"]["type"] = "file";
        $this->components["heading"]["ext"] = "html";
        $this->components["heading"]["parent"] = "body";
        $this->components["heading"]["required"] = TRUE;

        // main content of page
        $this->components["content"]["type"] = "file";
        $this->components["content"]["ext"] = "html";
        $this->components["content"]["parent"] = "body";
        $this->components["content"]["required"] = TRUE;
        
        // Demo-specific settings
        if ($this->params["typeOfDemo"] === "enterprise") {
        
            // <script src = 'janrain-init.js'></script>
            $this->components["janrain-init"]["type"] = "fileRef";
            $this->components["janrain-init"]["ext"] = "js";
            $this->components["janrain-init"]["parent"] = "head";
            $this->components["janrain-init"]["required"] = TRUE;
            
            // <script src = 'janrain-utils.js'></script>
            $this->components["janrain-utils"]["type"] = "fileRef";
            $this->components["janrain-utils"]["ext"] = "js";
            $this->components["janrain-utils"]["parent"] = "head";
            $this->components["janrain-utils"]["required"] = TRUE;
            
            // <a href="#" id="captureSignInLink" 
            // onclick="janrain.capture.ui.renderScreen('signIn')">
            // Sign In / Sign Up</a>
            $this->components["signinLinks"]["type"] = "file";
            $this->components["signinLinks"]["ext"] = "html";
            $this->components["signinLinks"]["parent"] = "body";
            $this->components["signinLinks"]["required"] = TRUE;
            
            // Janrain JTL + HTML
            $this->components["widgetScreens"]["type"] = "file";
            $this->components["widgetScreens"]["ext"] = "html";
            $this->components["widgetScreens"]["parent"] = "body";
            $this->components["widgetScreens"]["required"] = TRUE;

        }
        
        echo "<p>The type of demo is " . $this->params["typeOfDemo"];
    }
    
    // sets values for various needed paths in the filesystem and web
    public function setPaths($home) {
        
        $this->paths["home"] = "/" . basename($home);
        $this->paths["default"] = $this->paths["home"] . "/default";
        $this->paths["templates"] = $this->paths["default"] . "/templates";
        $this->paths["typeOfDemo"] = $this->paths["templates"] . "/" . $this->params["typeOfDemo"];
        
        $this->paths["fsHome"] = strstr($home, $this->paths["home"], TRUE);
         
        $this->paths["cwd"] = getcwd(); // current working directory
        
        echo "<p>Here are the paths: ";
        
        foreach ($this->paths as $key => $path) {
            echo "<p>" . $key . " is " . $path;
        }
    }
    
    public function setValues() {
        
        foreach ($this->components as $componentName => $properties) {
            echo "<p>The component is " . $componentName;
            
            if (empty($this->params[$componentName])) {
                echo "<p>The user did not explicitly supply a value for this component.";
                
                if ($this->components[$componentName]["required"] === TRUE) {
                    
                    $filePath = $this->getFullFilePath($componentName, $this->paths["cwd"]);
                
                    if (file_exists($filePath)) {
                        echo "<p>A local file exists.";

                        $this->components[$componentName]["finalValue"] = file_get_contents($filePath);

                    }
                    else {
                        echo "<p>A local file does not exist.";
                        $this->assignDefaultValue($componentName);
                    }
                }
                else {
                    $this->components[$componentName]["finalValue"] = "<p>Optional</p>";
                }
            }
            else {
                echo "<p>The user supplied the following value: <xmp>" . $this->params[$componentName] . "</xmp>";
                $this->components[$componentName]["finalValue"] = $this->params[$componentName];

            }
            echo "<hr>";
        }
    }
    
    private function assignDefaultValue($componentName) {
        
        $path = $this->getDefaultPath($componentName);
        
        $filePath = $this->getFullFilePath($componentName, $path);

        if ($componentName === "stylesheet") {
            $this->components[$componentName]["finalValue"] = 
               "<link rel='stylesheet' href='" . $filePath . "'/>";
        }
        else {

            if ($this->components[$componentName]["type"] === "file") {
                
                $filePath = $this->paths["fsHome"] . $filePath;

                $this->components[$componentName]["finalValue"] = file_get_contents($filePath);
            }
            elseif ($this->components[$componentName]["type"] === "fileRef") {
                $this->components[$componentName]["finalValue"] = 
                     "<script src='$filePath' type='text/javascript'></script>";
            }
        }
    }
    
    private function getDefaultPath($componentName) {
        
        if ($componentName === "stylesheet" || $componentName === "navigation") {
            $path = $this->paths["default"];
        }
        else {
            $path = $this->paths["typeOfDemo"];
        }
        
        return $path;
    }

    
    private function getFileName($componentName) {
        return $componentName . "." . $this->components[$componentName]["ext"];
    }
    
    private function getFullFilePath($componentName, $filePath) {
        $fileName = $this->getFileName($componentName);
        
        return $filePath . "/" . $fileName;
    }
    
    public function showFinalValues() {
        
        echo "<hr>";
        
        foreach ($this->components as $componentName => $values) {
            
            echo "<p>The component name is: " . $componentName;
            
            echo "<p>The final value is: <xmp>" . $values["finalValue"] . "</xmp>";
        }
    }
    
    public function show() {
        
        $output = "<html>\n";
        
        $output .= "<head>\n";
        
        $output .= $this->getElements("head");
        
        // echo "<p>The return value is: <xmp>" . $this->getElements("head");
        
        $output .= "</head>\n";
        
        $output .= "<body>\n";
        
        $output .= $this->getElements("body");
        
        $output .= "</body>\n";
        
        $output .= "</html>";
        
        echo $output;
    }
    
    private function getElements($type) {
        
        $returnVal = "";
         
        foreach ($this->components as $component) {
            
            if ($component["parent"] === $type) {
                
                $finalVal = $component["finalValue"];
                
                if ($finalVal != "<p>Optional</p>") {
                    $returnVal .= $component["finalValue"] . "\n\n";
                }
            }
        }
        return $returnVal;    
    }
}

function showDemo($params, $fsHome) {

    $demo = new demo;

    // these are the params passed from the user (the demo creator)
    // can be completely empty
    $demo->params = $params;
    
    // Sets typeOfDemo = enterprise if the user did not supply a value
    $demo->setDemoType();
    
    // Sets necessary paths for web references and filesystem references
    $demo->setPaths($fsHome);
    
    // sets the list of fields required for the demo
    // different demo types (Enterprise, Engagement, etc.) need different
    // values (capture instance, token URL, etc.)
    $demo->setComponents();
    
    // set a value for each component.
    // 1. Check for a user-passed parameter. else:
    // 2. Check for a file in the local directory. else:
    // 3. Set the default value.
    $demo->setValues();
        
    $demo->showFinalValues();
    
    $demo->show();

}
/*
$configItems = array(); // initializing just to avoid warnings

$paths["default"] = $paths["home"] . "/default";
$paths["templates"] = $paths["default"] . "/templates";
$paths["includes"] = $paths["default"] . "/includes";
$paths["webHome"] = "/JanrainDemoSites";
$paths["cwd"] = getcwd(); // current working directory

// variables that need to be referenced globally
$manifest;
$finalHTML;
$typeOfDemo;

include $paths["includes"] . "/navigation.php";

function showPage($configItems, $typeOfDemo) {
    
    global $manifest;
    global $paths; // paths related to the environment
    global $typeOfDemo;
    
    $paths["webDefault"] = $paths["webHome"] . "/default/templates/" . $typeOfDemo;
    
    include $paths["templates"] . "/manifest.php";
    
    $manifest = getManifest($typeOfDemo);
    
    $basicHTMLstrings = getHTMLstrings($configItems); // grab any simple HTML 
                                    //strings from the list of configurations

    $thesePaths = setPaths(); // paths to all of the needed files
    
    $finalPaths = evaluatePaths($thesePaths); // files opened, html tags added
    
    $basicHTMLstrings["navigation"] = getNav();

    $finalHTML = getFinalHTML($finalPaths, $basicHTMLstrings);
    
    $htmlTemplate = getHTMLtemplate($configItems);

    $htmlString = buildPage($htmlTemplate, $finalHTML);
    
    echo $htmlString;
}

function getHTMLstrings($configItems) {
    
    global $manifest;
    
    foreach ($manifest as $setting => $params) {
        if ($params["type"] === "htmlString") {
            if (empty($configItems[$setting])) {
                $finalHTML[$setting] = $manifest[$setting]["html"];
            }
            else { $finalHTML[$setting] = $configItems[$setting]; }            
        }
    }
    
    return $finalHTML;
}

function setPaths() {

    global $manifest;
    global $configItems;
    
    $initialSettings = array();
    
    foreach ($manifest as $setting => $params) {
        
        if ($params["type"] === "htmlString") {}
        else {
            if (empty($configItems[$setting])) {                
                $initialSettings[$setting]= checkForFile($setting); 
            }
            else {
                $initialSettings[$setting] = $configItems[$setting];
            }
        }
    }
    
    return $initialSettings;
}

// This function is reached when the user has not supplied a path
// First we check for a file in the local directory
// If there's not a file in the local directory, then we supply the default
// value.
function checkForFile($setting) {

    global $manifest;
    global $paths;
    
    $fileName = $setting . "." . $manifest[$setting]["extension"];
    
    if (file_exists($paths["cwd"] . "/" . $fileName)) {
        
        if ($manifest[$setting]["type"] === "inlineHTML") {
            return $paths["cwd"] . "/" . $fileName;
        }
        else {
            return $fileName;
        }
    }
    else { // the file is not in the local directory
        if (array_key_exists("optional", $manifest[$setting])) {
            if ($manifest[$setting]["optional"] === TRUE) {
                unset($manifest[$setting]);
                return "";
            }
        }
        else { return getDefaultValue($setting, $fileName); }        
    }
}

function getDefaultValue($setting, $fileName) {
    global $manifest;
    global $paths;
    global $typeOfDemo;

    if ($manifest[$setting]["type"] === "inlineHTML") {
        return $paths["templates"] . "/" . $typeOfDemo . "/" . $fileName;
    }
    else { return $paths["webDefault"] . "/" . $fileName; }
}

function evaluatePaths($thesePaths) {
    
    global $manifest;
    global $typeOfDemo;

    foreach ($manifest as $setting => $params) {
        
        if ($params["type"] === "htmlString") {}
        if ($params["type"] === "scriptRef") {
            $finalHTML[$setting] = "<script src = '" . $thesePaths[$setting] . "' type = 'text/javascript'></script>";
        }
        if ($params["type"] === "inlineHTML") {
            $finalHTML[$setting] = tryToOpen($thesePaths[$setting]);
        }
    }
    
    if ($typeOfDemo === "socialAjax") {
        $target = "__PATH_TO_CLIENT_VALIDATION_SCRIPT__";
        $arrow = $thesePaths["ajaxScript"];
        $finalHTML["jwol"] = str_replace($target, $arrow, $finalHTML["jwol"]); 
    }
    elseif ($typeOfDemo === "socialRedirect") {
        $target = "__REPLACE_WITH_YOUR_TOKEN_URL__";
        
        $arrow = "http://" . $_SERVER["SERVER_NAME"] . $thesePaths["tokenURL"];

        $finalHTML["socialLogin"] = str_replace($target, $arrow, $finalHTML["socialLogin"]); 
    }
    
    return $finalHTML; 
}

function getHeaderString() {
    
    global $typeOfDemo;
    global $displayNames; // from navigation.php
    
    return "<h1>Janrain " . $displayNames[$typeOfDemo] . " Demos</h1>";
}

function tryToOpen($fileName) {
    
    $thisString = "";
    
    if (file_exists($fileName)) {
                
        $thisString = file_get_contents($fileName);
        
        if ($thisString === FALSE) {
            $thisString = "\n" . "<p><font color = 'red'>Sorry, but the file $fileName could not be opened.</font></p>\n";
        }
    }
    else { $thisString = $fileName; }
    
    return $thisString;
}

function getHTMLtemplate($configItems) {
    
    global $paths;
    
    if (!array_key_exists("htmlTemplate", $configItems)) {
        $htmlTemplate = $paths["templates"] . "/htmlTemplate.html";
    }
    else { 
        $htmlTemplate = $configItems["htmlTemplate"];
        unset($configItems["htmlTemplate"]);
    }
    return $htmlTemplate;
}

function getFinalHTML($finalPaths, $basicHTMLstrings) {
    foreach ($finalPaths as $key => $value) {
        $finalHTML[$key] = $value;
    }
    foreach ($basicHTMLstrings as $key => $value) {
        $finalHTML[$key] = $value;
    }
    return $finalHTML;
}

function buildPage ($htmlTemplate, $settings) {
    
    global $typeOfDemo;

    $mainHTMLstring = tryToOpen($htmlTemplate);
    
    foreach ($settings as $key => $value) {
        $target = "<!--" . $key . " placeholder-->";
        $arrow = $value;
        $mainHTMLstring = str_replace($target, $arrow, $mainHTMLstring);
    }
    
    if ($typeOfDemo === "enterprise") {
        $target = '/<!-- Janrain Social Login scripts -->(?s).*<!-- end Janrain Social Login scripts -->/';

    }
    elseif ($typeOfDemo === "socialRedirect" || $typeOfDemo === "socialAjax") {
        $target = '/<!-- Janrain Enterprise scripts -->(?s).*<!-- end Janrain Enterprise scripts -->/';
    }
    $arrow = "";
    $mainHTMLstring = preg_replace($target, $arrow, $mainHTMLstring);   

    return $mainHTMLstring;
}

function getNav() {
    global $paths;
    
    $nav = tryToOpen($paths["templates"] . "/navigation/globalNav.html");
    $nav .= tryToOpen($paths["templates"] . "/navigation/home.html");
    $nav .= tryToOpen($paths["templates"] . "/navigation/sidebar.html");
    
    $links = getNavLinks();
    
    return str_replace("<!--links-->", $links, $nav);

}

function getNavLinks() {
    global $paths;
    global $links; // from navigation.php
    global $displayNames; // from navigation.php
    
    // This gets the current directory name for nav purposes
    // It parses $paths["cwd"], which is a path
    $navFolder = basename(dirname($paths["cwd"]));
    
    // This is a hack to accommodate the token URL
    if ($navFolder === "templates") {
        $navFolder = "socialLogin";
    }
    
    $returnString = "";
    
    foreach($links as $listName => $linkList) {
                
        if ($listName === $navFolder) { $liClass = "dropdown expanded"; }
        else { $liClass = "dropdown"; }

        $returnString .= "<li class='$liClass'>\n";
        $returnString .= "\t<a><span class='icon-navigation quilt-icon-folder'></span>\n";
        $returnString .= " $displayNames[$listName]</a>\n";
        $returnString .= "\t<ul class='children'>\n";

        foreach($linkList as $link => $linkName) {
            $thisLink = $paths["webHome"] . "/demos/" . $listName . "/" . $link;
            $returnString .= "<li><a href = '$thisLink'>$linkName</a></li>\n";
        }
        
        $returnString .= "</ul></li>";
    }

    return $returnString;

}