<?php
require_once(dirname(dirname(dirname(__DIR__))) . '/config/database.php'); 

if (isset($_POST['registrar'])) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password_texto_plano = $_POST['password']; // "texto plano" porque la contraseña no fue echa con hash, simplemente va a comparar los caracteres que el usuario proporcione

    if (empty($nombre) || empty($email) || empty($password_texto_plano)) {
        die("Error: Todos los campos son obligatorios.");
    
    $password_a_guardar = $password_texto_plano;
    $rol = 'usuario'; 
    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $email, $password_a_guardar, $rol]);

        // registro exitoso
        header("Location: /Atenea/assets/charts/login.html?registro=exitoso");
        exit();

    } catch (\PDOException $e) {
        if ($e->getCode() === '23000') {
            die("Error 23000: El correo electrónico ya está registrado. Por favor, utiliza otro.");
        } else {
            die("Error al registrar usuario: " . $e->getMessage());
        }
    }

} else {
    // redirige al registro para que pueda iniciar sesion, pero en cuanto pongan index redirigira a eso 
    header("Location: /Atenea/assets/charts/registro.html");
    exit();
}
?>