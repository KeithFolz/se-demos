<?php

class demo {
    public $params; // user-supplied parameters
    public $fsHome; // filesystem home (full path)
    public $homeDir; // homedir for demos (default = JanrainDemoSites)
    public $typeOfDemo; // enterprise || engagement || socialRedirect || socialAjax
        
    private $paths;
    private $components;
    
    public function setDemoType() {
        if (empty($this->params["typeOfDemo"])) {
            $this->typeOfDemo = "enterprise";
        }
        else { $this->typeOfDemo = $this->params["typeOfDemo"]; }
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
        $this->buildComponent("title", "string", "html", "title", TRUE);
        
        // Stylesheet
        $this->buildComponent("screen", "fileRef", "css", "head", TRUE);

        // Navigation (the js file that controls left nav)
        $this->buildComponent("navigation", "fileRef", "js", "head", TRUE);

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
        if ($this->typeOfDemo === "enterprise") {
        
            // <script src = 'janrain-init.js'></script>
            $this->buildComponent("janrain-init", "fileRef", "js", "head", TRUE);
            
            // <script src = 'janrain-utils.js'></script>
            $this->buildComponent("janrain-utils", "fileRef", "js", "head", TRUE);
            
            // Janrain js settings
            // <script>
            //      janrain.settings.width = '330';
            // </script>
            $this->buildComponent("janrainSettings", "file", "html", "head", FALSE);
            
            // <a href="#" id="captureSignInLink" 
            // onclick="janrain.capture.ui.renderScreen('signIn')">
            // Sign In / Sign Up</a>
            $this->buildComponent("signinLinks", "file", "html", "body", TRUE);
            
            // Janrain JTL + HTML
            $this->buildComponent("widgetScreens", "file", "html", "body", TRUE);

        }
        elseif ($this->typeOfDemo === "socialAjax" || $this->typeOfDemo === "socialRedirect") {
       
            // Main social login script
            // For Social Redirect, this file include reference to Token URL
            $this->buildComponent("socialLogin", "file", "html", "head", TRUE);

            // Error-checking
            $this->buildComponent("errorChecking", "fileRef", "html", "head", FALSE);

            // Janrain widget onload()
            $this->buildComponent("jwol", "file", "html", "head", FALSE);

            if ($this->typeOfDemo === "socialAjax") {

                // Path to the Ajax script
                $this->buildComponent("ajaxScript", "fileRef", "php", "none", TRUE);
                
                // For Ajax, JWOL is required.
                $this->buildComponent("jwol", "file", "html", "head", TRUE);
                
                $this->buildComponent("janrainSettings", "file", "html", "head", TRUE);

            }
            
            else {

                // Token URL
                $this->buildComponent("tokenURL", "fileRef", "php", "none", TRUE);
                
                $this->buildComponent("janrainSettings", "file", "html", "head", FALSE);

                
            }
        }

        
        // <script>some random code</script>
        // <script>another script</script>
        $this->buildComponent("otherScripts", "file", "html", "head", FALSE);
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
        $this->paths["typeOfDemo"] = $this->paths["templates"] . "/" . $this->typeOfDemo;
        $this->paths["navigation"] = $this->paths["templates"] . "/navigation";
        
        $this->paths["fsHome"] = strstr($home, $this->paths["home"], TRUE);
        // $this->paths["includes"] = $this->paths["fsHome"] . $this->paths["default"] . "/includes";

        $this->paths["cwd"] = getcwd(); // current working directory
        $this->paths["thisFolder"] = basename($this->paths["cwd"]);
    }

    public function setFinalValues() {
        
        $componentNames = array_keys($this->components);
        
        foreach ($componentNames as $componentName) {
            
            $this->components[$componentName]["finalValue"] = 
                    $this->findValue($componentName);
        }
       
        $this->components["sidebar_col"]["finalValue"] = $this->replaceHolder("links", $this->getNavLinks(), $this->components["sidebar_col"]["finalValue"]);

        if ($this->typeOfDemo === "socialRedirect") {
            $this->components["socialLogin"]["finalValue"] = $this->replaceHolder("tokenURL", $this->components["tokenURL"]["finalValue"], $this->components["socialLogin"]["finalValue"]);
        }
        elseif ($this->typeOfDemo === "socialAjax") {
            $this->components["jwol"]["finalValue"] = $this->replaceHolder("ajaxScript", $this->components["ajaxScript"]["finalValue"], $this->components["jwol"]["finalValue"]);
        
        }
    }
    
    private function findValue($componentName) {
        // First, check for a user-supplied value
        if (!empty($this->params[$componentName])) {
            
            if ($this->components[$componentName]["type"] === "file") {
                
                // $returnVal = file_get_contents($this->params[$componentName]);
                $returnVal = $this->params[$componentName];
            }
            else { $returnVal = $this->params[$componentName]; }
        }
        // If no user-supplied value, check for a file in the local directory
        elseif ($this->fileExistsInLocalDir($componentName)) {
                            
            if ($this->components[$componentName]["type"] === "fileRef") {
                $returnVal = $this->getFileRef($componentName, $this->getFileName($componentName));
            }
            elseif ($this->components[$componentName]["type"] === "file") {
                $fullFilePath = $this->getFullFilePath($componentName, $this->paths["cwd"]);

                $returnVal = file_get_contents($fullFilePath);
            }
        }
        // If no file in the local directory, assign the default value
        elseif ($this->components[$componentName]["required"] === TRUE)  { 

            $returnVal = $this->getDefaultValue($componentName);
        }
        else { $returnVal = ""; }
        
        return $returnVal;
    }
    
