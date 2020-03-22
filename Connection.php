<?php
 Class Connection
 {
     private $user ;
     private $host;
     private $pass ;
     private $db;
 
     public function __construct()
     {
         $this->user = "root";
         $this->host = "localhost";
         $this->pass = "";
         $this->db = "db_naivebayes";
     }
     public function connect()
     {
         return mysqli_connect($this->host, $this->user, $this->pass, $this->db);
     }
 }

?>