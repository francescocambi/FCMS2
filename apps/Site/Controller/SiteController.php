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

    function renderPage($lang='', $url='', Application $app)
    {

        //Detect and retrieve requested language
        if (strlen($lang) == 0)
            $lang = $app['config']->get('Site.defaultLanguageCode');
        $language = $app['em']->getRepository('\Model\Language')->findOneBy(array("code" => $lang));
        if (is_null($language)) {
            //Retrieve default language
            $language = $app['em']->getRepository('\Model\Language')->findOneBy(array(
                "code" => $app['config']->get('Site.defaultLanguageCode')
            ));
        }

        //Detect and retrieve requested page
        $urlEntry = null;
        if (strlen($url) > 0)
            $urlEntry = $app['em']->find('Model\Url', $url);

        if (is_null($urlEntry) === FALSE) {
            //Valid url, page retrieved
            $page = $urlEntry->getPage();
        } else {
            //Not valid url, retrieve home page for detected language
            $homePageName = $app['config']->get('Site.homePageNamePrefix').$language->getCode();
            $page = $app['em']->getRepository('\Model\Page')->findOneBy(array(
                'name' => $homePageName
            ));
        }

        $menu = $language->getMenu();
        $menuBuilder = new MenuBuilder($app, $menu);

        $languages = $app['em']->getRepository('\Model\Language')->findAll();

        $languageBar = new LanguageBar($app, $languages);

        $bodyBgUrl = $app['em']->getRepository('\Model\Setting')->findOneBy(array("settingKey" => "BODY_BG"))->getSettingValue();

        $titleSuffix = $app['em']->getRepository('\Model\Setting')->findOneBy(array("settingKey" => "TITLE_DESC"))->getSettingValue();

        return $app['twig']->render('App\\Site\\Page.twig', array(
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