    private function getFileRef($componentName, $filePath) {

        if ($this->components[$componentName]["ext"] === "js") {

            $returnVal = "<script src='$filePath' type='text/javascript'></script>";
        }
        elseif ($this->components[$componentName]["ext"] === "css") {

            $returnVal = "<link rel='stylesheet' href='$filePath'></script>";

        }
        elseif ($componentName === "tokenURL") {

            $returnVal = "http://" . $_SERVER["SERVER_NAME"] . $filePath;
        }
        elseif ($componentName === "ajaxScript") {
            
            $returnVal = $filePath;
        }
/*
        if ($typeOfDemo === "socialAjax") {
            $target = "__PATH_TO_CLIENT_VALIDATION_SCRIPT__";
            $arrow = $thesePaths["ajaxScript"];
            $finalHTML["jwol"] = str_replace($target, $arrow, $finalHTML["jwol"]); 
        }
 * 
 */

        return $returnVal;

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
    
    private function getDisplayValue($folderName) {
        
	if ("/" . $folderName === $this->paths["home"]) {
	    $returnval = "";
	}
	else {
	    global $links, $navFolder; // from includes/navigation.php
	    $listName = $navFolder[$this->typeOfDemo];
	    $returnval = $links[$listName][$folderName];
	}

        return $returnval;
        
    }

    private function getDemoFolderName() {
        if ($this->paths["thisFolder"] == "socialRedirect") {
            return "standard-redirect";
        }
        else { return $this->paths["thisFolder"]; }
    }
    
    private function getDefaultValue($componentName) {
        
        global $displayNames;
        
        if ($componentName === "title" || $componentName === "heading") { 
            
	    $baseString = "Janrain " . $displayNames[$this->typeOfDemo];
	    
	    $displayName = $this->getDisplayValue($this->getDemoFolderName());
	    
	    if ($displayName != "") { $baseString .= ": " . $displayName; }

	    /*
            $baseString = "Janrain " . $displayNames[$this->typeOfDemo] . ": " . $this->getDisplayValue($this->getDemoFolderName());
            */
	    
            if ($componentName === "title") { $defaultVal = "<title>" . $baseString . "</title>\n"; }
            else { $defaultVal = "<h1>" . $baseString . "</h1>\n"; }
        }
        else {
            $path = $this->getDefaultPath($componentName);
        
            $filePath = $this->getFullFilePath($componentName, $path);
                    
            if ($this->components[$componentName]["type"] === "file") {

                $filePath = $this->paths["fsHome"] . $filePath;

                $defaultVal = file_get_contents($filePath);
            }
            elseif ($this->components[$componentName]["type"] === "fileRef") {

                $defaultVal = $this->getFileRef($componentName, $filePath);

            }
        }
        
        return $defaultVal;
    }
    
    private function getDefaultPath($componentName) {
        
        if ($this->components[$componentName]["ext"] === "css") {
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
        
        echo "<p>The type of demo is " . $this->typeOfDemo . "</p>";
        
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
        
        $componentNames = array_keys($this->components);
        
        echo "<p><b>Components</b></p>";
        
        $thisString = "<table border = '1'>";
        $thisString .= "<tr><td>Component</td><td>Type</td><td>ext</td><td>parent</td><td>required?</td></tr>";
/*
 *     
    private function buildComponent($name, $type, $ext, $parent, $required) {
        $this->components[$name]["type"] = $type;
        $this->components[$name]["ext"] = $ext;
        $this->components[$name]["parent"] = $parent;
        $this->components[$name]["required"] = $required;
    }
 */
        $dataTypes = array("type", "ext", "parent", "required");
        
        foreach ($componentNames as $componentName) {
            
            $thisString .= "<tr>";
            
            $thisString .= "<td>" . $componentName . "</td>";
            
            foreach ($dataTypes as $dataType) {
                $thisString .= "<td>" . $this->components[$componentName][$dataType] . "</td>";
            }
            
            $thisString .= "</tr>";
        }
        
        $thisString .= "</table>";
        
        echo $thisString;
        
        echo "<p><b>Values</b></p>";
        
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
        
        global $links, $displayNames, $navFolder; // from includes/navigation.php
        
        $returnString = "";
        
        foreach($links as $listName => $linkList) {
                        
            if ($listName === $navFolder[$this->typeOfDemo]) { $liClass = "dropdown expanded"; }
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
	$this->page = new htmlPage();
	
	// $thisPage = new htmlPage();
	
	// $thisPage->setTitle($this->components["title"]["finalValue"]);

	$this->page->setTitle("testTitle");
	
        $output = $this->replaceHolder("title", $this->components["title"]["finalValue"], $this->components["htmlTemplate"]["finalValue"]);
        
        $output = $this->replaceHolder("head", $this->getElements("head"), $output);
        

        $output = $this->replaceHolder("navigation", $this->getElements("navigation"), $output);

        $output = $this->replaceHolder("header", $this->components["heading"]["finalValue"], $output);
        
        $output = $this->replaceHolder("body", $this->getElements("body"), $output);

	$this->page->show();

	// $thisPage->show();
        // echo $output;
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