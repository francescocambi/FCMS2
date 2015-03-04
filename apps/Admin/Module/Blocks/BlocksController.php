<?php
/**
 * User: Francesco
 * Date: 28/02/15
 * Time: 19:04
 */

namespace App\Admin\Module\Blocks;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class BlocksController {

    public function checkNameUnique(Application $app, Request $request) {
        $name = $request->get('name');

        if (strlen($name) == 0) {
            $response['result'] = false;
            $response['errormessage'] = "Name argument not provided.";
            return json_encode($response);
        }

        $blockid = $request->get('blockid') or -1;
        $response = array();
        try {
            $block = $app['em']->getRepository('Model\Block')->findOneBy(array("name" => $name));
        } catch (\Exception $e) {
            $response['status'] = 'error';
            $response['errormessage'] = "EXCEPTION => ".$e->getMessage()."\n\nTRACE => ".$e->getTraceAsString();
            return json_encode($response);
        }
        $response['status'] = "ok";
        if (is_null($block) || $block->getId() == $blockid) {
            $response['result'] = "true";
        } else {
            $response['result'] = "false";
        }
        return json_encode($response);
    }

    /**
     * @param Application $app
     * @param int $id
     * @return string
     */
    public function getBlock(Application $app, $id) {

        try {
            $requestedBlock = $app['em']->find('Model\ContentBlock', $id);
        } catch (\Exception $e) {
            $response['status'] = 'error';
            $response['errormessage'] = "EXCEPTION => ".$e->getMessage()."\n\nTRACE => ".$e->getTraceAsString();
            return json_encode($response);
        }

        if (is_null($requestedBlock)) exit('{}');

        $mapping = array(
            "ID" => $requestedBlock->getId(),
            "NAME" => $requestedBlock->getName(),
            "DESCRIPTION" => $requestedBlock->getDescription(),
            "BLOCK_STYLE_ID" => $requestedBlock->getBlockStyleClassName(),
            "BG_URL" => $requestedBlock->getBgurl(),
            "BG_RED" => $requestedBlock->getBgred(),
            "BG_GREEN" => $requestedBlock->getBggreen(),
            "BG_BLUE" => $requestedBlock->getBgblue(),
            "BG_OPACITY" => $requestedBlock->getBgopacity(),
            "BG_REPEATX" => $requestedBlock->getBgrepeatx(),
            "BG_REPEATY" => $requestedBlock->getBgrepeaty(),
            "BG_SIZE" => $requestedBlock->getBgsize(),
            "CONTENT" => $requestedBlock->getContent()
        );

        //Encode object to JSON and print

        $result_string = "{";

        foreach ($mapping as $key => $value) {
            $result_string .= "\t".json_encode($key).": ".json_encode($value).",\n";
        }
        $result_string = substr($result_string, 0, strlen($result_string)-2);
        $result_string .= "\n}";

        return $result_string;
    }

} 