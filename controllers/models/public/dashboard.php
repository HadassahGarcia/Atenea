<?php
session_start();

//Seguridad de login
if(!isset($_SESSION['usuario_id'])){
    header("Location: ../../../assets/charts/login.html");
    exit();
}

$nombreUsuario = $_SESSION['usuario_nombre'];
$rolUsuario = $_SESSION['usuario_rol'];

// Si el usuario es admin, redirigir al index
if($rolUsuario === 'admin'){
    header("Location: ../../../assets/charts/index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Atenea</title>
  <link rel="stylesheet" href="../../../assets/css/index.css">
</head>
<body>
  <header>
    <div class="logo">
      ATENEA
    </div>
    
    <nav>
      <button class="tab-btn active">Mis Préstamos</button>
    </nav>

    <div style="display: flex; align-items: center; gap: 15px;">
      <span style="color: white; font-weight: 500;">👤 <?php echo htmlspecialchars($nombreUsuario); ?></span>
      <a href="logout.php" style="background: #e74c3c; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: 500;">Cerrar Sesión</a>
    </div>
  </header>

  <div class="main-content">
    <div class="table-container">
      <h2 class="section-title">Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
      <p style="color: #666; margin-bottom: 20px;">Vista de usuario - Próximamente más funcionalidades</p>
      
      <div class="tab-content active">
        <p style="text-align: center; padding: 40px; color: #999;">
          📚 Aquí podrás ver tus préstamos activos y el historial de libros que has solicitado.
        </p>
      </div>
    </div>
  </div>
</body>
</html>
