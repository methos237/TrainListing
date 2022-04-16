<?php

namespace ComponentSystem\Util\Database;

require_once "$_SERVER[DOCUMENT_ROOT]/db.conf.php";

use PDO;
use PDOException;

class DatabaseManager {
	
	private static $connection;
	
	private function __construct() {}
	
	public static function connect() {
		if (!empty(self::$connection)) {
			return self::$connection;
		}
		
		try {
			$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_SCHEMA.";charset=UTF8", DB_USER, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));

			self::$connection = $conn;

			return $conn;
			
		} catch (PDOException $e) {
			print "Database Connection Error : {$e->getMessage()}\n";
			die();
		}
	}
	
}