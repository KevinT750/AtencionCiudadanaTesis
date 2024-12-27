<?php
require_once "../config/Conexion.php";
require_once '../phpOffice/vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

class Usuario {
    public function __construct() {}

    public function verificar($usu_login, $usu_clave) {
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

    public function solicitud($idEstudiante, $isSolicitud, $idPdf){
        try {
            // Validar que todos los parámetros estén presentes
            if (empty($idEstudiante) || empty($isSolicitud) || empty($idPdf)) {
                return ["estado" => false, "error" => "Faltan parámetros necesarios"];
            }
    
            // Ejecutar el procedimiento almacenado para insertar la solicitud
            $sql = "CALL atencion_ciudadana_ist17j.SP_InsertarSolicitud('$idEstudiante', '$isSolicitud', '$idPdf')";
            $rspta = ejecutarConsulta($sql);
    
            // Verificar si la consulta fue exitosa
            if ($rspta) {
                return ["estado" => true, "mensaje" => "Solicitud procesada correctamente"];
            } else {
                return ["estado" => false, "error" => "Error al procesar la solicitud"];
            }
    
        } catch (Exception $e) {
            error_log("Error en solicitud: " . $e->getMessage());
            return ["estado" => false, "error" => "Error inesperado: " . $e->getMessage()];
        }
    }
    

    private function establecerPermisos($rol) {

        $_SESSION['Escritorio'] = 0;
        $_SESSION['Descargar'] = 0;
        $_SESSION['Solicitud'] = 0;
        $_SESSION['Atencion'] = 0;
        $_SESSION['Estado'] = 0;
        $_SESSION['Historial'] = 0;
        $_SESSION['Aprobadas'] = 0;
        $_SESSION['Ver_Solicitudes'] = 0;
        $_SESSION['Subir_Solicitud'] = 0;
        $_SESSION['Gestion'] = 0;
        $_SESSION['Reporte'] = 0;

        $_SESSION['Escritorio'] = 1;
    
        // Asignar permisos según el rol
        switch(strtoupper($rol)) {
            case 'ESTUDIANTE':
                $_SESSION['Descargar'] = 1;
                $_SESSION['Solicitud'] = 1;
                $_SESSION['Atencion'] = 1;
                $_SESSION['Estado'] = 1;
                $_SESSION['Historial'] = 1;
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

    public function descargarSolicitud($file) {
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

    public function generarDocumentoSolicitud($nombres, $cedula, $carrera, $telefono_domicilio, $celular, $correo, $fecha, $asunto) {
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

    

}
?>
