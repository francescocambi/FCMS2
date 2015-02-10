<?php
/**
 * User: Francesco
 * Date: 02/02/15
 * Time: 17:57
 */

namespace App\Site\Controller;

use App\Site\LanguageBar\LanguageBar;

use App\Site\Menu\MenuBuilder;
use Silex\Application;

class SiteController {

    function renderPage($lang='it', $url='', Application $app)
    {

        $requestmanager = new \GETRequestManager($app['em']);
        $request = $requestmanager->getRequest(array(
            "url" => $url,
            "lang" => $lang
        ), null);

        $menu = $request->getLanguage()->getMenu();
        $menuBuilder = new MenuBuilder($app, $menu);

        $languages = $app['em']->getRepository('\Model\Language')->findAll();

        $languageBar = new LanguageBar($app, $languages);

        $bodyBgUrl = $app['em']->getRepository('\Model\Setting')->findOneBy(array("settingKey" => "BODY_BG"))->getSettingValue();

        $page = $request->getPage();

        $titleSuffix = $app['em']->getRepository('\Model\Setting')->findOneBy(array("settingKey" => "TITLE_DESC"))->getSettingValue();

        return $app['twig']->render('Page.twig', array(
            "title" => $page->getTitle(),
            "titleSuffix" => $titleSuffix,
            "pageDescription" => $page->getDescription(),
            "bodyBgUrl" => $bodyBgUrl,
            "languageBarHtml" => $languageBar->getHTML(),
            "menuHtml" => $menuBuilder->getHTML(),
            "page" => $page,
            "menuobj" => $menu
        ));
    }

} 