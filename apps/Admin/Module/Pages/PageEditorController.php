<?php
/**
 * User: Francesco
 * Date: 27/02/15
 * Time: 16:42
 */

namespace App\Admin\Module\Pages;


use Model\Page;
use Model\Url;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class PageEditorController {

    /**
     * @param $em \Doctrine\ORM\EntityManager
     * @param $data array
     * @param null $pageid int
     * @return int Id of processed page
     */
    protected function insertUpdateProcessing($em, $data, $pageid = null) {
        $update = !is_null($pageid);

        if ($update)
            $page = $em->find('Model\Page', $pageid);
        else
            $page = new Page();

        if ($update) {
            $deleteurl = $em->createQueryBuilder()
                ->delete()
                ->from('Model\Url', "url")
                ->where("url.page=".$page->getId())
                ->getQuery();
            $deletepageblock = $em->createQueryBuilder()
                ->delete()
                ->from('Model\PageBlock', "pb")
                ->where("pb.page=".$page->getId())
                ->getQuery();
            $deleteurl->execute();
            $deletepageblock->execute();
        }

        $page->setName($data['name']);
        $page->setDescription($data['description']);
        $page->setTitle($data['title']);
        $page->setPublished(!is_null($data['published']));
        $page->setPublic(!is_null($data['public']));
        $language = $em->find('Model\Language', $data['language']);
        $page->setLanguage($language);

        //Url insertion
        foreach ($data['url'] as $urlstring)
            if (strlen($urlstring) > 2) {
                $url = new Url();
                $url->setUrl($urlstring);
                $page->addUrl($url);
            }

        if (!$update) {
            $em->persist($page);
            $em->flush();
        }

        //Processing Blocks
        for ($i=0;$i<count($data['block']['id']);$i++) {
            $blockid = $data['block']['id'][$i];

            //If block is new and content is empty, skip this block
            if ( !($blockid == 0 && strlen($data['block']['content'][$i]) == 0) ) {

                if ($blockid == 0) {
                    //Insert new block
                    $block = new \Model\ContentBlock();
                } else {
                    //Update existing block
                    $block = $em->find('Model\ContentBlock', $blockid);
                }

                //Sets block properties
                $block->setName($data['block']['name'][$i]);
                $block->setDescription($data['block']['description'][$i]);
                $block->setBlockStyleClassName($data['block']['style'][$i]);
                $block->setBgurl($data['block']['bckurl'][$i]);
                $block->setBgred($data['block']['bckred'][$i]);
                $block->setBggreen($data['block']['bckgreen'][$i]);
                $block->setBgblue($data['block']['bckblue'][$i]);
                $block->setBgopacity($data['block']['bckopacity'][$i]);
                $block->setBgrepeatx($data['block']['bckrepeatx'][$i]);
                $block->setBgrepeaty($data['block']['bckrepeaty'][$i]);
                $block->setBgsize($data['block']['bcksize'][$i]);

                if (!is_null($data['block']['content'][$i]) && $data['block']['content'][$i] != "")
                    $block->setContent($data['block']['content'][$i]);

                if ($blockid == 0)
                    $em->persist($block);
                else
                    $em->merge($block);

                $em->flush();

                //Adding block to page
                $page->addBlock($block, $i);
            }
        }

        if ($update)
            $em->merge($page);
        else
            $em->persist($page);

        $em->flush();

        return $page->getId();

    }

    /**
     * Page Insertion
     * @param Application $app
     * @param Request $request
     * @return string
     */
    public function insertPage(Application $app, Request $request)
    {
        try {
            $app['em']->beginTransaction();
            $data = $request->request->all();
            $newid = $this->insertUpdateProcessing($app['em'], $data);
            $app['em']->commit();
            $response['result'] = true;
        } catch (\Exception $e) {
            $app['em']->rollback();
            $response['result'] = false;
            $response['trace'] = $e->getTraceAsString();
            $response['exception'] = $e->getMessage();
            return var_export($response);
        }
        return $app->redirect(
            $app['url_generator']->generate('admin.pages.editPage', array(
                'id' => $newid
            ))."?s=true"
        );
    }

    /**
     * Page Update
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return string
     */
    public function updatePage(Application $app, Request $request, $id)
    {
        if (is_null($id)) return null;

        try {
            $app['em']->beginTransaction();
            $data = $request->request->all();
            $this->insertUpdateProcessing($app['em'], $data, $id);
            $app['em']->commit();
            $response['result'] = true;
        } catch (\Exception $e) {
            $app['em']->rollback();
            $response['result'] = false;
            $response['trace'] = $e->getTraceAsString();
            $response['exception'] = $e->getMessage();
            return var_export($response);
        }

        return $app->redirect(
            $app['url_generator']->generate('admin.pages.editPage', array(
                'id' => $id
            ))."?s=true"
        );
    }

    public function renderEditor(Application $app, Request $request, $id=null) {

        $exception = null;
        $page = null;

        $success = ($request->get('s') == "true") ;

        $languages = $app['em']->getRepository('Model\Language')->findAll();

        $blocks = $app['em']->getRepository('Model\Block')->findAll();

        $styles = $app['config']->get('BlockStyles');

        // UPDATE MODE
        if (!is_null($id) && $id > 0) {
            //Retrieving all informations about this page
            try {
                $page = $app['em']->find('Model\Page', $id);
            } catch (\Exception $e) {
                $exception['message'] = "EXCEPTION >> ".$e->getMessage();
                $exception['traceString'] = $e->getTraceAsString();
                $page = new Page();
            }
        } else {
            //Insert mode
            $page = new Page();
        }

        return $app['twig']->render('App\\Admin\\Module\\Pages\\Editor.twig', array(
            'menuBlock' => $app['admin.menu']->renderMenu("Pages"),
            'page' => $page,
            'exception' => $exception,
            'languages' => $languages,
            'blocks' => $blocks,
            'styles' => $styles,
            'success' => $success
        ));



    }

} 