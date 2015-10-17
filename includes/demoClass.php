<?php

class demo {
    public $params; // user-supplied parameters
    public $fsHome; // filesystem home (full path)
    public $homeDir; // homedir for demos (default = JanrainDemoSites)
    public $typeOfDemo; // enterprise || engagement || socialRedirect || socialAjax
        
    private $paths;
    private $components;

    // $params, $fsHome
    public function demo($params) {
	$this->page = new htmlPage();
	
	// these are the params passed from the user (the demo creator)
	// can be completely empty
	$this->params = $params;
	
	// Sets typeOfDemo = enterprise if the user did not supply a value
	$this->setDemoType();
	
	// Sets necessary paths for web references and filesystem references
	$this->setPaths($this->params["fsHome"]);
	
	// sets the list of fields required for the demo
	// different demo types (Enterprise, Engagement, etc.) need different
	// values (capture instance, token URL, etc.)
	
	$this->page->addMeta("<meta charset='utf-8'/>");
	$this->page->addMeta("<meta name='viewport' content='width=device-width, initial-scale=1'>");
	
	$stylesheet = $this->paths["default"] . "/styles/screen.css";
	$this->page->addCSS($stylesheet, "fileRef");
	
	$jquery = "http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js";
	$this->page->addScript($jquery, "fileRef");
	
	$navigation = $this->paths["default"] . "/scripts/navigation.js";
	$this->page->addScript($navigation, "fileRef");
	
	// Add class to <body> tag
	$this->page->addClassToTag("body", "janrain-font");
	
	// Add navigation <div>s to body
	$this->page->addToBody(file_get_contents($this->paths["navFull"] . "/global_nav.html"));
	$this->page->addToBody(file_get_contents($this->paths["navFull"] . "/home.html"));
	$this->page->addToBody(file_get_contents($this->paths["navFull"] . "/sidebar_col.html"));
	
	// Add nav links
	$navLinks = $this->getNavLinks();
	$body = $this->replaceHolder("links", $navLinks, $this->page->getBody());
	$this->page->setBody($body);

	// Add body_col element to body
	$this->page->addToBody(file_get_contents($this->paths["templatesFull"] . "/bodyCol.html"));

	$this->setComponents();

	// set a value for each component.
        // 1. Check for a user-passed parameter. else:
        // 2. Check for a file in the local directory. else:
        // 3. Set the default value.
        // $this->setFinalValues();
	
    }

    public function setDemoType() {
        if (empty($this->params["typeOfDemo"])) {
            $this->typeOfDemo = "enterprise";
        }
        else { $this->typeOfDemo = $this->params["typeOfDemo"]; }
    }

    // sets the list of fields that are needed for this demo type
    // some may be optional
    public function setComponents() {
	
	// Title
	if (!empty($this->params["title"])) { $title = $this->params["title"]; }
	else { $title = $this->getDefaultValue("title"); }
	$this->page->setTitle($title);

	// Header
	if (!empty($this->params["header"])) { $header = $this->params["header"]; }
	else { $header = $this->getDefaultValue("header"); }
	
	$body = $this->replaceHolder("header", $header, $this->page->getBody());
	$this->page->setBody($body);
	
	$this->content = "";

        // DemoType-specific settings
        if ($this->typeOfDemo === "enterprise") {

	    // janrain-init.js
	    $path = $this->getPath("janrain-init.js", "webPath");
	    $this->page->addScript($path, "fileRef");
	    
	    // janrain-utils.js
	    $path = $this->getPath("janrain-utils.js", "webPath");
	    $this->page->addScript($path, "fileRef");

            // Janrain js settings
            // <script>
            //      janrain.settings.width = '330';
            // </script>
	    $fileName = "janrainSettings.js";
	    if (file_exists($this->paths["cwd"] . "/" . $fileName)) {
		$this->page->addScript($fileName, "fileRef");
	    }

            // <a href="#" id="captureSignInLink" 
            // onclick="janrain.capture.ui.renderScreen('signIn')">
            // Sign In / Sign Up</a>
	    $path = $this->getPath("signinLinks.html", "fileSystem");
	    $this->content = file_get_contents($path);

	    $path = $this->getPath("content.html", "fileSystem");
	    $this->content .= file_get_contents($path);

            // Janrain JTL + HTML
	    $path = $this->getPath("widgetScreens.html", "fileSystem");
	    $this->content .= file_get_contents($path);

        }
        elseif ($this->typeOfDemo === "socialAjax" || $this->typeOfDemo === "socialRedirect") {

	    if (empty($this->params["content"])) {
		$path = $this->getPath("content.html", "fileSystem");
		$this->content .= file_get_contents($path);
	    }
	    else { $this->content .= $this->params["content"]; }

	    // The basic Social login script
	    $socialLoginPath = $this->getPath("socialLogin.html", "fileSystem");
	    
	    // optional error-checking
	    $fileName = "errorChecking.html";
	    if (file_exists($this->paths["cwd"] . "/" . $fileName)) {
		$this->page->addScript($fileName, "fileRef");
	    }

            if ($this->typeOfDemo === "socialAjax") {

		$this->page->addScript($socialLoginPath, "inline");

		// Path to the Ajax script
		$ajaxPath = $this->getPath("ajaxScript.php", "webRoot");
		
		// Janrain widget onload() is required for Social Ajax
		$jwolPath = $this->getPath("jwol.html", "fileSystem");
		
		$jwol = file_get_contents($jwolPath);
		
		// insert the path to the ajax script into the Janrain Widget
		// onLoad function
		$jwol = $this->replaceHolder("ajaxScript", $ajaxPath, $jwol);
		
		$this->page->scriptBlock .= $jwol . "\n";

		// must set janrain.settings.tokenAction='event';
		$settings = $this->getPath("janrainSettings.html", "fileSystem");
		$this->page->addScript($settings, "inline");

            }
            
            else { // Social Login - Redirect (token URL)

		// optional Janrain widget onload()
		$fileName = "jwol.html";
		if (file_exists($this->paths["cwd"] . "/" . $fileName)) {
		    $this->page->addScript($fileName, "fileRef");
		}
		
		$tokenURL = "http://" . $_SERVER["SERVER_NAME"] . $this->getPath("tokenURL.php", "webRoot");
		
		$socialLogin = file_get_contents($socialLoginPath);
		
		$socialLogin = $this->replaceHolder("tokenURL", $tokenURL, $socialLogin);
		
		$this->page->scriptBlock .= $socialLogin . "\n";
	    }

        }

	// Check for other scripts
	$fileName = "otherScripts.html";
	if (file_exists($this->paths["cwd"] . "/" . $fileName)) {
	    $this->page->addScript($fileName, "inline");
	}
	
	$body = $this->replaceHolder("content", $this->content, $this->page->getBody());
	$this->page->setBody($body);

    }
    
