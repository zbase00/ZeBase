<?php  
$config["db"]["hostname"] = "localhost";
$config["db"]["username"] = "root";
$config["db"]["password"] = "snaz01klo";
$config["db"]["database"] = "dvfish";
$config["db"]["db_debug"] = "TRUE";
$config["db"]["dbdriver"] = "mysql";
 
$_SESSION["hostname"] = $config["db"]["hostname"];
$_SESSION["db_username"] = $config["db"]["username"];
$_SESSION["db_password"] = $config["db"]["password"];
$_SESSION["database"] = $config["db"]["database"];
?> 