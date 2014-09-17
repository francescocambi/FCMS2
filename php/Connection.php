<?php

require_once("mysql.properties.php");

class Connection {
	
	private static $pdoinstance;
	
	public static function getPDO() {
		
		if (Connection::$pdoinstance == null) {
			$params = new ConnectionParameters();
			try {
				$pdoinstance = new PDO($params->MYSQL_CONN_STRING, $params->MYSQL_USERNAME, $params->MYSQL_PASSWORD);
				$pdoinstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
				echo $e->getMessage()."<br>";
				echo ($e->getTraceAsString()."<br>");
				exit();
			}
			Connection::$pdoinstance = $pdoinstance;
		}
		return Connection::$pdoinstance;
		
	}
	
}

?>