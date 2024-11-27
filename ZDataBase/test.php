<?php
require("app/Models/conexion.php");
require("app/Models/login.php");

$con = new conexion(); 

$login = new Login($con->conectar());
$login->create(1,1,"Jose","carlos","nieto","blanquicett","jose25@gmail.com","Jose");



$s = $login->lis();
print_r($s);
echo "Hola";
?>