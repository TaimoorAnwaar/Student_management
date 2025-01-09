<?php
$host = "localhost";
$username = 'root';
$password = null;
$dbname = 'students';

$conn = new mysqli($host, $username, $password, $dbname);

if (!$conn) {
   echo 'connection failed';


}



?>