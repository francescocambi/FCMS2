<?php
/**
 * User: Francesco
 * Date: 17/04/15
 * Time: 16:08
 */

namespace App\Admin\Module\Roles;


use App\Admin\Model\Module;
use App\Admin\Model\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditorController {

    public function renderEditor(Application $app, Request $request, $id=null) {

        $success = ($request->get('s') == "true");
        $exception = null;

        if (is_null($id))
            $role = new Role();
        else {
            try {
                $role = $app['em']->find('App\Admin\Model\Role', $id);
            } catch (\Exception $e) {
                $exception = $e;
                $role = new Role();
                $app['monolog']->addError($e->getMessage());
            }
        }

        $modules = $app['em']->getRepository('\App\Admin\Model\Module')->findAll();

        $menuBlock = $app['admin.menu']->renderMenu('Roles');

        return $app['twig']->render('App\\Admin\\Module\\Roles\\Editor.twig', array(
            'success' => $success,
            'exception' => $exception,
            'role' => $role,
            'modules' => $modules,
            'menuBlock' => $menuBlock
        ));
    }

    public function save(Application $app, Request $request, $id=null) {

        $data = $request->request->all();

        try {
            $processedId = $this->insertUpdateProcessing($app['em'], $data, $id);

            return $app->redirect(
                $app['url_generator']->generate('admin.roles.edit', array('id' => $processedId))."?s=true"
            );
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response(
                $app['admin.message_composer']->exceptionMessage($e), 500
            );
        }

    }

    /**
     * @param $em EntityManager
     * @param $data array
     * @param $id int
     * @return int
     * @throws \Exception
     */
    public function insertUpdateProcessing($em, $data, $id) {
        $update = !is_null($id);

        try {
            $em->beginTransaction();

            if ($update) {
                $role = $em->find('App\Admin\Model\Role', $id);
            } else {
                $role = new Role();
            }

            $role->setName($data['name']);
            $role->setDescription($data['description']);

            $role->setModules(new ArrayCollection());

            if ($update) {
                /** @var Module[] $modules */
                $modules = $em->getRepository('App\Admin\Model\Module')->findAll();
                foreach ($modules as $module) {
                    $contains = $module->getAllowedRoles()->contains($role);
                    if (isset($data['modules'][$module->getId()])) {
                        if (!$contains)
                            $module->addAllowedRole($role);
                    } else {
                        if ($contains)
                            $module->removeAllowedRole($role);
                    }
                }
            } else {
                //New role
                /** @var Module[] $modules */
                $modules = $em->getRepository('App\Admin\Model\Module')->findAll();
                foreach ($modules as $module) {
                    if (isset($data['modules'][$module->getId()]))
                        $module->addAllowedRole($role);
                }
            }

            if ($update)
                $em->merge($role);
            else
                $em->persist($role);

            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw $e;
        }

        return $role->getId();
    }

} 