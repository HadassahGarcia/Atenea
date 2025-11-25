<?php

$host = 'localhost:8888';
$db = 'biblioteca_db';
$user = 'root';
$pass = 'root';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;port=8888;charset=$charset";

$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // da con exactitud el error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Evita la devolucion de datos duplicados
    PDO::ATTR_EMULATE_PREPARES => false, // Es una opcion mas segura
);
//obtiene el usuario directo de la base de datos y busca si existe
try{
    $pdo = new PDO($dsn, $user, $pass, $options);
//evitar que el programa se quede esperando
} catch (\PDOexception $e) {
    throw new \PDOexception($e->getMessage(), (int)$e->getCode());
}
?>