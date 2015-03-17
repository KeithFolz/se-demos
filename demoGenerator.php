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
        
        /* 
         * Each type of demo has its own set of required components.
         * The buildComponent method sets the values for each of the components.
         * The elements of a component are:
         * name = "title", "stylesheet", for example. Note: this value will 
         *      also be used in the file name.
         * type = file || css || fileRef
         * ext = file extension
         * parent = HTML container
         * required
         */
        
        // HEAD elements

        // Title
        // <title>Janrain Demo Sites</title>      
        $this->buildComponent("title", "file", "html", "title", TRUE);
        
        // Stylesheet
        $this->buildComponent("stylesheet", "css", "css", "head", TRUE);

        // Navigation (the js file that controls left nav)
        $this->buildComponent("navigation", "fileRef", "js", "head", TRUE);

        // Janrain js settings
        // <script>
        //      janrain.settings.width = '330';
        // </script>
        $this->buildComponent("janrainSettings", "file", "html", "head", FALSE);
        
        // <script>some random code</script>
        // <script>another script</script>
        $this->buildComponent("otherScripts", "file", "html", "head", FALSE);

        // BODY elements
        
        // Heading
        // <h1>Janrain Social Login Demos</h1>
        $this->buildComponent("heading", "file", "html", "header", TRUE);

        // Content
        // main content of page
        $this->buildComponent("content", "file", "html", "body", TRUE);
        
        // Navigation
        $this->buildComponent("global_nav", "file", "html", "navigation", TRUE);
        $this->buildComponent("home", "file", "html", "navigation", TRUE);
        $this->buildComponent("sidebar_col", "file", "html", "navigation", TRUE);
        
        // htmlTemplate
        $this->buildComponent("htmlTemplate", "file", "html", "htmlTemplate", TRUE);
        
        // DemoType-specific settings
        if ($this->params["typeOfDemo"] === "enterprise") {
        
            // <script src = 'janrain-init.js'></script>
            $this->buildComponent("janrain-init", "fileRef", "js", "head", TRUE);
            
            // <script src = 'janrain-utils.js'></script>
            $this->buildComponent("janrain-utils", "fileRef", "js", "head", TRUE);
            
            // <a href="#" id="captureSignInLink" 
            // onclick="janrain.capture.ui.renderScreen('signIn')">
            // Sign In / Sign Up</a>
            $this->buildComponent("signinLinks", "file", "html", "body", TRUE);
            
            // Janrain JTL + HTML
            $this->buildComponent("widgetScreens", "file", "html", "body", TRUE);

        }        
    }
    
    private function buildComponent($name, $type, $ext, $parent, $required) {
        $this->components[$name]["type"] = $type;
        $this->components[$name]["ext"] = $ext;
        $this->components[$name]["parent"] = $parent;
        $this->components[$name]["required"] = $required;
    }
    
    // sets values for various needed paths in the filesystem and web
    public function setPaths($home) {
        
        $this->paths["home"] = "/" . basename($home);
        $this->paths["default"] = $this->paths["home"] . "/default";
        $this->paths["templates"] = $this->paths["default"] . "/templates";
        $this->paths["typeOfDemo"] = $this->paths["templates"] . "/" . $this->params["typeOfDemo"];
        $this->paths["navigation"] = $this->paths["templates"] . "/navigation";
        
        $this->paths["fsHome"] = strstr($home, $this->paths["home"], TRUE);
        $this->paths["includes"] = $this->paths["fsHome"] . $this->paths["default"] . "/includes";

        $this->paths["cwd"] = getcwd(); // current working directory
    }
    
    public function setFinalValues() {
        
        $componentNames = array_keys($this->components);
        
        foreach ($componentNames as $componentName) {
            
            $this->components[$componentName]["finalValue"] = 
                    $this->findValue($componentName);
        }
    }
    
    private function findValue($componentName) {
                
        if (!empty($this->params[$componentName])) {
            return $this->params[$componentName];
        }
        else {
            
            if ($this->components[$componentName]["required"] === TRUE) {
                
                if ($this->fileExistsInLocalDir($componentName)) {
            
                    return file_get_contents($this->getFullFilePath($componentName, $this->paths["cwd"]));
                }
                else { 
                    
                    return $this->getDefaultValue($componentName);
                }
            }
        }            
    }
    
    private function fileExistsInLocalDir($componentName) {
        
        return (file_exists($this->getFullFilePath($componentName, $this->paths["cwd"])));
    
    }
    
    private function setAllPossibleValues() {
        $componentNames = array_keys($this->components);
        
        foreach ($componentNames as $componentName) {
            
            if (array_key_exists($componentName, $this->params)) {
                
                if (!empty($this->params[$componentName])) {
                    $this->components[$componentName]["usv"] = $this->params[$componentName];
                }
                else { $this->components[$componentName]["usv"] = ""; }
            }
            else { $this->components[$componentName]["usv"] = ""; }

            if ($this->fileExistsInLocalDir($componentName)) {
                $this->components[$componentName]["localDir"] = 
                        $this->getFullFilePath($componentName, $this->paths["cwd"]);    
            }
            else {
                $this->components[$componentName]["localDir"] = "no";
            }
            
            if ($this->components[$componentName]["required"] === TRUE) {
                $this->components[$componentName]["defaultValue"] = 
                        $this->getDefaultValue($componentName);
            }
            else {
                $this->components[$componentName]["defaultValue"] = "optional";
            }
        }
    }
    
    private function getDefaultValue($componentName) {
        
        $path = $this->getDefaultPath($componentName);
        
        $filePath = $this->getFullFilePath($componentName, $path);

        if ($componentName === "stylesheet") {
            $defaultVal = "<link rel='stylesheet' href='" . $filePath . "'/>";
        }
        else {

            if ($this->components[$componentName]["type"] === "file") {
                
                $filePath = $this->paths["fsHome"] . $filePath;

                $defaultVal = file_get_contents($filePath);
            }
            elseif ($this->components[$componentName]["type"] === "fileRef") {
                $defaultVal = "<script src='$filePath' type='text/javascript'></script>";
            }
        }
        
        return $defaultVal;
    }
    
    private function getDefaultPath($componentName) {
        
        if ($componentName === "stylesheet") {
            $path = $this->paths["default"] . "/styles";
        }
        elseif ($componentName === "navigation") {
            $path = $this->paths["default"] . "/scripts";
        }
        elseif ($this->components[$componentName]["parent"] === "navigation") {
            $path = $this->paths["templates"] . "/navigation";
        }
        elseif ($componentName === "htmlTemplate") {
            $path = $this->paths["templates"];
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

    // This is a debug function. It shows all of the paths and component
    // settings in a plain table. Fire this fucntion by adding
    // ?mode=debug to the url of your demo
    public function showAllValues() {
        
        echo "<p>The type of demo is " . $this->params["typeOfDemo"] . "</p>";
        
        $this->setAllPossibleValues();
        
        $pathsString = "<p><b>Paths</b></p>";
        
        $pathsString .= "<table border = '1'>";
        
        foreach ($this->paths as $key => $path) {
            $pathsString .= "<tr>";
            $pathsString .= "<td>" . $key . "</td><td>" . $path . "</td>";
            $pathsString .= "</tr>";
        }
        
        $pathsString .= "</table>";
        
        echo $pathsString;
        
        echo "<p><b>Values</b></p>";
        
        $componentNames = array_keys($this->components);
        
        $thisString = "<table border = '1'>";
        $thisString .= "<tr><td>Component</td><td>user-supplied value</td><td>File in local dir?</td><td>Default</td><td>Final</td></tr>";
        
        $valueTypes = array("usv", "localDir", "defaultValue", "finalValue");
        
        foreach ($componentNames as $componentName) {
            
            $thisString .= "<tr>";
            $thisString .= "<td>" . $componentName . "</td>";
            
            foreach ($valueTypes as $valueType) {
                $thisString .= $this->getTableCell($componentName, $valueType);
            }

            $thisString .= "</tr>"; 
            
        }
        $thisString .= "</table>";
        
        echo $thisString;
    }
    
    private function getTableCell($componentName, $value) {
        
        // max number of chars to display from each component finalVal
        $outputMax = 250;
        
        $returnString = "<td><xmp>";
        
        $returnString .= substr($this->components[$componentName][$value], 0, $outputMax);
        
        $returnString .= "</xmp></td>";
        
        return $returnString;
    }

    private function replaceHolder($target, $arrow, $barnside) {
        $target = "<!--" . $target . " placeholder-->";
        return str_replace($target, $arrow, $barnside);
    }
    
    private function getNavLinks() {
        include $this->paths["includes"] . "/navigation.php";

        // This gets the current directory name for nav purposes
        // It parses $paths["cwd"], which is a path
        $navFolder = basename(dirname($this->paths["cwd"]));

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
                $thisLink = $this->paths["home"] . "/demos/" . $listName . "/" . $link;
                $returnString .= "<li><a href = '$thisLink'>$linkName</a></li>\n";
            }

            $returnString .= "</ul></li>";
        }

        return $returnString;

    }   
    
    public function show() {
        
        $output = $this->replaceHolder("title", $this->components["title"]["finalValue"], $this->components["htmlTemplate"]["finalValue"]);
        
        $output = $this->replaceHolder("head", $this->getElements("head"), $output);
        
        $this->components["sidebar_col"]["finalValue"] = $this->replaceHolder("links", $this->getNavLinks(), $this->components["sidebar_col"]["finalValue"]);

        $output = $this->replaceHolder("navigation", $this->getElements("navigation"), $output);

        $output = $this->replaceHolder("header", $this->components["heading"]["finalValue"], $output);
        
        $output = $this->replaceHolder("body", $this->getElements("body"), $output);
        
        echo $output;
    }
   
    private function getElements($type) {
        
        $returnVal = "";
         
        foreach ($this->components as $component) {
            
            if ($component["parent"] === $type) {
                
                if (!empty($component["finalValue"])) {
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
    $demo->setFinalValues();
    
    if (empty($_GET["mode"])) {
        $demo->show();
    }
    else { 
        if ($_GET["mode"] === "debug") {
            $demo->showAllValues();   
        }
    }
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

 */