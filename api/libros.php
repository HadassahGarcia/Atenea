<?php
// buscar error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//

// Obtener los datos de la base de datos
header("content-type: application/json; charset=UTF-8"); // Tipo de contenido
header("Access-Control-Allow-Origin:* ");// Permitir acceso
header("Access-Control-Allow-Methods: GET, POST, DELETE");// Permitir metodos para modificar

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

    if(!isset($datos['titulo']) && !empty($datos['autor'])){
        try{
            $sql = "INSERT INTO libros (titulo, autor, isbn, cantidad) VALUES (?, ?, ?, ?)";
            $stmt = $pdo-> prepare($sql);
            $stmt-> execute([datos['titulo'],$datos['autor'],$datos['isbn'] ?? 'N/A', $datos['cantidad'] ?? 1]);

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

    default:
    http_response_code(404); // No se encontro el metodo
    echo json_encode(["error" => "Metodo incorrecto❌, ingrese GET o POST por favor"]);
}
?>