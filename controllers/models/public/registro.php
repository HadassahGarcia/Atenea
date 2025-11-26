<?php
// Incluir el archivo de conexión. Usamos __DIR__ y dirname() para resolver la ruta absoluta 
// y garantizar que la ruta sea correcta sin importar cómo se ejecute Apache.
// __DIR__ es '/Atenea/controllers/models/public'
// dirname(__DIR__) es '/Atenea/controllers/models'
// dirname(dirname(__DIR__)) es '/Atenea/controllers'
// dirname(dirname(dirname(__DIR__))) es '/Atenea' (Raíz del proyecto)
require_once(dirname(dirname(dirname(__DIR__))) . '/config/database.php'); 

if (isset($_POST['registrar'])) {
    // 1. Recoger y sanitizar los datos del formulario
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    // Almacenamos la contraseña directamente sin hashear.
    $password_texto_plano = $_POST['password']; 

    // 2. Validación simple
    if (empty($nombre) || empty($email) || empty($password_texto_plano)) {
        die("Error: Todos los campos son obligatorios.");
    }

    // 3. La contraseña para almacenar es el texto plano
    $password_a_guardar = $password_texto_plano;
    
    // El rol por defecto para un nuevo registro es 'usuario'.
    $rol = 'usuario'; 

    // 4. Preparar la consulta SQL con PDO para prevenir inyección SQL
    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        // Ejecutamos la sentencia con el valor de la contraseña en texto plano
        $stmt->execute([$nombre, $email, $password_a_guardar, $rol]);

        // 5. Registro exitoso
        header("Location: /Atenea/assets/charts/login.html?registro=exitoso");
        exit();

    } catch (\PDOException $e) {
        // 6. Manejar errores de la base de datos (Ej: email duplicado)
        if ($e->getCode() === '23000') {
            die("Error 23000: El correo electrónico ya está registrado. Por favor, utiliza otro.");
        } else {
            // Error de base de datos - Muestra un error más amigable en la vida real.
            die("Error al registrar usuario: " . $e->getMessage());
        }
    }

} else {
    // Si se accede al archivo sin enviar el formulario (GET), redirige al formulario de registro
    header("Location: /Atenea/assets/charts/registro.html");
    exit();
}
?>