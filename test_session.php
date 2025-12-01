<?php
session_start();

echo "<h2>Test de Sesión</h2>";

if(isset($_SESSION['usuario_id'])){
    echo "<p style='color: green;'>✓ Sesión activa</p>";
    echo "<ul>";
    echo "<li><strong>ID:</strong> " . $_SESSION['usuario_id'] . "</li>";
    echo "<li><strong>Nombre:</strong> " . $_SESSION['usuario_nombre'] . "</li>";
    echo "<li><strong>Rol:</strong> " . $_SESSION['usuario_rol'] . "</li>";
    echo "</ul>";
    
    if($_SESSION['usuario_rol'] === 'admin'){
        echo "<p>Deberías poder acceder a: <a href='/assets/charts/index.php'>index.php</a></p>";
    } else {
        echo "<p>Eres usuario normal, deberías ver: <a href='/controllers/models/public/dashboard.php'>dashboard.php</a></p>";
    }
} else {
    echo "<p style='color: red;'>✗ No hay sesión activa</p>";
    echo "<p>Debes iniciar sesión en: <a href='/assets/charts/login.html'>login.html</a></p>";
}

echo "<hr>";
echo "<p><a href='/controllers/models/public/logout.php'>Cerrar sesión</a></p>";
?>
