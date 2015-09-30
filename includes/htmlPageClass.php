<?php

class htmlPage {

    public function setDoctype($doctype) {
	if (empty($doctype)) {
	    $this->doctype = "<!DOCTYPE html>";
	}
    }

    public function setPage() {
	$this->page = $this->getDoctype() . $this->getHTML();
    }

    public function getPage() {
	return $this->page;
    }

    public function setHTML() {
	$this->html = $this->head . $this->body;
    }

    public function setMeta($meta) {
	if (empty($meta)) {
	    // $this->meta = "<meta name='viewport' content='width=device-width, initial-scale=1'>";
	}
	else { $this->meta = $meta; }
    }

    public function setHead() {
	
	$this->head .= "<head>\n";

	// meta
	if (empty($this->meta)) {}
	else { $this->head .= $this->meta; }

	// title
	$this->head .= $this->title;

	// CSS
	$this->head .= $this->cssBlock;
	
	// <script>
	
	$this->head .= $this->scriptBlock;
	
	$this->head .= "</head>";
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