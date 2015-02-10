<?php

namespace App\Site\LanguageBar;

use \Model\Language;
use \Silex\Application;

class LanguageBar {

    /**
     * @var \Silex\Application
     */
    protected $app;

    /**
     * @var Language[]
     */
    protected $languages;

    /**
     * UrlGenerator is responsible for flags link url generation with
     * the appropriate language code
     * @param \Silex\Application $app
     * @param Language[] $languages
     */
    public function __construct(Application $app, array $languages) {
        $this->languages = $languages;
        $this->app = $app;
    }

    /**
     * Render language bar view
     * @return string
     */
	public function getHTML() {
        $context = array(
            'languages' => $this->languages
        );

        return $this->app['twig']->render(
            $this->app['config']->get('LanguageBar.View'),
            $context
        );
	}

}
