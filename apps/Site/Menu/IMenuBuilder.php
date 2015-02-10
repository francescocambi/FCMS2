<?php

namespace App\Site\Menu;

use Silex\Application;
use Model\IHierarchicalMenu;

interface IMenuBuilder {
	
	public function __construct(Application $app, IHierarchicalMenu $menu);
	
	public function getHTML();
	
}

?>