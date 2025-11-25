<?php
// buscar error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

require_once '../config/database.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        $sql = "SELECT p.id, u.nombre AS usuario, l.titulo AS libro, p.fecha_prestamo,
                p.fecha_devolucion_estimada AS fecha_devolucion, p.estado AS Estado
                FROM prestamos p
                JOIN usuarios u ON p.usuario_id = u.id # unir tablas usuariss con prestamos
                JOIN libros l ON p.libro_id = l.id # unir tablas libros con prestamos
                ORDER BY p.id DESC"; // ordenar por id de forma descendente
        
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        $datos = json_decode(file_get_contents("php://input"), true);

        if(!empty($datos['usuario_id']) && !empty($datos['libro_id'])){
            try{
                $fechaLimite = isset($datos['fecha_limite']) ? $datos['fecha_limite'] : '2024-12-31'; // fecha por defecto es para ejemplificar

                $sql = "INSERT INTO prestamos (usuario_id, libro_id, fecha_prestamo, fecha_devolucion_estimada)
                        VALUES (?,?,CURDATE(),?)"; // fecha de devolucion

                $stmt = $pdo->prepare($sql);
                $stmt->execute([$datos['usuario_id'],$datos['libro_id'], $fechaLimite]); // Ejecutar la sentencia para insertar

                echo json_encode(["Mensaje" => "Préstamo registrado📋"]);
            } catch (Exception $e){
                echo json_encode(["error" => "Error al registrar el préstamo❌: " . $e->getMessage()]); // Error al registrar el prestamo
            }
        }else {
            echo json_encode(["error" => "Falta información de préstamo❌"]);// Error en el momento de registrar el prestamo
        }
        break;
}

?>
