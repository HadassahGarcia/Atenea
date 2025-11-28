<?php
// buscar error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//

// Obtener los datos de la base de datos
header("content-type: application/json; charset=UTF-8"); // Tipo de contenido
header("Access-Control-Allow-Origin:* ");// Permitir acceso
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");// Permitir metodos para modificar

// Mandar la peticion a la base de datos
require_once '../config/database.php';

//Buscar el metodo HTTP que se solicita
$metodo = $_SERVER['REQUEST_METHOD'];

// Crear switch para buscar el metodo
switch ($metodo){
    case 'GET';
    // mandar a llamar los libros
    try {
        if(isset($_GET['id'])){
            $stmt = $pdo->prepare("SELECT * FROM libros WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $resultado = $stmt->fetch();// Obtener los datos solicitados
        }else if (isset($_GET['buscar'])){
            $buscar = '%'. $_GET['buscar'] . '%';
            $stmt = $pdo->prepare("SELECT * FROM libros WHERE titulo LIKE ? OR autor LIKE ? OR isbn LIKE ?");//buscar los datos en la tabla de libros
            $stmt->execute([$buscar, $buscar, $buscar]);// Buscar los datos
            $resultado = $stmt->fetchALL(); // Obtener todos los datos
        } else {
            // si no se consigue toda la info de los libros
            $stmt = $pdo->query("SELECT *FROM libros");
            $resultado = $stmt->fetchAll(); // Obtener todos los datos
        }
        //Devolver archivo en formato json
        echo json_encode($resultado);
    }catch (Exception $e){
        http_response_code(500);// Enviar error al cliente
        echo json_encode(["error" => "Error al consultar: " . $e->getMessage()]);// error enviado en formato json
    }
    break;
    case 'POST':
    // Crear un libro o agregar un nuevo libro
    $datos = json_decode(file_get_contents("php://input"), true);

    if(isset($datos['titulo']) && isset($datos['autor']) && isset($datos['cantidad']) && isset($datos['disponibles']) && isset($datos['estado'])){
        try{
            $isbn = isset($datos['isbn']) ? $datos['isbn'] : null// Obtener el isbn(el isbn es opcional)
            $imagen_url = isset($datos['imagen_url']) ? $datos ['imagen_url']: null; // Obtener la imagen url
            $sql = "INSERT INTO libros (titulo, autor, cantidad, disponibles, estado, isbn, imagen_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo-> prepare($sql);
            $stmt-> execute([
                $datos['titulo'],
                $datos['autor'],
                $datos['cantidad'],
                $datos['disponibles'],
                $datos['estado'],
                $isbn,
                $imagen_url
            ]);

            http_response_code(201); //Alerta de que el libro fue creado
            echo json_encode(["status" => "Libro creado con exito⭐"]); // Respuesta enviada en formato json
        }catch (Exception $e){
            http_response_code(500);
            echo json_encode(["error" => "hubo un error al crear el libro❌:" . $e-> getMessage()]); // Respuesta del fallo en formato json
        }
    } else{
        http_response_code(400); // No se pudo crear el libro
        echo json_encode(["error" => "No se pudo crear el libro❌"]);
    }
    break;

    case 'PUT':
    // Actualizar un libro
    $datos = json_decode(file_get_contents("php://input"), true);
    if(isset($datos['id']) && isset($datos['titulo']) && isset($datos['autor']) && isset($datos['cantidad']) && isset($datos['disponibles']) && isset($datos['estado'])){
        try{
            $isbn = isset($datos['isbn']) ? $datos['isbn'] : null;
            $imagen_url = isset($datos['imagen_url']) ? $datos['imagen_url'] : null;
            $sql = "UPDATE libros SET titulo = ?, autor = ?, cantidad = ?, disponibles = ?, estado = ?, isbn = ?, imagen_url = ? WHERE id = ?";//Actualizar los datos del libro
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $datos['titulo'],
                $datos['autor'],
                $datos['cantidad'],
                $datos['disponibles'],
                $datos['estado'],
                $isbn,
                $imagen_url,
                $datos['id']
            ]);
            http_response_code(200); // Actualizar el libro
            echo json_encode(["status" => "Libro actualizado con exito⭐"]); // Respuesta enviada en formato json
        }catch (Exception $e){
            http_response_code(500);
            echo json_encode(["error" => "hubo un error al actualizar el libro❌:" . $e-> getMessage()]); // Respuesta del fallo en formato json
        }
    } else{
        http_response_code(400); // No se pudo actualizar el libro
        echo json_encode(["error" => "No se pudo actualizar el libro, datos incompletos❌"]);
    }
    break;
    // Eliminar un libro
    case 'DELETE':
    if(isset($_GET['id'])){
        try{
            $sql = "DELETE FROM libros WHERE id = ?";// Eliminar el libro desde la base de datos
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_GET['id']]);

            http_response_code(200);
            echo json_encode(["status" => "Libro eliminado con exito⭐"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar el libro❌:" . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "ID no especificado para eliminar el libro❌"]);
    }
    break;

    default:
    http_response_code(404); // No se encontro el metodo
    echo json_encode(["error" => "Metodo incorrecto❌, ingrese el correcto por favor"]);
}
?>