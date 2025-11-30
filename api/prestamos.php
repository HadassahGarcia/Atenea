<?php
// buscar error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

require_once '../config/database.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        if(isset($_GET['buscar'])){// Buscar prestamos por usuario o libro
            $buscar = '%' . $_GET['buscar'] . '%';
            $sql = "SELECT p.id, u.nombre AS usuario, l.titulo AS libro, p.fecha_prestamo,
                    p.fecha_devolucion_estimada AS fecha_devolucion, p.estado AS Estado, p.usuario_id, p.libro_id
                    FROM prestamos p
                    JOIN usuarios u ON p.usuario_id = u.id
                    JOIN libros l ON p.libro_id = l.id
                    WHERE u.nombre LIKE ? OR l.titulo LIKE ?
                    ORDER BY p.id DESC";
            $stmt = $pdo->prepare($sql);// Preparar la sentencia
            $stmt->execute([$buscar, $buscar]);// Ejecutar la sentencia
            echo json_encode($stmt->fetchAll());
        } else {
            $sql = "SELECT p.id, u.nombre AS usuario, l.titulo AS libro, p.fecha_prestamo,
                    p.fecha_devolucion_estimada AS fecha_devolucion, p.estado AS Estado, p.usuario_id, p.libro_id
                    FROM prestamos p
                    JOIN usuarios u ON p.usuario_id = u.id
                    JOIN libros l ON p.libro_id = l.id
                    ORDER BY p.id DESC";
            $stmt = $pdo->query($sql);
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        $datos = json_decode(file_get_contents("php://input"), true);

        if(!empty($datos['usuario_id']) && !empty($datos['libro_id'])){// Verificar que se tenga la informacion necesaria para crear el prestamo
            try{
                $fechaLimite = isset($datos['fecha_limite']) ? $datos['fecha_limite'] : date('Y-m-d', strtotime('+15 days'));

                $sql = "INSERT INTO prestamos (usuario_id, libro_id, fecha_prestamo, fecha_devolucion_estimada)
                        VALUES (?,?,CURDATE(),?)";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([$datos['usuario_id'],$datos['libro_id'], $fechaLimite]);// Ejecutar la sentencia para crear el prestamo

                $sqlUpdate = "UPDATE libros SET disponibles = disponibles - 1 WHERE id = ?";
                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([$datos['libro_id']]);

                http_response_code(201);// Prestamo creado exitosamente
                echo json_encode(["status" => "Prestamo registrado exitosamente📋"]);
            } catch (Exception $e) {
                http_response_code(500);// Error al crear el prestamo
                echo json_encode(["error" => "Error registrando prestamo ❌ " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);// Falta informacion para crear el prestamo
            echo json_encode(["error" => "Falta informacion de prestamo ❌"]);
        }
        break;

    case 'PUT':
        $datos = json_decode(file_get_contents("php://input"), true);

        if(isset($datos['id']) && isset($datos['usuario_id']) && isset($datos['libro_id'])){
            try{
                $fechaLimite = isset($datos['fecha_limite']) ? $datos['fecha_limite'] : null;
                $estado = isset($datos['estado']) ? $datos['estado'] : 'activo';
                
                $sql = "UPDATE prestamos SET usuario_id = ?, libro_id = ?, fecha_devolucion_estimada = ?, estado = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$datos['usuario_id'], $datos['libro_id'], $fechaLimite, $estado, $datos['id']]);

                http_response_code(200);// Prestamo actualizado exitosamente
                echo json_encode(["status" => "Prestamo modificado exitosamente✏️"]);
            } catch (Exception $e) {
                http_response_code(500);// Error al actualizar el prestamo
                echo json_encode(["error" => "Error al actualizar el prestamo ❌ " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);// Informacion incompleta para actualizar el prestamo
            echo json_encode(["error" => "Informacion incompleta para actualizar el prestamo ❌"]);
        }
        break;
    
    case 'DELETE':
        if(isset($_GET['id'])){
            try{
                $sql = "DELETE FROM prestamos WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_GET['id']]);

                http_response_code(200);// Prestamo eliminado exitosamente
                echo json_encode(["status" => "Prestamo eliminado exitosamente✏️"]);
            } catch (Exception $e) {
                http_response_code(500);// Error al eliminar el prestamo
                echo json_encode(["error" => "Error al eliminar el prestamo ❌ " . $e->getMessage()]);
            }
        } else{
            http_response_code(400);// Falta informacion para eliminar el prestamo
            echo json_encode(["error" => "Falta informacion para eliminar el prestamo ❌"]);
        }
        break;
}

?>
