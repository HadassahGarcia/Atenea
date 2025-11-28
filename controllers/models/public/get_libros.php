<?php
header('Content-Type: application/json');

// Conexión
require_once "../config/database.php";

$stmt = $pdo->query("SELECT * FROM libros");
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver JSON
echo json_encode($libros);
