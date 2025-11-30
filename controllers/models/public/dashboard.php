<?php
session_start();

//Seguridad de login
if(!isset($_SESSION['usuario_id'])){
    header("Location: login.html");
    exit();
}
$nombreUsuario = $_SESSION['usuario_nombre'];
$rolUsuario = $_SESSION['usuario_rol'];

?>