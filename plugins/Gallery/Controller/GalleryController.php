<?php
/**
 * User: Francesco
 * Date: 07/10/15
 * Time: 16:59
 */

namespace Plugin\Gallery\Controller;


class GalleryController
{
    /**
     * @param \Silex\Application $app
     */
    public function getHTML($app) {

        $galleries = $app['em']->getRepository('Plugin\Gallery\Model\Gallery')->findAll();

        return $app['twig']->render('Plugin\\Gallery\\tableGallery.twig', array(
            'galleries' => $galleries
        ));

    }

}