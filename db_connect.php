<?php 
$DATABASE_HOST = "localhost";
$DATBASE_USER = "root";
$DATBASE_PASSWORD = "";
$DATBASE_NAME = "registerlogin";

$con = mysqli_connect($DATABASE_HOST,$DATBASE_USER,$DATBASE_PASSWORD,$DATBASE_NAME);
mysqli_set_charset($con,"utf-8");
if(mysqli_connect_errno()){
	die("Failed to connect to MYSQL, error :".mysqli_connect_error());
}
?>