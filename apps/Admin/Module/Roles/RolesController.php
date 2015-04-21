<?php
/**
 * User: Francesco
 * Date: 17/04/15
 * Time: 15:47
 */

namespace App\Admin\Module\Roles;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class RolesController {

    public function listRoles(Application $app) {

        $roles = $app['em']->getRepository('App\Admin\Model\Role')->findAll();
        $menuBlock = $app['admin.menu']->renderMenu('Roles');

        return $app['twig']->render('App\Admin\Module\Roles\List.twig', array(
            'roles' => $roles,
            'menuBlock' => $menuBlock
        ));

    }

    public function delete($id, Application $app) {

        $role = $app['em']->find('App\Admin\Model\Role', $id);

        if (is_null($role)) {
            return new Response($app['admin.message_composer']->failureMessage("Role not found."), 404);
        }

        try {
            $app['em']->beginTransaction();
            $app['em']->remove($role);
            $app['em']->flush();
            $app['em']->commit();
        } catch (\Exception $e) {
            $app['em']->rollback();
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        return $app['admin.message_composer']->successMessage();

    }

    public function checkNameUnique(Application $app, Request $request) {
        $name = $request->get('name');
        $id = $request->get('id') or -1;

        try {
            $role = $app['em']->getRepository('App\Admin\Model\Role')->findOneBy(array('name' => $name));
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        return new Response($app['admin.message_composer']->dataMessage(array(
            'unique' => (is_null($role) || $role->getId() == $id)
        )));
    }

} 