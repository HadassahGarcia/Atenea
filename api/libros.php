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
            $stmt = $pdo->prepare("SELECT * FROM libros WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $resultado = $stmt->fetch();
        } else if(isset($_GET['buscar'])){
            $buscar = '%' . $_GET['buscar'] . '%';
            $stmt = $pdo->prepare("SELECT * FROM libros WHERE titulo LIKE ? OR autor LIKE ? OR isbn LIKE ?");
            $stmt->execute([$buscar, $buscar, $buscar]);
            $resultado = $stmt->fetchAll();
        } else {
            $stmt = $pdo->query("SELECT * FROM libros");
            $resultado = $stmt->fetchAll();
        }
        echo json_encode($resultado);
    }catch (Exception $e){
        http_response_code(500);
        echo json_encode(["error" => "Error en consulta: " . $e->getMessage()]);
    }
    break;
    case 'POST':
    $datos = json_decode(file_get_contents("php://input"), true);

    if(isset($datos['titulo']) && isset($datos['autor']) && isset($datos['cantidad']) && isset($datos['disponibles']) && isset($datos['estado'])){
        try{
            $isbn = isset($datos['isbn']) ? $datos['isbn'] : null;
            $imagen_url = isset($datos['imagen_url']) ? $datos['imagen_url'] : null;
            $sql = "INSERT INTO libros (titulo, autor, cantidad, disponibles, estado, isbn, imagen_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $datos['titulo'],
                $datos['autor'],
                $datos['cantidad'],
                $datos['disponibles'],
                $datos['estado'],
                $isbn,
                $imagen_url
            ]);

            http_response_code(201);
            echo json_encode(["status" => "Libro agregado correctamente"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "No se pudo crear: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
    }
    break;

    case 'PUT':
    $datos = json_decode(file_get_contents("php://input"), true);

    if(isset($datos['id']) && isset($datos['titulo']) && isset($datos['autor']) && isset($datos['cantidad']) && isset($datos['disponibles']) && isset($datos['estado'])){
        try{
            $isbn = isset($datos['isbn']) ? $datos['isbn'] : null;
            $imagen_url = isset($datos['imagen_url']) ? $datos['imagen_url'] : null;
            $sql = "UPDATE libros SET titulo = ?, autor = ?, cantidad = ?, disponibles = ?, estado = ?, isbn = ?, imagen_url = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $datos['titulo'],
                $datos['autor'],
                $datos['cantidad'],
                $datos['disponibles'],
                $datos['estado'],
                $isbn,
                $imagen_url,
                $datos['id']
            ]);

            http_response_code(200);
            echo json_encode(["status" => "Libro actualizado"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar el libro: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos para actualizar"]);
    }
    break;

    case 'DELETE':
    if(isset($_GET['id'])){
        try{
            $sql = "DELETE FROM libros WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_GET['id']]);

            http_response_code(200);
            echo json_encode(["status" => "Libro eliminado"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar el libro: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "ID no especificado"]);
    }
    break;

    default:
    http_response_code(404);
    echo json_encode(["error" => "Metodo no valido"]);
}
?>