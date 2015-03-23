<?php
/**
 * User: Francesco
 * Date: 06/03/15
 * Time: 15:42
 */

namespace App\Admin\Module\CssEditor;

define('CSS_FOLDER_PATH', DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'css');

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CssEditorController {

    public function render(Application $app) {

        $menuBlock = $app['admin.menu']->renderMenu('CssEditor');

        //Retrieve css files in CSS_FOLDER_PATH
        $files = scandir($app['rootFolderPath'].CSS_FOLDER_PATH);

        $cssfiles = array();
        foreach($files as $file) {
            if (strpos($file, '.css') == strlen($file)-4) {
                array_push($cssfiles, $file);
            }
        }


        return $app['twig']->render('App\\Admin\\Module\\CssEditor\\CssEditor.twig', array(
            'menuBlock' => $menuBlock,
            'files' => $cssfiles
        ));

    }

    public function getFileContent(Application $app, Request $request) {

        $filename = $request->get('fileName');

        $filePath = $app['rootFolderPath'].CSS_FOLDER_PATH.DIRECTORY_SEPARATOR.$filename;

        //Open file and read its content
        //$lines is the stringified content of the file
        $content = file_get_contents($filePath);

        if ($content === FALSE) {
            //Can't open files
            $app['monolog']->addError("Can't load file ".$filePath." in CssEditor.");
            return new Response($app['admin.message_composer']->failureMessage("Can't load requested file."), 500);
        }

        //Return file content
        return $app['admin.message_composer']->dataMessage(array(
            'fileContent' => $content
        ));

    }

    public function saveFile(Application $app, Request $request) {

        $filename = $request->get('fileName');
        $content = $request->request->get('fileContent');

        $filePath = $app['rootFolderPath'].CSS_FOLDER_PATH.DIRECTORY_SEPARATOR.$filename;

        //Make a backup copy of the file that will be overwritten
        $cpy_result = copy($filePath, $filePath.".bck");

        if ($cpy_result === FALSE) {
            $app['monolog']->addError("Can't make a backup copy of file ".$filePath." in CssEditor.");
            return new Response($app['admin.message_composer']->failureMessage("Can't save file."), 500);
        }

        //Overwrite file if it exists. Otherwise a new file is created.
        $result = file_put_contents($filePath, $content);

        //Check operation result
        if ($result === FALSE) {
            $app['monolog']->addError("Can't write content on ".$filePath." in CssEditor.");
            return new Response($app['admin.message_composer']->failureMessage("Can't save file."), 500);
        }

        return $app['admin.message_composer']->successMessage();
    }

} 