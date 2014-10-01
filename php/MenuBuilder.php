<?php
interface MenuBuilder {
	
	public function generateFor(Model\HierarchicalMenu $menu);
	
	public function getHTML();
	
}

?>