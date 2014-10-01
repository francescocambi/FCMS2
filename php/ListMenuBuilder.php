<?php

class ListMenuBuilder implements MenuBuilder {
	
	private $menuHTML = "";
	
	public function generateFor(Model\HierarchicalMenu $menu) {
		$this->menuHTML .= "<ul>";
		$children = $menu->getChildren();
            foreach ($children as $child) {
                $this->menuHTML .= "<li><a href=\"".$child->getURL()."\">".$child->getLabel()."</a>";
                if ($child->getChildren() != null)
                    $this->generateFor($child);
                $this->menuHTML .= "</li>";
            }
		
		$this->menuHTML .= "</ul>";
		
	}
	
	public function getHTML() {
		return $this->menuHTML;
	}
	
}

?>