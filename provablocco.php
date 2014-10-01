<?php
/**
 * User: Francesco
 * Date: 28/09/14
 * Time: 18.43
 */

use \Model\ContentBlock;

require_once("bootstrap.php");

$cb = new ContentBlock();
$cb->setName("blocco");
$cb->setDescription("blocco");
$cb->setContent("<h1>Contenuto</h1>");
$cb->setBlockStyle(new CenteredBlockStyle());
$cb->setBgUrl("url");
$cb->setBgRed(0);
$cb->setBgGreen(2);
$cb->setBgBlue(0);
$cb->setBgOpacity(0.3);
$cb->setBgRepeatx(false);
$cb->setBgRepeaty(true);
$cb->setBgSize("contain");

$entityManager->persist($cb);
$entityManager->flush();


$cb = $entityManager->find('Model\ContentBlock', 2);
echo "TEST NAME --> ".($cb->getName() == "blocco")."\n";
echo "TEST BLOCKSTYLE --> ".(get_class($cb->getBlockStyle()))."\n";
echo "----- HTML DUMP -----\n\n";
echo $cb->getHTML(null);
echo "\n\n ----- END -----\n";