    // First looks in the local dir for the filename
    // If it's not there, this returns the default path
    // typeOfPath indicates whether the returned file path should be relative
    // to the fileSystem root or web Root
    private function getPath($fileName, $typeOfPath) {
	$path = $this->paths["cwd"] . "/" . $fileName;
	if (file_exists($path)) {
	    if ($typeOfPath != "fileSystem") { $path = $fileName; }
	}
	else { 
	    $path = $this->paths["templates"] . "/" . $this->typeOfDemo . "/" . $fileName;
	    if ($typeOfPath === "fileSystem") {
		$path = $this->paths["fsHome"] . $path;
	    }
	}
	
	return $path;
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
	$this->paths["navFull"] = $this->paths["fsHome"] . "/" . $this->paths["navigation"];
	$this->paths["templatesFull"] = $this->paths["fsHome"] . "/" . $this->paths["templates"];
        $this->paths["cwd"] = getcwd(); // current working directory
        $this->paths["thisFolder"] = basename($this->paths["cwd"]);
    }

    public function addScript($path, $fileRef) {
	$this->page->addScript($path, $fileRef);
    }

    public function setFinalValues() {

        // $componentNames = array_keys($this->components);
	
	foreach($this->components["required"] as $component) {
/*	    echo "<p>the component is: " . $component;
	    echo "<p>the param value is: " . $this->params[$component];
 * 
 */
	    // $this->components[$component]["finalValue"] = $this->findValue($component);
	    // $finalValue = $this->findValue($component);
	    // addToPage($this->components[$component]["finalValue"]);
	}
	
	
	//function addToPage()
	
	// echo "<p>the final value of title is: " . $this->components["title"]["finalValue"];
	// $title = $this->findValue("title");
	// $this->page->setTitle($title);
	// $this->page->setTitle($this->components["title"]["finalValue"]);
	// $this->page->setMeta($this->components["meta"]["finalValue"]);

	// $this->page->
/*
        foreach ($componentNames as $componentName) {
            
            $this->components[$componentName]["finalValue"] = 
                    $this->findValue($componentName);
        }
*/       
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
        
        if ($componentName === "title" || $componentName === "header") { 
            
	    $baseString = "Janrain " . $displayNames[$this->typeOfDemo];
	    
	    $displayName = $this->getDisplayValue($this->getDemoFolderName());
	    
	    if ($displayName != "") { $baseString .= ": " . $displayName; }

            if ($componentName === "title") { $defaultVal = $baseString; }
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

	if (empty($_GET["mode"])){}
	elseif ($_GET["mode"] === "debug") {
	    $this->showAllValues();
	    exit();
	}
    
        // $output = $this->replaceHolder("title", $this->components["title"]["finalValue"], $this->components["htmlTemplate"]["finalValue"]);
        
        // $output = $this->replaceHolder("head", $this->getElements("head"), $output);
        

        // $output = $this->replaceHolder("navigation", $this->getElements("navigation"), $output);

        // $output = $this->replaceHolder("header", $this->components["header"]["finalValue"], $output);
        
        // $output = $this->replaceHolder("body", $this->getElements("body"), $output);

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