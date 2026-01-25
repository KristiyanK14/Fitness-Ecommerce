<?php
$host= "localhost";
$db_name="spotterdb";
$username="Kris";
$password="kris14";
$con = new mysqli ($host, $username, $password, $db_name);  
if(mysqli_connect_error()) {  
   echo("Failed to connect with MySQL: ". mysqli_connect_error());  
 return $con;
}
?>
