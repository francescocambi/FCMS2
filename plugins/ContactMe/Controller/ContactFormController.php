<?php
/**
 * User: Francesco
 * Date: 06/02/15
 * Time: 17:44
 */

namespace Plugin\ContactMe\Controller;


class ContactFormController {

    /**
     * @param \Silex\Application $app
     */
    public function getHTML($app) {

        //Analyze request
        if ( is_null($app['request']->request->get('referrer')) ) {

            //If POST is empty render form
            return $app['twig']->render('contactFormBlock.twig');
        } else {
            return $app['twig']->render('formFeedbackBlock.twig');
            //else persist data, handle results and display op completed message
        }

    }

} 