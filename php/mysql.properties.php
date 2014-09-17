<?php

//FIXME Questi parametri vanno iniettati con un oggetto altrimenti o non vengono letti da chi li usa oppure
//mi ritrovo i parametri del db globali in tutta l'applicazione
class ConnectionParameters {
	private $MYSQL_DBNAME = "framework_new";
	private $MYSQL_HOST = "127.0.0.1";
	public $MYSQL_USERNAME = "root";
	public $MYSQL_PASSWORD = "mysql";
	
	public $MYSQL_CONN_STRING;
	
	public function __construct() {
		$this->MYSQL_CONN_STRING = "mysql:dbname=".$this->MYSQL_DBNAME.";host=".$this->MYSQL_HOST;
	}
}



?>