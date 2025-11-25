<?php
//buscar error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//

header("content-type: application/json; charset=UTF-8"); // Tipo de contenido
header("Access-Control-Allow-Origin:* ");// Permitir acceso
header("Access-Control-Allow-Methods: GET, POST, DELETE");// Permitir metodos para modificar

require_once '../config/database.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        $sql = "SELECT * FROM usuarios";
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        $datos = json_decode(file_get_contents("php://input"), true);

        // verificar que los datos sean correctos y no esten vacios
        if (!empty($datos['nombre']) && !empty($datos['email']) && !empty($datos['password'])) {
            
            try {
                $rol = isset($datos['rol']) ? $datos['rol'] : 'usuario';
                $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                
                // Pasamos los datos directos
                $stmt->execute([ $datos['nombre'], $datos['email'], $datos['password'],$rol]);
                echo json_encode(["mensaje" => "Usuario creado correctamente"]);

            } catch (Exception $e) {
                // en caso de que el correo ya exista
                echo json_encode(["error" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["error" => "Falta informacion de usuario"]);
        }
        break;
}
?>