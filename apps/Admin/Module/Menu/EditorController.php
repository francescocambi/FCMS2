<?php
/**
 * User: Francesco
 * Date: 05/03/15
 * Time: 18:01
 */

namespace App\Admin\Module\Menu;


use Doctrine\ORM\EntityManager;
use Model\Menu;
use Model\MenuItem;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditorController {

    public function renderEditor(Application $app, Request $request, $id=null) {

        $menuBlock = $app['admin.menu']->renderMenu('Menu');

        $success = ($request->get('s') == "true");

        $exception = null;

        if (is_null($id)) {
            $menu = new Menu();
        } else {
            try {
                $menu = $app['em']->find('Model\Menu', $id);
            } catch (\Exception $e) {
                $exception = $e;
                $app['monolog']->addError($e->getMessage());
                $menu = new Menu();
            }
        }

        return $app['twig']->render('App\\Admin\\Module\\Menu\\Editor.twig', array(
            'menuBlock' => $menuBlock,
            'success' => $success,
            'exception' => $exception,
            'menu' => $menu
        ));
    }

    public function insertMenu(Application $app, Request $request) {
        $data = $request->request->all();

        try {
            $newid = $this->insertUpdateProcessing($app['em'], $data);

            return $app->redirect(
                $app['url_generator']->generate('admin.menu.edit', array(
                    'id' => $newid
                ))."?s=true"
            );

        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response(var_export(array(
                'status' => false,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            )), 500);
        }
    }

    public function updateMenu(Application $app, Request $request, $id) {
        $data = $request->request->all();

        try {
            $this->insertUpdateProcessing($app['em'], $data, $id);

            return $app->redirect(
                $app['url_generator']->generate('admin.menu.edit', array(
                    'id' => $id
                ))."?s=true"
            );

        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response(var_export(array(
                'status' => false,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            )), 500);
        }
    }

    private function insertUpdateProcessing(EntityManager $em, $data, $id=null) {

        $update = !is_null($id);

        try {
            $em->beginTransaction();

            //Create or retrive entity
            if ($update) {
                $menu = $em->find('Model\Menu', $id);
            } else {
                $menu = new Menu();
            }

            //If update mode delete all items on db
            if ($update)
                foreach ($menu->getChildren() as $child)
                    $em->remove($child);

            $menu->setName($data['menu_name']);
            $menu->setDescription($data['menu_description']);

            if ($update)
                $em->merge($menu);
            else
                $em->persist($menu);
            $em->flush();
            $items = $this->processMenu($data, $menu->getId(), array());
            foreach ($items as $item) {
                $menu->addMenuItem($item);
            }
            $em->merge($menu);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw $e;
        }
        return $menu->getId();
    }

    /**
     * @param $data
     * @param $menuid
     * @param $order
     * @return MenuItem[]
     */
    public function processMenu($data, $menuid, $order) {
        $menuitems = array();
        for ($i = 0; $i < count($data['level']); $i++) {
            if (strlen($data['label'][$i]) > 0 && $data['level'][$i] == 0) {
                $menuitem = new MenuItem();
                $menuitem->setLabel($data['label'][$i]);
                $menuitem->setUrl($data['url'][$i]);
                if (!isset($order[$data['level'][$i]])) $order[$data['level'][$i]] = 0;
                $menuitem->setItemOrder($order[$data['level'][$i]]++);
                $menuitem->setMenu($menuid);
                $menuitem->setHidden(false);
                //Se questo elemento ha figli
                if ($data['level'][$i+1] > $data['level'][$i]) {
                    $processedItems = $this->processChildren($data, $i, $menuid, $order, $data['level'][$i+1]);
                    $i += count($processedItems);
                    foreach ($processedItems as $item) {
                        $menuitem->addChild($item);
                    }
                }
                array_push($menuitems, $menuitem);
            }
        }
        return $menuitems;
    }

    //Processa figli, ritorna un array contenente i menu item figli
    public function processChildren($data, $i, $menuid, $order, $level) {
        $menuitems = array();
        for ($i++; $i < count($data['level']) && $data['level'][$i] == $level; $i++) {
            if (strlen($data['label'][$i]) > 0) {
                $menuitem = new MenuItem();
                $menuitem->setLabel($data['label'][$i]);
                $menuitem->setUrl($data['url'][$i]);
                if (!isset($order[$data['level'][$i]])) $order[$data['level'][$i]] = 0;
                $menuitem->setItemOrder($order[$data['level'][$i]]++);
                $menuitem->setMenu($menuid);
                $menuitem->setHidden(false);
                //Se questo elemento ha figli
                if ($data['level'][$i+1] > $data['level'][$i]) {
                    $processedItems = $this->processChildren($data, $i, $menuid, $order, $data['level'][$i+1]);
                    $i += count($processedItems);
                    foreach ($processedItems as $item)
                        $menuitem->addChild($item);
                }
                array_push($menuitems, $menuitem);
            }
        }
        return $menuitems;
    }
} 