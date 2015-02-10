<?php

namespace Model;

interface IHierarchicalMenu {

    /**
     * @return IHierarchicalMenu[]
     */
    public function getChildren();
	
}

?>