<?php
require_once '../phpOffice/vendor/autoload.php';
require_once '../config/Conexion.php'; // Verifica que la ruta sea correcta
require_once '../Atencion_Ciudadana/drive.php';

use PhpOffice\PhpWord\TemplateProcessor;

class ModeloSolicitud
{
    public static function procesarSolicitud($datos, $archivo)
    {
        // Inicializar el servicio de Google Drive
        $servicio = Drive::servicioGoogle();
        if (!$servicio['estado']) {
            return ["estado" => false, "error" => $servicio["error"]];
        }
        $servicioDrive = $servicio['dato'];

        // Gestionar carpetas en Google Drive por año y mes
        $resultadoCarpetas = Drive::gestionarCarpetasAnualYMensual(RAIZ, $servicioDrive);
        if (!$resultadoCarpetas["estado"]) {
            return ["estado" => false, "error" => $resultadoCarpetas["error"]];
        }
        $anioCarpetaId = $resultadoCarpetas["anioCarpetaId"];
        $mesCarpetaId = $resultadoCarpetas["mesCarpetaId"];

        // Ruta absoluta a la plantilla del documento Word
        $templatePath = realpath('C:\xampp\htdocs\AtencionCiudadanaTesis\public\document\Solicitud Cambio.docx');
        if (!$templatePath || !file_exists($templatePath)) {
            return ["estado" => false, "error" => 'Plantilla no encontrada o no accesible.'];
        }

        try {
            // Procesar plantilla Word
            $templateProcessor = new TemplateProcessor($templatePath);
            $templateProcessor->setValue('fecha', $datos['fecha']);
            $templateProcessor->setValue('nombres', $datos['nombre']);
            $templateProcessor->setValue('cedula', $datos['cedula']);
            $templateProcessor->setValue('carrera', $datos['carrera']);
            $templateProcessor->setValue('telefono', $datos['telefono']);
            $templateProcessor->setValue('celular', $datos['celular']);
            $templateProcessor->setValue('correo', $datos['correo']);
            $templateProcessor->setValue('asuntos', $datos['Tipo']);
            $templateProcessor->setValue('asunto', $datos['asuntoTexto']);

            // Crear documento temporal en memoria
            $tempFile = tempnam(sys_get_temp_dir(), 'Solicitud_') . '.docx';
            $templateProcessor->saveAs($tempFile);

            // Subir el documento a Google Drive
            $archivoDrive = new Google_Service_Drive_DriveFile();
            $archivoDrive->setName("{$datos['nombre']}_{$datos['carrera']}_{$datos['fecha']}.docx");
            $archivoDrive->setParents([$mesCarpetaId]);

            $fileContent = file_get_contents($tempFile);
            $resultadoSubida = $servicioDrive->files->create($archivoDrive, [
                'data' => $fileContent,
                'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);
            unlink($tempFile); // Eliminar archivo temporal

            // Establecer el archivo como público
            $fileId = $resultadoSubida->id;
            $permiso = new Google_Service_Drive_Permission();
            $permiso->setType('anyone');
            $permiso->setRole('reader');
            $servicioDrive->permissions->create($fileId, $permiso);

            // Subir archivo PDF de cédula
            $archivoCedula = $archivo['tmp_name'];
            $nombreCedula = "{$datos['cedula']}.pdf";

            $archivoCedulaDrive = new Google_Service_Drive_DriveFile();
            $archivoCedulaDrive->setName($nombreCedula);
            $archivoCedulaDrive->setParents([$mesCarpetaId]);

            $contenidoCedula = file_get_contents($archivoCedula);
            $resultadoCedulaSubida = $servicioDrive->files->create($archivoCedulaDrive, [
                'data' => $contenidoCedula,
                'mimeType' => 'application/pdf',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);

            // Establecer el archivo de cédula como público
            $fileCedulaId = $resultadoCedulaSubida->id;
            $permisoCedula = new Google_Service_Drive_Permission();
            $permisoCedula->setType('anyone');
            $permisoCedula->setRole('reader');
            $servicioDrive->permissions->create($fileCedulaId, $permisoCedula);

            // Guardar las IDs en la sesión, sobrescribiendo los valores anteriores
            $_SESSION['doc_ids'] = $resultadoSubida->id; // Sobrescribir con el último ID
            $_SESSION['cedula_ids'] = $resultadoCedulaSubida->id; // Sobrescribir con el último ID

            // Retornar las IDs de los archivos subidos
            return [
                "estado" => true,
                "doc_id" => $resultadoSubida->id,
                "cedula_id" => $resultadoCedulaSubida->id
            ];
        } catch (Exception $e) {
            return ["estado" => false, "error" => 'Error al procesar la solicitud: ' . $e->getMessage()];
        }
    }

    public static function procesarSolicitud1($datos, $archivoSolicitud, $archivoCedula)
    {
        // Inicializar el servicio de Google Drive
        $servicio = Drive::servicioGoogle();
        if (!$servicio['estado']) {
            return ["estado" => false, "error" => $servicio["error"]];
        }
        $servicioDrive = $servicio['dato'];

        // Obtener nombre y cédula
        $nombre = $datos['nombre'];
        $cedula = $datos['cedula'];

        // Gestionar carpetas en Google Drive por año y mes
        $resultadoCarpetas = Drive::gestionarCarpetasAnualYMensual(RAIZ, $servicioDrive);
        if (!$resultadoCarpetas["estado"]) {
            return ["estado" => false, "error" => $resultadoCarpetas["error"]];
        }
        $anioCarpetaId = $resultadoCarpetas["anioCarpetaId"];
        $mesCarpetaId = $resultadoCarpetas["mesCarpetaId"];

        try {
            // Subir el archivo de solicitud (PDF o Word)
            $archivoSolicitudTemp = $archivoSolicitud['tmp_name'];
            $extensionSolicitud = pathinfo($archivoSolicitud['name'], PATHINFO_EXTENSION);
            $nombreSolicitud = "{$nombre}_{$cedula}_Solicitud.{$extensionSolicitud}";

            $archivoSolicitudDrive = new Google_Service_Drive_DriveFile();
            $archivoSolicitudDrive->setName($nombreSolicitud);
            $archivoSolicitudDrive->setParents([$mesCarpetaId]);

            $contenidoSolicitud = file_get_contents($archivoSolicitudTemp);
            $resultadoSolicitudSubida = $servicioDrive->files->create($archivoSolicitudDrive, [
                'data' => $contenidoSolicitud,
                'mimeType' => $archivoSolicitud['type'],
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);

            // Establecer el archivo de solicitud como público
            $fileSolicitudId = $resultadoSolicitudSubida->id;
            $permisoSolicitud = new Google_Service_Drive_Permission();
            $permisoSolicitud->setType('anyone');
            $permisoSolicitud->setRole('reader');
            $servicioDrive->permissions->create($fileSolicitudId, $permisoSolicitud);

            // Subir archivo PDF de la cédula
            $archivoCedulaTemp = $archivoCedula['tmp_name'];
            $nombreCedula = "{$cedula}_Cedula.pdf";

            $archivoCedulaDrive = new Google_Service_Drive_DriveFile();
            $archivoCedulaDrive->setName($nombreCedula);
            $archivoCedulaDrive->setParents([$mesCarpetaId]);

            $contenidoCedula = file_get_contents($archivoCedulaTemp);
            $resultadoCedulaSubida = $servicioDrive->files->create($archivoCedulaDrive, [
                'data' => $contenidoCedula,
                'mimeType' => $archivoCedula['type'],
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);

            // Establecer el archivo de cédula como público
            $fileCedulaId = $resultadoCedulaSubida->id;
            $permisoCedula = new Google_Service_Drive_Permission();
            $permisoCedula->setType('anyone');
            $permisoCedula->setRole('reader');
            $servicioDrive->permissions->create($fileCedulaId, $permisoCedula);

            // Guardar las IDs en la sesión
            $_SESSION['doc_ids'] = $fileSolicitudId;
            $_SESSION['cedula_ids'] = $fileCedulaId;

            // Retornar las IDs de los archivos subidos
            return [
                "estado" => true,
                "doc_id" => $fileSolicitudId,
                "cedula_id" => $fileCedulaId
            ];
        } catch (Exception $e) {
            return ["estado" => false, "error" => 'Error al procesar la solicitud: ' . $e->getMessage()];
        }
    }


    public function estadoSolicitud($usu_id)
    {
        $op = 1;
        $sql = "CALL SP_GetSolicitudesEstId($op, $usu_id, NULL, NULL)";
        return ejecutarConsulta($sql);
    }

    public function obtIdSol($usu_id)
    {
        $op = 2;
        $sql = "CALL SP_GetSolicitudesEstId($op, $usu_id, NULL, NULL)";
        return ejecutarConsulta($sql);
    }

    public function obtIdSolDoc($sol_sol, $sol_doc)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $op = 3;
        $sql = "CALL SP_GetSolicitudesEstId($op, NULL, '$sol_sol', '$sol_doc')";
        $result = ejecutarConsulta($sql);

        if ($result && $row = $result->fetch_assoc()) {
            $_SESSION['sol_id'] = $row['sol_id']; // Guardar sol_id en sesión
            $_SESSION['usu_id'] = $row['est_id']; // Guardar est_id como usu_id en sesión

            return $row; // Retornar los datos obtenidos
        }

        return null; // Retornar null si no se encontró la solicitud
    }


    public function Estado()
    {
        $sql = "call atencion_ciudadana_ist17j.SP_MOSTRAR_SOLICITUDES()";
        return ejecutarConsulta($sql);
    }

    public function eliminarSolicitud($sol_solicitud, $sol_documento)
    {
        $sql = "call atencion_ciudadana_ist17j.SP_EliminarSolicitud('$sol_solicitud', '$sol_documento');";
        return ejecutarConsulta($sql);
    }

    public function cambiarEstadoSolicitud($sol_solicitud, $sol_documento, $idEstado)
    {
        $sql = "call atencion_ciudadana_ist17j.SP_ActualizarEstadoSolicitud('$sol_solicitud', '$sol_documento', '$idEstado');
        ";
        return ejecutarConsulta($sql);
    }

    public function insertSeguimiento($op, $sol_id, $est_id, $seg_accion, $seg_comentario, $seg_visto)
    {
        // Asegurarse de que el SQL esté bien formado
        $sql = "CALL atencion_ciudadana_ist17j.SP_Seguimiento('$op', '$sol_id', '$est_id', '$seg_accion', '$seg_comentario', '$seg_visto', NULL)";
        return ejecutarConsulta($sql);
    }

    public function mostrarSeguimiento($op, $sol_id)
    {
        // Asegurarse de que el SQL esté bien formado
        $sql = "CALL atencion_ciudadana_ist17j.SP_Seguimiento('$op', '$sol_id')";
        return ejecutarConsulta($sql);
    }

    public function obtenerSolSeg($op, $est_id)
    {
        $sql = "CALL atencion_ciudadana_ist17j.SP_GetSolicitudesEstId('$op', '$est_id', NULL, NULL)";
        return ejecutarConsulta($sql);
    }

    public function obteneSeg($op, $est_id, $sol_id)
    {
        $sql = "CALL atencion_ciudadana_ist17j.SP_Seguimiento('$op', '$sol_id','$est_id', NULL, NULL, NULL, NULL)";
        return ejecutarConsulta($sql);
    }

    public function obtenerSegId($op, $est_id)
    {
        $sql = "CALL atencion_ciudadana_ist17j.SP_Seguimiento($op, NULL, $est_id, NULL, NULL, NULL, NULL)";
        return ejecutarConsulta($sql);
    }

    public function cambiarVis($op, $seg_id)
    {
        $sql = "CALL atencion_ciudadana_ist17j.SP_Seguimiento($op, NULL, NULL, NULL, NULL, NULL, '$seg_id')";
        return ejecutarConsulta($sql);
    }

    public function obtSolId($sol_id, $est_id)
    {
        $op = 5;
        $sql = "CALL atencion_ciudadana_ist17j.SP_GetSolicitudesEstId('$op', '$est_id', '$sol_id', NULL)";
        return ejecutarConsulta($sql);
    }

    public function obtAsunto(){
        $op = 1;
        $sql = "call atencion_ciudadana_ist17j.SP_Asunto('$op', NULL, NULL)";
        return ejecutarConsulta($sql);
    }
}
