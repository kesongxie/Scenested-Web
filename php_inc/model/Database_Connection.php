<?php
	class Database_Connection{
		const HOSTNAME = "localhost";
		const USERNAME  = "higout_root";
		const PASSWORD = "woaini1314";
		const DATABASE_NAME = "higout_db";
		private $connection = null;
		
		public function __construct(){
			$mysqli = new mysqli(self::HOSTNAME,self::USERNAME,self::PASSWORD,self::DATABASE_NAME);
			if(!$mysqli->connect_errno){
				 $this->connection = $mysqli; 
			}
		}
		
		public function getConnection(){
			return $this->connection;
		}
	}	
?>