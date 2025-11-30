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
