<?php

class htmlPage {

    public $css;
    public $meta;
    public $scriptBlock;
    

    public function htmlPage() {
	$this->page = $this->getPage();
    }

    public function addClassToTag($tag, $class) {
	if ($tag === "body") {
	    $arrow = "<body class = '$class'>";
	    $this->body = str_replace("<body>", $arrow, $this->body);
	}
	/*
        if ($typeOfDemo === "socialAjax") {
            $target = "__PATH_TO_CLIENT_VALIDATION_SCRIPT__";
            $arrow = $thesePaths["ajaxScript"];
            $finalHTML["jwol"] = str_replace($target, $arrow, $finalHTML["jwol"]); 
        }
 * 
 */

    }
    
    public function getBody() {
	if (empty($this->body)) { $this->body = "<body></body>\n"; }
	return $this->body;
    }
    
    public function getDocType() {
	if (empty($this->docType)) { $this->docType = "<!DOCTYPE html>"; }
	return $this->docType;
    }

    public function setDoctype($doctype) {
	$this->doctype = $doctype;
    }

    public function getPage() {
	$this->page = $this->getDocType() . "\n" . $this->getHTML();
	return $this->page;
    }

    public function getHTML() {
	$this->html = "<html>\n" . $this->getHead() . "\n\n" . $this->getBody() . "\n</html>";
	return $this->html;
    }

    public function setMeta($meta) {
	$this->meta = $meta;
    }
    
    public function setTitle($title) {
	$this->title = "<title>" . $title . "</title>";
    }

    private function getCSSblock() {
	if (empty($this->css)) { $this->css = "<css></css>"; }
	return $this->css;
    }

    private function getScriptBlock() {
	if (empty($this->scriptBlock)) { $this->scriptBlock = "<script></script>"; }
	return $this->scriptBlock;
    }

    private function getMeta() {
	if (empty($this->meta)) {
	    $this->meta = "<meta name='viewport' content='width=device-width, initial-scale=1'>";
	}
	return $this->meta;
    }
    
    public function getTitle() {
	if (empty($this->title)) { $this->title = "<title></title>"; }

	return $this->title;
    }

    private function getHead() {
	
	if (empty($this->head)) {
	    $this->head = "<head>\n";

	    $this->head .= "\t<!--placeholder: head-->\n";
	}
	else {
	    $this->head = "<head>\n";

	    // meta
	    $this->head .= $this->getMeta();
	    
	    $this->head .= "\n" . $this->getTitle();
	    
	    $this->head .= "\n" . $this->getCSSblock();
	    
	    $this->head .= "\n" . $this->getScriptBlock();
	}

	$this->head .= "\n</head>\n";

	return $this->head;
    }

    public function addToBody($tag) {
	$arrow = "\n" . $tag . "\n</body>";
	$this->body = str_replace("</body>", $arrow, $this->body);
    }

    public function setBody($body) {
	$this->body = $body;
    }

    public function addCSS($stylesheet, $type) {
	if ($type === "fileRef") {
	    $this->css .= "<link rel='stylesheet' href='" . $stylesheet . "'>\n";
	}
	else {
	    $this->css .= $stylesheet;
	}
    }
    
    // Expects a valid html string: "<meta ... />"
    public function addMeta($metaString) {
	$this->meta .= $metaString . "\n";
    }

    // this function expects a filepath.
    // if type = "fileRef" then the script will be included as a src = parameter
    // otherwise, the filepath will be opened and the script will be included
    // in the <head>
    public function addScript($path, $type) {
	if ($type === "fileRef") {
	    $this->scriptBlock .= "<script type='text/javascript' src='" . $path . "'></script>\n";
	}
	else { 
	    $script = file_get_contents($path);
	    $this->scriptBlock .= $script . "\n";
	}
    }

    public function show() {
	echo $this->getPage();
    }
}