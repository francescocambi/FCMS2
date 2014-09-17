<?php

class GETRequestManager implements RequestManager {
	
	//TODO: Definire dove prendere i dao
	public function getParsedRequest() {
		$page; $language; $user;
		$params = funct_get_args();
		$get = $params[0];
		
		//Rileva la pagina da visualizzare analizzando la querystring nell'url
		if (isset($get['id'])) {
			$page = $pagedao->getById($get['id']);
		} else if (isset($get['name'])) {
			$page = $pagedao->getByName($get['name']);
		} else if (isset($get['urlstring'])) {
			$page = $pagedao->getByURL($get['urlstring']);
		} else {
			$page = $pagedao->getByName("HomePage");
		}
		
		//Rileva la lingua
		if (isset($get['lang'])) {
			$language = $languagedao->getByCode($get['lang']);
		} else {
			//TODO Richiede a languagedao la lingua di default nelle impostazioni
			$language = $languagedao->getByCode('it');
		}
		
		//Rileva l'utente e delega il suo riconoscimento a LoginManager
		$session = $params[1];
		//Riconosce il codice di sessione e se lo fa cercare da LoginManager
		//che gli restituisce l'oggetto utente associato
		//TODO
		
		return new Request($page, $language, $user);
		
	}
	
}

?>
