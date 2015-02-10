<?php

namespace App\Site\Menu;

use Silex\Application;
use Model\IHierarchicalMenu;

class MenuBuilder implements IMenuBuilder {

    /**
     * @var string
     */
	protected $menuHTML;

    /**
     * @param Application $app
     * @param IHierarchicalMenu $menu
     */
    public function __construct(Application $app, IHierarchicalMenu $menu) {

        $context = array(
            'menuobj' => $menu
        );

        $this->menuHTML = $app['twig']->render(
            $app['config']->get('Menu.View'),
            $context
        );

    }

    /**
     * @return string
     */
	public function getHTML() {
		return $this->menuHTML;
	}
	
}

?>