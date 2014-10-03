<?php

class GETRequestManager implements RequestManager {

    protected $em;

    /**
     * @param $em \Doctrine\ORM\EntityManager
     */
    public function __construct($em) {
        $this->em = $em;
    }

	public function getRequest() {
        $params = func_get_args();
		$get = $params[0];

		//Rileva la pagina da visualizzare analizzando la querystring nell'url
		if (isset($get['id']) && strlen($get['id']) > 0) {
            $page = $this->em->find('Model\Page', $get['id']);
		} else if (isset($get['url']) && strlen($get['url']) > 0) {
			$url = $this->em->find('Model\Url', $get['url']);
            if (!is_null($url))
                $page = $url->getPage();
        }
	    if (is_null($page)) {
            $page = $this->em->getRepository('Model\Page')->findOneBy(array("name" => "home"));
        }

		//Rileva la lingua
        if (is_null($get['lang'])) {
            $langCode = "it"; //Eventualmente quella di default nelle impostazioni
        } else {
            $langCode = $get['lang'];
        }
        $language = $this->em->getRepository('Model\Language')->findOneBy(array("code" => $langCode));
		
		//Rileva l'utente e delega il suo riconoscimento a LoginManager
		$session = $params[1];
		//Riconosce il codice di sessione e se lo fa cercare da LoginManager
		//che gli restituisce l'oggetto utente associato
		//TODO
		
		return new Request($page, $language, null);
		
	}
	
}

?>
