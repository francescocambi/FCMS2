<?php
/**
 * User: Francesco
 * Date: 04/03/15
 * Time: 16:20
 */

namespace App\Admin\Module\Blocks;


use Doctrine\ORM\EntityManager;
use Model\ContentBlock;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class EditorController {

    public function renderEditor(Application $app, Request $request, $id=null) {

        $exception = null;
        $page = null;

        $success = ($request->get('s') == 'true');

        $styles = $app['config']->get('BlockStyles');

        if (is_null($id))
            $block = new ContentBlock();
        else {
            try {
                $block = $app['em']->find('Model\ContentBlock', $id);
            } catch (\Exception $e) {
                $app['monolog']->addError($e->getMessage());
                $exception = array(
                    'message' => 'EXCEPTION >> '.$e->getMessage(),
                    'traceString' => $e->getTraceAsString()
                );
                $block = new ContentBlock();
            }
        }


        return $app['twig']->render('App\Admin\Module\Blocks\Editor.twig', array(
           'block' => $block,
           'styles' => $styles,
            'success' => $success,
            'menuBlock' => $app['admin.menu']->renderMenu("Blocks"),
            'exception' => $exception
        ));

    }

    public function insertBlock(Application $app, Request $request) {
        $data = $request->request->all();
        try {
            $newid = $this->insertUpdateProcessing($app['em'], $data);

            return $app->redirect(
                $app['url_generator']->generate('admin.blocks.editBlock', array(
                    'id' => $newid
                ))."?s=true"
            );

        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return var_export(array(
                'status' => false,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
        }
    }

    public function updateBlock(Application $app, Request $request, $id) {
        $data = $request->request->all();

        try {
            $this->insertUpdateProcessing($app['em'], $data, $id);

            return $app->redirect(
                $app['url_generator']->generate('admin.blocks.editBlock', array(
                    'id' => $id
                ))."?s=true"
            );

        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return var_export(array(
                'status' => false,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
        }

    }

    public function insertUpdateProcessing(EntityManager $em, $data, $blockid=null) {
        $update = !is_null($blockid);

        //If insert mode, creates new instance
        if ($update) {
            $block = $em->find('Model\ContentBlock', $blockid);
        } else {
            $block = new ContentBlock();
        }

        //Update instance values
        $block->setName($data['name']);
        $block->setDescription($data['description']);
        $block->setBlockStyleClassName($_POST['blockStyleClassName']);
        $block->setBgurl($data['bgurl']);
        $block->setBgred($data['bgred']);
        $block->setBggreen($data['bggreen']);
        $block->setBgblue($data['bgblue']);
        $block->setBgopacity($data['bgopacity']);
        $block->setBgrepeatx(isset($data['bgrepeatx']));
        $block->setBgrepeaty(isset($data['bgrepeaty']));
        $block->setBgsize($data['bgsize']);
        if (strlen($data['content']) > 0)
            $block->setContent($data['content']);

        try {
            $em->beginTransaction();
            if ($update)
                $em->merge($block);
            else
                $em->persist($block);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw $e;
        }

        return $block->getId();
    }

} 