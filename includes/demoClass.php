<?php

// to-do
// is cwd needed?
// clean up setPaths

include_once "htmlPageClass.php";

class demo extends htmlPage {
    public $params; // user-supplied parameters
    public $typeOfDemo; // enterprise || engagement || socialRedirect || socialAjax

    private $paths;

    public function demo($params) {
	// $this->page = new htmlPage();
	
	// these are the params passed from the user (the demo creator)
	// can be completely empty
	$this->params = $params;
	
	// Sets typeOfDemo = enterprise if the user did not supply a value
	$this->setDemoType();
	
	// Sets necessary paths for web references and filesystem references
	$this->setPaths($this->params["fsHome"]);

	// Set static values for doctype etc. <html><body>
	$this->setHTMLbasics();

	// Set up the main parts of the demo
	$this->setComponents();

    }

    private function setHTMLbasics() {
	
	$this->initializeHTML();
	
	$this->addMeta("<meta charset='utf-8'/>");
	$this->addMeta("<meta name='viewport' content='width=device-width, initial-scale=1'>");
	
	$stylesheet = $this->paths["default"] . "/styles/screen.css";
	$this->addCSS($stylesheet, "fileRef");
	
	$jquery = "http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js";
	$this->addScript($jquery, "fileRef");
	
	$navigation = $this->paths["default"] . "/scripts/navigation.js";
	$this->addScript($navigation, "fileRef");
	
	// Add class to <body> tag
	$this->addClassToTag("body", "janrain-font");
	
	// Add navigation <div>s to body
	$this->addToBody(file_get_contents($this->paths["navFull"] . "/global_nav.html"));
	$this->addToBody(file_get_contents($this->paths["navFull"] . "/home.html"));
	$this->addToBody(file_get_contents($this->paths["navFull"] . "/sidebar_col.html"));
	
	// Add nav links
	$navLinks = $this->getNavLinks();
	$body = $this->replaceHolder("links", $navLinks, $this->getBody());
	$this->setBody($body);

	// Add body_col element to body
	$this->addToBody(file_get_contents($this->paths["templatesFull"] . "/bodyCol.html"));
    }
    
    public function setDemoType() {
        if (empty($this->params["typeOfDemo"])) {
            $this->typeOfDemo = "enterprise";
        }
        else { $this->typeOfDemo = $this->params["typeOfDemo"]; }
    }

