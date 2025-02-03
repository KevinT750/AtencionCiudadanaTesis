<?php
require_once "../config/Conexion.php";
require_once '../phpOffice/vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

class Usuario
{
    public function __construct() {}

    public function verificar($usu_login, $usu_clave)
    {
        try {
            if (empty($usu_login) || empty($usu_clave)) {
                return false;
            }

            $sql = "call atencion_ciudadana_ist17j.SP_Login('$usu_login', '$usu_clave')";
            $rspta = ejecutarConsulta($sql);

            if ($rspta && $rspta->num_rows > 0) {

                $usuario = $rspta->fetch_object();

                $_SESSION['usu_id'] = $usuario->UsuarioID;
                $_SESSION['usu_nombre'] = $usuario->Nombre;
                $_SESSION['usu_login'] = $usu_login;

                if ($usuario->Rol == 'Estudiante') {
                    $_SESSION['Rol'] = 'Estudiante';
                } else {
                    $_SESSION['Rol'] = 'Secretaria';
                }

                $this->establecerPermisos($usuario->Rol);

                return $usuario;
            }

            return false;
        } catch (Exception $e) {
            error_log("Error en verificación: " . $e->getMessage());
            return false;
        }
    }

    public function solicitud($idEstudiante, $isSolicitud, $idPdf, $estadoId, $titulo)
    {
        try {
            // Iniciar la sesión si no está iniciada
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (empty($idEstudiante) || empty($isSolicitud) || empty($idPdf) || empty($estadoId)) {
                return ["estado" => false, "error" => "Faltan parámetros necesarios"];
            }

            // Ejecutar el procedimiento almacenado para insertar la solicitud
            $sql = "CALL atencion_ciudadana_ist17j.SP_InsertarSolicitud('$idEstudiante', '$isSolicitud', '$idPdf', '$estadoId', '$titulo', @p_sol_id)";
            $sqlGetId = "SELECT @p_sol_id AS sol_id";

            // Ejecutar las consultas
            $sol_id = ejecutarConsulta_retornarIDs($sql, $sqlGetId);

            // Verificar si se obtuvo sol_id
            if ($sol_id) {
                // Guardar sol_id en la sesión
                $_SESSION['sol_id'] = $sol_id;

                return ["estado" => true, "sol_id" => $sol_id]; // Retornar el sol_id
            } else {
                return ["estado" => false, "error" => "Error al obtener la ID de la solicitud"];
            }
        } catch (Exception $e) {
            error_log("Error en solicitud: " . $e->getMessage());
            return ["estado" => false, "error" => "Error inesperado: " . $e->getMessage()];
        }
    }


    private function establecerPermisos($rol)
    {

        $_SESSION['Escritorio'] = 0;
        $_SESSION['Descargar'] = 0;
        $_SESSION['Solicitud'] = 0;
        $_SESSION['Atencion'] = 0;
        $_SESSION['Estado'] = 0;
        $_SESSION['Seguimiento'] = 0;
        $_SESSION['Aprobadas'] = 0;
        $_SESSION['Ver_Solicitudes'] = 0;
        $_SESSION['Subir_Solicitud'] = 0;
        $_SESSION['Gestion'] = 0;
        $_SESSION['Reporte'] = 0;

        $_SESSION['Escritorio'] = 1;

        // Asignar permisos según el rol
        switch (strtoupper($rol)) {
            case 'ESTUDIANTE':
                $_SESSION['Descargar'] = 1;
                $_SESSION['Solicitud'] = 1;
                $_SESSION['Atencion'] = 1;
                $_SESSION['Estado'] = 1;
                $_SESSION['Seguimiento'] = 1;
                $_SESSION['Aprobadas'] = 1;
                $_SESSION['Estudiante'] = 1;  // Activar permisos para Estudiante
                break;

            case 'SECRETARIA':
                $_SESSION['Ver_Solicitudes'] = 1;
                $_SESSION['Subir_Solicitud'] = 1;
                $_SESSION['Gestion'] = 1;
                $_SESSION['Reporte'] = 1;
                $_SESSION['Secretaria'] = 1;  // Activar permisos para Secretaria
                break;
        }
    }

    public function descargarSolicitud($file)
    {
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit();
        } else {
            die('El archivo no existe.');
        }
    }

    public function generarDocumentoSolicitud($nombres, $cedula, $carrera, $telefono_domicilio, $celular, $correo, $fecha, $asunto)
    {
        try {
            // Ruta al documento base
            $templatePath = '../public/document/Solicitud Cambio.docx';
            $outputPath = 'ruta/a/Solicitud_Completada.docx';

            // Crear una instancia de TemplateProcessor
            $templateProcessor = new TemplateProcessor($templatePath);

            // Rellenar los campos del documento
            $templateProcessor->setValue('fecha', $fecha); // Fecha actual
            $templateProcessor->setValue('dirigido', 'Msc. Pedro Arias');
            $templateProcessor->setValue('nombres', $nombres);
            $templateProcessor->setValue('cedula', $cedula);
            $templateProcessor->setValue('carrera', $carrera);
            $templateProcessor->setValue('telefono_domicilio', $telefono_domicilio);
            $templateProcessor->setValue('celular', $celular);
            $templateProcessor->setValue('correo', $correo);
            $templateProcessor->setValue('asunto', $asunto);

            $templateProcessor->saveAs($outputPath);

            echo "Documento generado correctamente en: $outputPath";
        } catch (Exception $e) {
            error_log("Error al generar el documento: " . $e->getMessage());
        }
    }

    public function datosEst($sol_solicitud, $sol_documento)
    {
        $sql = "call atencion_ciudadana_ist17j.obtener_datos_estudiante('$sol_solicitud', '$sol_documento');";
        $resultado = ejecutarConsulta($sql);

        // Verifica si hay resultados
        if ($resultado && $resultado->num_rows > 0) {
            // Recupera el primer registro como un array asociativo
            return $resultado->fetch_assoc(); // Esto te devuelve los datos como un array
        }

        return null; // Retorna null si no se encuentra el estudiante
    }

    public function idEstudiante($tipo, $valor)
    {
        $sql = "call atencion_ciudadana_ist17j.SP_BUSCAR_ESTUDIANTE('$tipo', '$valor');";
        $resultado = ejecutarConsulta($sql);

        // Verifica si hay resultados
        if ($resultado && $resultado->num_rows > 0) {
            $data = array();
            while ($reg = $resultado->fetch_assoc()) {
                $data[] = array(
                    'id' => $reg['est_id'],
                    'nombre' => isset($reg['est_nombre']) ? $reg['est_nombre'] : 'No disponible', // Valor predeterminado
                    'cedula' => isset($reg['est_cedula']) ? $reg['est_cedula'] : 'No disponible'  // Valor predeterminado
                );
            }
            return $data; // Devuelve todos los registros como un arreglo
        }

        return []; // Retorna un arreglo vacío si no hay resultados
    }
}
