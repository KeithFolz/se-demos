<?php

class htmlPage {

    public function htmlPage() {
	$this->page = $this->getPage();
    }

    private function getBody() {
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
	$this->title = $title;
    }

    private function getCSSblock() {
	if (empty($this->css)) { $this->css = "<css></css>"; }
	return $this->css;
    }

    private function getScriptBlock() {
	if (empty($this->scripts)) { $this->scripts = "<script></script>"; }
	return $this->scripts;
    }

    private function getMeta() {
	if (empty($this->meta)) {
	    $this->meta = "<meta name='viewport' content='width=device-width, initial-scale=1'>";
	}
	return $this->meta;
    }
    
    private function getTitle() {
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

    public function setBody($body) {
	$this->body = "<body>";
	$this->body .= $body;
	$this->body .= "</body>";
    }

    // <link rel="stylesheet" href="/JanrainDemoSites/default/styles/screen.css" />
    public function addCSS($stylesheet, $type) {
	if ($type === "fileRef") {
	    $this->css .= "<link rel='stylesheet' href='" . $stylesheet . '">\n';
	}
	else {
	    $this->css .= $stylesheet;
	}
    }

//    <script type="text/javascript" src="/JanrainDemoSites/default/scripts/navigation.js"></script>

    // this function expects either a filepath (type = fileRef) or
    // a string enclosed in <script></script> tags
    public function addScript($script, $type) {
	if ($type === "fileRef") {
	    $this->scriptBlock .= "<script type='text/javascript' src='" . $script . "'></script>";
	}
	else { $this->scriptBlock .= $script . "\n"; }
    }
    
    public function show() {
	echo $this->getPage();
    }
}