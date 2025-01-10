<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require_once '../model/solicitud.php';

$solicitud = new ModeloSolicitud();

// Verificar si el parámetro 'op' está presente en la URL
if (isset($_GET['op'])) {
    $op = $_GET['op'];

    switch ($op) {
        case 'estado':
            // Lógica para manejar la solicitud de estado
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datos = $_POST;
                $archivo = $_FILES['archivo'];

                // Validar tamaño del archivo
                if ($archivo['size'] > 2 * 1024 * 1024) {
                    echo json_encode([
                        'estado' => false,
                        'error' => 'El archivo excede el tamaño máximo permitido de 2 MB.'
                    ]);
                    exit();
                }

                // Procesar solicitud
                $resultado = ModeloSolicitud::procesarSolicitud($datos, $archivo);

                if ($resultado['estado']) {
                    // Responder con éxito
                    echo json_encode([
                        'estado' => true,
                        'mensaje' => 'Solicitud enviada correctamente.',
                        'doc_id' => $resultado['doc_id'],
                        'cedula_id' => $resultado['cedula_id']
                    ]);
                } else {
                    // Responder con error
                    echo json_encode([
                        'estado' => false,
                        'error' => 'Error al enviar la solicitud: ' . $resultado['error']
                    ]);
                }
            }
            break;

        case 'Solicitudes':
            $rspta = $solicitud->Estado();
            $date[] = array();

            if($rspta !== false){
                while($reg = $rspta->fetch_row()){
                    $data[] = array(
                        "0" => $reg[1], //Fecha y Hora
                        "1" => strip_tags(html_entity_decode($reg[5])), //Nombre del Estudiante
                        "2" => strip_tags(html_entity_decode($reg[6])), //Correo Persssonal
                        "3" => strip_tags(html_entity_decode($reg[7])), //Correo Institucional
                        "4" => $reg[8], // Numero de Celular
                        "5" => strip_tags(html_entity_decode($reg[2])),
                        "6" => strip_tags(html_entity_decode($reg[3])),
                        "7" => $reg[4] //Estado
                    );

                    
                }
                // Devuelve los datos en formato JSON para DataTables
                echo json_encode(array(
                    "sEcho" => 1, // Eco de la solicitud
                    "iTotalRecords" => count($data), // Total de registros encontrados
                    "iTotalDisplayRecords" => count($data), // Total de registros mostrados
                    "aaData" => $data // Los datos procesados
                ));

            }else{
                // En caso de error, responde con un mensaje adecuado
                echo json_encode(array(
                    "error" => "No se pudieron obtener los datos. Verifique la consulta o el procedimiento almacenado."
                ));
            }

            break;

        case 'Eliminar':
                // Verificar si los parámetros necesarios están presentes
            if (isset($_POST['sol_solicitud']) && isset($_POST['sol_documento'])) {
                $sol_solicitud = $_POST['sol_solicitud'];
                $sol_documento = $_POST['sol_documento'];
                                    // Llamar a la función eliminarSolicitud
                $rspta = $solicitud->eliminarSolicitud($sol_solicitud, $sol_documento);
            
                    // Verificar la respuesta y retornar el resultado al cliente
                echo $rspta ? "Solicitud eliminada correctamente" : "No se pudo eliminar la solicitud.";
                } else {
                    echo "Faltan parámetros para eliminar la solicitud.";
                }
            break;
            

        // Agregar otros casos si es necesario
        default:
            echo json_encode([
                'estado' => false,
                'error' => 'Operación no válida.'
            ]);
            break;
    }
} else {
    echo json_encode([
        'estado' => false,
        'error' => 'No se especificó la operación.'
    ]);
}
?>
