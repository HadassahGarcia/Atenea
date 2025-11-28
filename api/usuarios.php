<?php
//buscar error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//

header("content-type: application/json; charset=UTF-8"); // Tipo de contenido
header("Access-Control-Allow-Origin:* ");// Permitir acceso
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");// Permitir metodos para modificar

require_once '../config/database.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        if (isset($_GET['buscar'])){// Buscar usuarios por nombre o email
            $buscar = '%' . $_GET['buscar'] . '%';
            $sql = "SELECT * FROM usuarios WHERE nombre LIKE ? OR email LIKE ?";
            $stmt = $pdo->query($sql);
            $stmt->execute([$buscar, $buscar]);// Ejecutar la sentencia
            echo json_encode($stmt->fetchAll());
        } else {
            $sql= "SELECT id, nombre, email, rol, fecha_registro FROM usuarios";
            $stmt = $pdo->query($sql); // Ejecutar la sentencia
            echo json_encode($stmt->fetchAll()); // Obtener todos los datos
        }
        break;

    case 'POST':
        $datos = json_decode(file_get_contents("php://input"), true);

        // verificar que los datos sean correctos y no esten vacios
        if (!empty($datos['nombre']) && !empty($datos['email']) && !empty($datos['password'])) {
            
            try {
                $rol = isset($datos['rol']) ? $datos['rol'] : 'usuario';
                $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$datos['nombre'], $datos['email'], $datos['password'], $rol]);
                
                http_response_code(201);
                echo json_encode(["status" => "Usuario creado exitosamente✅"]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Error al crear el usuario ❌ " . $e->getMessage()]);

            } catch (Exception $e) {
                // en caso de que el correo ya exista
                echo json_encode(["error" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Datos incompletos ❌"]);
        }
        break;
    
    case 'PUT':
        $datos = json_decode(file_get_contents("php://input"), true);

        if(isset($datos['id']) && !empty($datos['nombre']) && !empty($datos['email'])){// Verificar que se tenga la informacion necesaria para actualizar el usuario
            try{
                $rol = isset($datos['rol']) ? $datos['rol'] : 'usuario';
                if(isset($datos['password']) && !empty($datos['password'])){// Verificar si se actualiza la contraseña
                    $sql = "UPDATE usuarios SET nombre = ?, email = ?, password = ?, rol = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$datos['nombre'], $datos['email'], $datos['password'], $rol, $datos['id']]);
                } else {
                    $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$datos['nombre'], $datos['email'], $rol, $datos['id']]);
                }

                http_response_code(200);// Usuario actualizado exitosamente
                echo json_encode(["status" => "Usuario actualizado exitosamente✅"]);
            }catch (Exception $e){
                http_response_code(500);// Error al actualizar el usuario
                echo json_encode(["error" => "Error al actualizar el usuario ❌ " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);// Faltan datos para actualizar el usuario
            echo json_encode(["error" => "Faltan datos para actualizar el usuario ❌"]);
        }
        break;
    
    case 'DELETE':
        if(isset($_GET['id'])){
            try{
                $sql = "DELETE FROM usuarios WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_GET['id']]);

                http_response_code(200);// Usuario eliminado exitosamente
                echo json_encode(["status" => "Usuario eliminado exitosamente✅"]);
            }catch (Exception $e){
                http_response_code(500);// Error al eliminar el usuario
                echo json_encode(["error" => "Error al eliminar el usuario ❌ " . $e->getMessage()]);
            }
        }else{
            http_response_code(400);// Falta el ID para eliminar el usuario
            echo json_encode(["error" => "Falta el ID para eliminar el usuario ❌"]);
        }
        break;
}
?>