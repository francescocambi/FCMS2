<?php
/**
 * User: Francesco
 * Date: 29/03/15
 * Time: 21:43
 */

namespace App\Admin\Module\Users;


use Model\User;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Role\Role;

class EditorController {

    public function renderEditor(Application $app, Request $request, $id=null) {

        $success = ($request->get('s') == "true");
        $exception = null;

        if (is_null($id))
            $user = new User();
        else {
            try {
                $user = $app['em']->find('Model\User', $id);
            } catch (\Exception $e) {
                $exception = $e;
                $user = new User();
                $app['monolog']->addError($e->getMessage());
            }
        }

        $roles = $app['em']->getRepository('\App\Admin\Model\Role')->findAll();

        $menuBlock = $app['admin.menu']->renderMenu('Users');

        return $app['twig']->render('App\\Admin\\Module\\Users\\Editor.twig', array(
            'success' => $success,
            'exception' => $exception,
            'user' => $user,
            'roles' => $roles,
            'menuBlock' => $menuBlock
        ));
    }

    public function save(Application $app, Request $request, $id=null) {

        $data = $request->request->all();

        try {
            $processedId = $this->insertUpdateProcessing($app['em'], $app['password_encoder'], $data, $id);

            return $app->redirect(
                $app['url_generator']->generate('admin.users.edit', array('id' => $processedId))."?s=true"
            );
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response(
                $app['admin.message_composer']->exceptionMessage($e), 500
            );
        }

    }

    public function insertUpdateProcessing($em, $passwordEncoder, $data, $id) {
        $update = !is_null($id);

        try {
            $em->beginTransaction();

            if ($update) {
                $user = $em->find('Model\User', $id);
            } else {
                $user = new User();
            }

            $user->setUsername($data['username']);

            //Regenerate salt and update password only if a new password is provided
            if (isset($data['password'])
                && strlen($data['password']) > 8
                && strlen($data['password']) < 1024) {

                //Prepare salt
//                $salt = base64_encode(mcrypt_create_iv(ceil(0.75 * 32), MCRYPT_DEV_URANDOM));
                $salt = uniqid("", true);
                $user->setSalt($salt);

                //Encrypt password
                $password = $passwordEncoder->encodePassword($data['password'], $user->getSalt());
                $user->setPassword($password);
            }

            $user->setName($data['name']);
            $user->setSurname($data['surname']);
            $user->setEmail($data['email']);
            $user->setPhone($data['phone']);
            $user->setAddress($data['address']);
            $user->setCap($data['cap']);
            $user->setCity($data['city']);
            $user->setProvince($data['province']);
            $user->setCountry($data['country']);

            //Prepare roles string
            $roles = array();
            if (isset($data['roles']))
                foreach ($data['roles'] as $roleString => $val)
                    $roles[] = new Role($roleString);
            $user->setRoles($roles);

            //Prepare accountExpiration DateTime object
            $accountExpiration = new \DateTime($data['accountExpiration'], new \DateTimeZone('Europe/Rome'));
            $user->setAccountExpiration($accountExpiration);

            $enabled = (isset($data['enabled']));
            $user->setEnabled($enabled);

            //Prepare credentialsExpiration DateTime object
            $credentialsExpiration = new \DateTime($data['credentialsExpiration'], new \DateTimeZone('Europe/Rome'));
            $user->setCredentialsExpiration($credentialsExpiration);

            if ($update)
                $em->merge($user);
            else
                $em->persist($user);

            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw $e;
        }

        return $user->getId();
    }

} 