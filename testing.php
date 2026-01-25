<?php

session_start();


$postcode = $_GET['postcode'] ?? $_SESSION['postcode'] ?? '';
$phone = $_GET['phone'] ?? $_SESSION['phone'] ?? '';
$address = $_GET['address'] ?? $_SESSION['address'] ?? '';



var_dump($_SESSION['postcode']);
?>