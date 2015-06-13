<?php
	class Database_Connection{
		private $hostname = "localhost";
		private $username = "root";
		private $password = "root";
		private $database_name = "lsere_db";
		private $connection = null;
		
		public function __construct(){
			$mysqli = new mysqli("localhost","root","root","lsere_db");
			if(!$mysqli->connect_errno){
				 $this->connection = $mysqli; 
			}
		}
		
		public function getConnection(){
			return $this->connection;
		}
	}	
?>
