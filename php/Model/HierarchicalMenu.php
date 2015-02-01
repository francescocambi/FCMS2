<?php

namespace Model;

interface HierarchicalMenu {

    /**
     * @return HierarchicalMenu[]
     */
    public function getChildren();
	
}

?>