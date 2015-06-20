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
        $page = null;
        $language = null;

        //Search page for requested url
        $urlEntry = null;
        if (strlen($url) > 0)
            $urlEntry = $app['em']->find('Model\Url', $url);
        if (is_null($urlEntry) === FALSE) {
            //Retrieve Page for this url
            $page = $urlEntry->getPage();
        }
        if (is_null($page)) {
            //Retrieve home page
            if (strlen($lang) > 0) {
                //for selected language
                $language = $app['em']->getRepository('\Model\Language')->findOneBy(array("code" => $lang));
            } else {
                //or for default language
                $language = $app['em']->getRepository('\Model\Language')->findOneBy(array(
                    "code" => $app['config']->get('Site.defaultLanguageCode')
                ));
            }
            //Retrieve home page
            $homePageName = $app['config']->get('Site.homePageNamePrefix').$language->getCode();
            $page = $app['em']->getRepository('\Model\Page')->findOneBy(array(
                'name' => $homePageName
            ));
        } else {
            $language = $page->getLanguage();
        }

        $menu = $language->getMenu();
        $menuBuilder = new MenuBuilder($app, $menu);

        $languages = $app['em']->getRepository('\Model\Language')->findAll();

        $languageBar = new LanguageBar($app, $languages);

        $bodyBgUrl = $app['em']->getRepository('\Model\Setting')->findOneBy(array("settingKey" => "BODY_BG"))->getSettingValue();

        $titleSuffix = $app['em']->getRepository('\Model\Setting')->findOneBy(array("settingKey" => "TITLE_DESC"))->getSettingValue();

        $pageView = $app['config']->get('Site.PageView');

        return $app['twig']->render($pageView, array(
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