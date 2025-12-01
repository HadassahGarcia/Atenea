<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../../../config/database.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // obtener datos de la peticion
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    //validmos que no esten vacios
    if(empty($email)||empty($password)){
        header("Location: ../../../assets/charts/login.html?error=vacio");
        exit();
    }

try{
    // obtener datos de la base de datos
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if($usuario && $usuario['password'] == $password){
        // se guarda el usuario en el servidor
        $_SESSION['usuario_id'] = $usuario['id'];// guardar id del usuario
        $_SESSION['usuario_nombre'] = $usuario['nombre'];// guardar nombre del usuario
        $_SESSION['usuario_rol'] = $usuario['rol'];// guardar rol del usuario

        header("Location: dashboard.php");
        exit();
    }else{
        header("Location: ../../../assets/charts/login.html?error=credenciales");
        exit();
    }

} catch(Exception $e){
    header("Location: ../../../assets/charts/login.html?error=servidor");
    exit();
}
}else{
    header("Location: ../../../assets/charts/login.html");
    exit();
}
?>