    // Main logic of script
    public function setComponents() {
	
	// Title
	if (!empty($this->params["title"])) { $title = $this->params["title"]; }
	else { $title = $this->getDefaultValue("title"); }
	$this->setTitle($title);

	// Header
	if (!empty($this->params["header"])) { $header = $this->params["header"]; }
	else { $header = $this->getDefaultValue("header"); }
	
	// Inserts the Header <h1>$header</h1> into the body element
	$bodyWithHeader = $this->replaceHolder("header", $header, $this->getBody());
	$this->setBody($bodyWithHeader);
	
	$this->content = "";

        // DemoType-specific settings
        if ($this->typeOfDemo === "enterprise") {

	    // janrain-init.js
	    $this->addScript($this->getPath("janrain-init.js", "webPath"), "fileRef");

	    // janrain-utils.js
	    $this->addScript($this->getPath("janrain-utils.js", "webPath"), "fileRef");

            // Janrain js settings, for example:
            // <script>janrain.settings.width = '330';</script>
	    $fileName = "janrainSettings.js";
	    if (file_exists($this->paths["cwd"] . "/" . $fileName)) {
		$this->addScript($fileName, "fileRef");
	    }

            // <a href="#" id="captureSignInLink" 
            // onclick="janrain.capture.ui.renderScreen('signIn')">
            // Sign In / Sign Up</a>
	    $contentFiles = array("signinLinks.html", "content.html", "widgetScreens.html");
	    
	    foreach($contentFiles as $file) {
		$this->content .= file_get_contents($this->getPath($file, "fileSystem"));
	    }
	    
	    /*
	    $path = $this->getPath("signinLinks.html", "fileSystem");
	    $this->content = file_get_contents($path);

	    $path = $this->getPath("content.html", "fileSystem");
	    $this->content .= file_get_contents($path);

            // Janrain JTL + HTML
	    $path = $this->getPath("widgetScreens.html", "fileSystem");
	    $this->content .= file_get_contents($path);
	     * 
	     */

        }
        elseif ($this->typeOfDemo === "socialAjax" || $this->typeOfDemo === "socialRedirect") {

	    if (empty($this->params["content"])) {
		$path = $this->getPath("content.html", "fileSystem");
		$this->content .= file_get_contents($path);
	    }
	    else { $this->content .= $this->params["content"]; }

	    // The basic Social login script
	    // just getting the path for now, bc the content will need to
	    // change based on redirect vs. ajax (below)
	    $socialLoginPath = $this->getPath("socialLogin.html", "fileSystem");
	    
	    // optional error-checking
	    $fileName = "errorChecking.html";
	    if (file_exists($this->paths["cwd"] . "/" . $fileName)) {
		$this->addScript($fileName, "fileRef");
	    }

            if ($this->typeOfDemo === "socialAjax") {

		$this->addScript($socialLoginPath, "inline");

		// Path to the Ajax script
		$ajaxPath = $this->getPath("ajaxScript.php", "webRoot");
		
		// Janrain widget onload() is required for Social Ajax
		$jwolPath = $this->getPath("jwol.html", "fileSystem");
		
		$jwol = file_get_contents($jwolPath);
		
		// insert the path to the ajax script into the Janrain Widget
		// onLoad function
		// $jwol = $this->replaceHolder("ajaxScript", $ajaxPath, $jwol);
		
		$this->scriptBlock .= $this->replaceHolder("ajaxScript", $ajaxPath, $jwol) . "\n";

		// must set janrain.settings.tokenAction='event';
		$settings = $this->getPath("janrainSettings.html", "fileSystem");
		$this->addScript($settings, "inline");

            }
            
            else { // Social Login - Redirect (token URL)

		// optional Janrain widget onload()
		$fileName = "jwol.html";
		if (file_exists($this->paths["cwd"] . "/" . $fileName)) {
		    $this->addScript($fileName, "fileRef");
		}
		
		$tokenURL = "http://" . $_SERVER["SERVER_NAME"] . $this->getPath("tokenURL.php", "webRoot");
		
		$socialLogin = file_get_contents($socialLoginPath);
		
		$socialLogin = $this->replaceHolder("tokenURL", $tokenURL, $socialLogin);
		
		$this->scriptBlock .= $socialLogin . "\n";
	    }

        }

	// Check for other scripts
	$fileName = "otherScripts.html";
	if (file_exists($this->paths["cwd"] . "/" . $fileName)) {
	    $this->addScript($fileName, "inline");
	}
	
	$body = $this->replaceHolder("content", $this->content, $this->getBody());
	$this->setBody($body);

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
        if ($this->paths["thisFolder"] === "socialRedirect") {
            return "standard-redirect";
        }
        else { return $this->paths["thisFolder"]; }
    }
    
    private function getDefaultValue($componentName) {
        
        global $displayNames;

	$baseString = "Janrain " . $displayNames[$this->typeOfDemo];

	$displayName = $this->getDisplayValue($this->getDemoFolderName());

	if ($displayName != "") { $baseString .= ": " . $displayName; }

	if ($componentName === "title") { $defaultVal = $baseString; }
	else { $defaultVal = "<h1>" . $baseString . "</h1>\n"; }

        return $defaultVal;
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

    /*
    public function show() {

	if (empty($_GET["mode"])){}
	elseif ($_GET["mode"] === "debug") {
	    $this->showAllValues();
	    exit();
	}

	$this->show();
    }
*/
}