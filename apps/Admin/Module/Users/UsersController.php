<?php
/**
 * User: Francesco
 * Date: 29/03/15
 * Time: 21:33
 */

namespace App\Admin\Module\Users;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController {


    public function listUsers(Application $app) {

        $users = $app['em']->getRepository('Model\User')->findAll();

        $menuBlock = $app['admin.menu']->renderMenu('Users');

        return $app['twig']->render('App\\Admin\\Module\\Users\\List.twig', array(
            'users' => $users,
            'menuBlock' => $menuBlock
        ));
    }

    public function delete(Application $app, $id) {

        $user = $app['em']->find('Model\User', $id);

        if (is_null($user)) {
            return new Response($app['admin.message_composer']->failureMessage("User not found."), 404);
        }

        try {
            $app['em']->beginTransaction();
            $app['em']->remove($user);
            $app['em']->flush();
            $app['em']->commit();
        } catch (\Exception $e) {
            $app['em']->rollback();
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        return $app['admin.message_composer']->successMessage();
    }

    public function checkUsernameUnique(Application $app, Request $request) {
        $username = $request->get('username');
        $id = $request->get('id') or -1;

        try {
            $user = $app['em']->getRepository('Model\User')->findOneBy(array('username' => $username));
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        return new Response($app['admin.message_composer']->dataMessage(array(
            'unique' => (is_null($user) || $user->getId() == $id)
        )));
    }

} 