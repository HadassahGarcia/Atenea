<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin:* ");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

require_once '../config/database.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        try {
            if(isset($_GET['id'])){
                $stmt = $pdo->prepare("SELECT * FROM autores WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $resultado = $stmt->fetch();
            } else if(isset($_GET['buscar'])){
                $buscar = '%' . $_GET['buscar'] . '%';
                $stmt = $pdo->prepare("SELECT * FROM autores WHERE nombre LIKE ? OR nacionalidad LIKE ?");
                $stmt->execute([$buscar, $buscar]);
                $resultado = $stmt->fetchAll();
            } else {
                $stmt = $pdo->query("SELECT * FROM autores");
                $resultado = $stmt->fetchAll();
            }
            echo json_encode($resultado);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error consultando: " . $e->getMessage()]);
        }
        break;

    case 'POST':
        $datos = json_decode(file_get_contents("php://input"), true);

        if(isset($datos['nombre'])){
            try{
                $nacionalidad = isset($datos['nacionalidad']) ? $datos['nacionalidad'] : null;
                $fecha_nacimiento = isset($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null;
                $sql = "INSERT INTO autores (nombre, nacionalidad, fecha_nacimiento) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$datos['nombre'], $nacionalidad, $fecha_nacimiento]);

                http_response_code(201);
                echo json_encode(["status" => "Autor agregado"]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Error al crear el autor: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos"]);
        }
        break;

    case 'PUT':
        $datos = json_decode(file_get_contents("php://input"), true);

        if(isset($datos['id']) && isset($datos['nombre'])){
            try{
                $nacionalidad = isset($datos['nacionalidad']) ? $datos['nacionalidad'] : null;
                $fecha_nacimiento = isset($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null;
                $sql = "UPDATE autores SET nombre = ?, nacionalidad = ?, fecha_nacimiento = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$datos['nombre'], $nacionalidad, $fecha_nacimiento, $datos['id']]);

                http_response_code(200);
                echo json_encode(["status" => "Autor modificado"]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Error al actualizar el autor: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos para actualizar el autor"]);
        }
        break;

    case 'DELETE':
        if(isset($_GET['id'])){
            try{
                $sql = "DELETE FROM autores WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_GET['id']]);

                http_response_code(200);
                echo json_encode(["status" => "Autor eliminado"]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Error al eliminar el autor: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Falta el ID"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Metodo no permitido"]);
}
?>

