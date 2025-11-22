<?php
// Obtener los datos de la base de datos
header("content-type: application/json; charset=UTF-8"); // Tipo de contenido
header("Access-Control-Allow-Origin:* ");// Permitir acceso
header("Access-Control-Allow-Methods: GET, POST, DELETE");// Permitir metodos para modificar

// Mandar la peticion a la base de datos
require_once '/config/database.php';

//Buscar el metodo HTTP que se solicita
$metodo = $_SERVER['REQUEST_METHOD'];

// Crear switch para buscar el metodo
switch ($metodo){
    case 'GET';
    // mandar a llamar los libros
    try {
        if(isset($_GET['id'])){
            $stmt = $pdo->prepare("SELECT * FROM libros WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $resultado = $stmt->fetch();// Obtener los datos solicitados
        } else {
            // si no se consigue toda la info de los libros
            $stmt = $pdo->query("SELECT *FROM libros");
            $resultado = $stmt->fetchAll(); // Obtener todos los datos
        }
        //Devolver archivo en formato json
        echo json_encode($resultado);
    }catch (Exception $e){
        http_responnse_code(500);
        echo json_encode(["error" => "Error al consultar: " . $e->getMessage()]);
    }
    break;
}
