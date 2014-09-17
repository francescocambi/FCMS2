<?php

interface MenuBuilder {
	
	public function generateFor(HierarchicalMenu $menu);
	
	public function getHTML();
	
}

?>