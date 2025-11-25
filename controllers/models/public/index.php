<?php
// Conexión
require_once "../config/database.php";

// Consulta
$stmt = $pdo->query("SELECT * FROM libros");

// Obtener resultados
$libros = $stmt->fetchAll();
?>
<?php foreach ($libros as $libro): ?>
      <tr>
        <td>📘 <?= htmlspecialchars($libro["titulo"]) ?></td>
        <td><?= htmlspecialchars($libro["autor"]) ?></td>
        <td><?= $libro["disponibles"] > 0 ? "Disponible" : "Prestado" ?></td>
      </tr>
    <?php endforeach; ?>