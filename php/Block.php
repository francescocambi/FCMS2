<?php

abstract class Block {
	
	private $id;
	private $name;
	private $description;
	private $bgurl;
	private $bgred;
	private $bggreen;
	private $bgblue;
	private $bgopacity;
	private $bgrepeatx;
	private $bgrepeaty;
	private $bgsize;
	
	public function __construct($id, $name, $description, $bgurl, $bgred, $bggreen, $bgblue, $bgopacity, $bgrepeatx, $bgrepeaty, $bgsize) {
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->bgurl = $bgurl;
		$this->bgred = $bgred;
		$this->bggreen = $bggreen;
		$this->bgblue = $bgblue;
		$this->bgopacity = $bgopacity;
		$this->bgrepeatx = $bgrepeatx;
		$this->bgrepeaty = $bgrepeaty;
		$this->bgsize = $bgsize;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function setBlockStyle(BlockStyle $style) {
		$this->blockStyle = $style;
	}
	
	public function getBlockStyle() {
		return $this->blockStyle;
	}
	
	public abstract function hasLanguage(Language $lang);
	
	public abstract function getHTML(Language $lang);
	
	public function getBackgroundCSS() {
		$stringcss = "background:";
		if ($this->bgurl != "") $stringcss .= " url('".$this->bgurl."')";
		if ($this->bgred != "" && $this->bggreen != "" && $this->bgblue != "") {
			$opacity = 1;
			if (isset($this->bgopacity) && $this->bgopacity != "")
				$opacity = $this->bgopacity;
			$stringcss .= " rgba($this->bgred, $this->bggreen, $this->bgblue, $opacity)";
		} 
		if ($this->bgrepeatx == 1 && $this->bgrepeaty == 1) $stringcss .= " repeat";
		if ($this->bgrepeatx == 1 && $this->bgrepeaty == 0) $stringcss .= " repeat-x";
		if ($this->bgrepeatx == 0 && $this->bgrepeaty == 1) $stringcss .= " repeat-y";
		if ($this->bgrepeatx == 0 && $this->bgrepeaty == 0) $stringcss .= " no-repeat";
		
		if ($stringcss == "background:") {
			$stringcss = "";
		} else {
			$stringcss .= "; ";
		}
		
		if ($this->bgsize != "") $stringcss .= "background-size: $this->bgsize;";
		
		return $stringcss;
	}
	
	
}

?>