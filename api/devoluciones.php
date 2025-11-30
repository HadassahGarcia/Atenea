<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

require_once '../config/database.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        if(isset($_GET['buscar'])){
            $buscar = '%' . $_GET['buscar'] . '%';
            $sql = "SELECT p.id, u.nombre AS usuario, l.titulo AS libro, p.fecha_prestamo,
                    p.fecha_devolucion_estimada, p.fecha_devolucion_real, p.estado
                    FROM prestamos p
                    JOIN usuarios u ON p.usuario_id = u.id
                    JOIN libros l ON p.libro_id = l.id
                    WHERE p.estado = 'devuelto' AND (u.nombre LIKE ? OR l.titulo LIKE ?)
                    ORDER BY p.fecha_devolucion_real DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$buscar, $buscar]);
            echo json_encode($stmt->fetchAll());
        } else {
            $sql = "SELECT p.id, u.nombre AS usuario, l.titulo AS libro, p.fecha_prestamo,
                    p.fecha_devolucion_estimada, p.fecha_devolucion_real, p.estado
                    FROM prestamos p
                    JOIN usuarios u ON p.usuario_id = u.id
                    JOIN libros l ON p.libro_id = l.id
                    WHERE p.estado = 'devuelto'
                    ORDER BY p.fecha_devolucion_real DESC";
            $stmt = $pdo->query($sql);
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        $datos = json_decode(file_get_contents("php://input"), true);

        if(!empty($datos['prestamo_id'])){
            try{
                $sql = "UPDATE prestamos SET estado = 'devuelto', fecha_devolucion_real = CURDATE() WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$datos['prestamo_id']]);

                $sqlLibro = "SELECT libro_id FROM prestamos WHERE id = ?";
                $stmtLibro = $pdo->prepare($sqlLibro);
                $stmtLibro->execute([$datos['prestamo_id']]);
                $prestamo = $stmtLibro->fetch();

                if($prestamo){
                    $sqlUpdate = "UPDATE libros SET disponibles = disponibles + 1 WHERE id = ?";
                    $stmtUpdate = $pdo->prepare($sqlUpdate);
                    $stmtUpdate->execute([$prestamo['libro_id']]);
                }

                http_response_code(200);
                echo json_encode(["status" => "Devolucion realizada"]);
            } catch (Exception $e){
                http_response_code(500);
                echo json_encode(["error" => "Error al registrar la devolucion: " . $e->getMessage()]);
            }
        }else {
            http_response_code(400);
            echo json_encode(["error" => "Falta informacion de devolucion"]);
        }
        break;
}

?>

