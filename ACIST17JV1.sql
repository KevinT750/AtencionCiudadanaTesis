-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: atencion_ciudadana_ist17j
-- ------------------------------------------------------
-- Server version	8.0.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `detalle_reporte`
--

DROP TABLE IF EXISTS `detalle_reporte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_reporte` (
  `detalle_id` int NOT NULL AUTO_INCREMENT,
  `reporte_id` int NOT NULL COMMENT 'ID del reporte mensual',
  `tipo_solicitud` varchar(100) NOT NULL COMMENT 'Tipo de solicitud (e.g., Cambio de Carrera, Homologación)',
  `total_recibidas` int DEFAULT '0' COMMENT 'Número total de solicitudes recibidas por tipo',
  `total_aprobadas` int DEFAULT '0' COMMENT 'Número total de solicitudes aprobadas por tipo',
  `total_rechazadas` int DEFAULT '0' COMMENT 'Número total de solicitudes rechazadas por tipo',
  `porcentaje_aprobacion` decimal(5,2) DEFAULT '0.00' COMMENT 'Porcentaje de solicitudes aprobadas por tipo',
  PRIMARY KEY (`detalle_id`),
  KEY `reporte_id` (`reporte_id`),
  CONSTRAINT `detalle_reporte_ibfk_1` FOREIGN KEY (`reporte_id`) REFERENCES `reporte_mensual` (`reporte_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_reporte`
--

LOCK TABLES `detalle_reporte` WRITE;
/*!40000 ALTER TABLE `detalle_reporte` DISABLE KEYS */;
/*!40000 ALTER TABLE `detalle_reporte` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_solicitud`
--

DROP TABLE IF EXISTS `estado_solicitud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_solicitud` (
  `estado_id` int NOT NULL AUTO_INCREMENT,
  `estado_nombre` varchar(45) NOT NULL COMMENT 'Ejemplo: Enviado, Leído, Aceptado, Rechazado',
  PRIMARY KEY (`estado_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_solicitud`
--

LOCK TABLES `estado_solicitud` WRITE;
/*!40000 ALTER TABLE `estado_solicitud` DISABLE KEYS */;
INSERT INTO `estado_solicitud` VALUES (1,'Enviado'),(2,'Leído'),(3,'Aceptado'),(4,'Rechazado'),(5,'No Leído'),(6,'Documentos subidos');
/*!40000 ALTER TABLE `estado_solicitud` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reporte_mensual`
--

DROP TABLE IF EXISTS `reporte_mensual`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reporte_mensual` (
  `reporte_id` int NOT NULL AUTO_INCREMENT,
  `reporte_mes` int NOT NULL COMMENT 'Mes del reporte (1-12)',
  `reporte_anio` int NOT NULL COMMENT 'Año del reporte',
  `total_recibidas` int DEFAULT '0' COMMENT 'Total de solicitudes recibidas en el mes',
  `total_aprobadas` int DEFAULT '0' COMMENT 'Total de solicitudes aprobadas en el mes',
  `total_rechazadas` int DEFAULT '0' COMMENT 'Total de solicitudes rechazadas en el mes',
  PRIMARY KEY (`reporte_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reporte_mensual`
--

LOCK TABLES `reporte_mensual` WRITE;
/*!40000 ALTER TABLE `reporte_mensual` DISABLE KEYS */;
/*!40000 ALTER TABLE `reporte_mensual` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seguimiento`
--

DROP TABLE IF EXISTS `seguimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguimiento` (
  `seg_id` int NOT NULL AUTO_INCREMENT,
  `sol_id` int NOT NULL,
  `est_id` int DEFAULT NULL COMMENT 'Usuario que realiza el seguimiento',
  `seg_fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `seg_accion` varchar(255) NOT NULL COMMENT 'Descripción de la acción realizada',
  `seg_comentario` text,
  `seg_visto` tinyint DEFAULT '0' COMMENT '0: No visto, 1: Visto',
  PRIMARY KEY (`seg_id`),
  KEY `sol_id` (`sol_id`),
  KEY `est_id` (`est_id`),
  CONSTRAINT `seguimiento_ibfk_1` FOREIGN KEY (`sol_id`) REFERENCES `solicitudes` (`sol_id`) ON DELETE CASCADE,
  CONSTRAINT `seguimiento_ibfk_2` FOREIGN KEY (`est_id`) REFERENCES `solicitudes` (`est_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguimiento`
--

LOCK TABLES `seguimiento` WRITE;
/*!40000 ALTER TABLE `seguimiento` DISABLE KEYS */;
INSERT INTO `seguimiento` VALUES (4,163,NULL,'2025-02-02 19:52:06','Solicitud Enviada','Su solicitud ha sido enviada correctamente. Debe esperar a que un responsable revise su solicitud para ser aprobada o rechazada. Manténgase atento.',0),(5,164,146,'2025-02-02 19:58:30','Solicitud Enviada','Su solicitud ha sido enviada correctamente. Debe esperar a que un responsable revise su solicitud para ser aprobada o rechazada. Manténgase atento.',0);
/*!40000 ALTER TABLE `seguimiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitudes`
--

DROP TABLE IF EXISTS `solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitudes` (
  `sol_id` int NOT NULL AUTO_INCREMENT,
  `est_id` int NOT NULL COMMENT 'Referencia al estudiante de la base de datos estudiantes_ist17j',
  `sol_fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `sol_solicitud` varchar(255) DEFAULT NULL,
  `sol_documento` varchar(255) DEFAULT NULL,
  `estado_id` int DEFAULT '1' COMMENT 'Estado inicial: 1 (Enviado)',
  PRIMARY KEY (`sol_id`),
  KEY `est_id` (`est_id`),
  KEY `estado_id` (`estado_id`),
  KEY `idx_sol_solicitud_documento` (`sol_solicitud`,`sol_documento`),
  CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`est_id`) REFERENCES `estudiantes_ist17j`.`estudiante` (`est_id`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_ibfk_2` FOREIGN KEY (`estado_id`) REFERENCES `estado_solicitud` (`estado_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitudes`
--

LOCK TABLES `solicitudes` WRITE;
/*!40000 ALTER TABLE `solicitudes` DISABLE KEYS */;
INSERT INTO `solicitudes` VALUES (163,146,'2025-02-02 19:52:06','1tTh6vPsUPJw-YPUdgV7VqjyEd5qMRcTl','1cFC-SSMlZzGBl_isoFkxdRvzmYLKEh7P',5),(164,146,'2025-02-02 19:58:30','1DmUBS6pA6zsdnORqRg0FqKRW68ouX0Pu','1hc82ZM64xjaCvWFr0S457VIZ7JNehQCw',5);
/*!40000 ALTER TABLE `solicitudes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `usuario_id` int NOT NULL AUTO_INCREMENT,
  `usuario_nombre` varchar(150) NOT NULL,
  `usuario_correo` varchar(150) NOT NULL,
  `usuario_telefono` varchar(10) DEFAULT NULL,
  `usuario_rol` varchar(50) NOT NULL COMMENT 'Ejemplo: secretaria, coordinador',
  `usuario_login` varchar(150) NOT NULL,
  `usuario_clave` varchar(150) NOT NULL,
  PRIMARY KEY (`usuario_id`),
  UNIQUE KEY `usuario_correo` (`usuario_correo`),
  UNIQUE KEY `usuario_login` (`usuario_login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Kevin','steveenteran@gmail.com','0998035014','Secretaria','admin','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'atencion_ciudadana_ist17j'
--

--
-- Dumping routines for database 'atencion_ciudadana_ist17j'
--
/*!50003 DROP PROCEDURE IF EXISTS `obtener_datos_estudiante` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtener_datos_estudiante`(
    IN p_sol_solicitud VARCHAR(255),
    IN p_sol_documento VARCHAR(255)
)
BEGIN
    -- Realiza una consulta a la tabla solicitudes y a la tabla estudiante
    SELECT 
        e.est_cedula,
        e.est_nombre,
        e.est_fechaNacimiento,
        e.est_lugarNacimiento,
        e.est_correoPersonal,
        e.est_correoInstitucional,
        e.est_telefono,
        e.est_celular,
        e.est_direccion
    FROM 
        atencion_ciudadana_ist17j.solicitudes s
    JOIN 
        estudiantes_ist17j.estudiante e ON s.est_id = e.est_id
    WHERE 
        s.sol_solicitud = p_sol_solicitud 
        AND s.sol_documento = p_sol_documento;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_ActualizarEstadoSolicitud` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_ActualizarEstadoSolicitud`(
    IN p_sol_solicitud VARCHAR(512),
    IN p_sol_documento VARCHAR(512),
    IN p_nuevo_estado_id INT
)
BEGIN
    -- Verificar si existe un registro con los valores proporcionados
    IF EXISTS (SELECT 1 FROM solicitudes WHERE sol_solicitud = p_sol_solicitud AND sol_documento = p_sol_documento) THEN
        -- Actualizar el estado_id si se encuentran los valores
        UPDATE solicitudes
        SET estado_id = p_nuevo_estado_id
        WHERE sol_solicitud = p_sol_solicitud AND sol_documento = p_sol_documento;
    ELSE
        -- Si no se encuentran los valores, devolver un mensaje o manejar el caso
        SELECT 'No se encontraron registros con los valores proporcionados.' AS mensaje;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_BUSCAR_ESTUDIANTE` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_BUSCAR_ESTUDIANTE`(
    IN p_tipo_busqueda VARCHAR(10), -- Puede ser 'cedula' o 'nombre'
    IN p_valor_busqueda VARCHAR(200) -- El valor parcial de la cédula o nombre a buscar
)
BEGIN
    IF p_tipo_busqueda = 'cedula' THEN
        -- Búsqueda parcial por cédula en la base de datos estudiantes_ist17j
        SELECT est_id, est_nombre, est_cedula
        FROM estudiantes_ist17j.estudiante
        WHERE est_cedula LIKE CONCAT('%', p_valor_busqueda, '%');
    ELSEIF p_tipo_busqueda = 'nombre' THEN
        -- Búsqueda parcial por nombre en la base de datos estudiantes_ist17j
        SELECT est_id, est_nombre, est_cedula
        FROM estudiantes_ist17j.estudiante
        WHERE est_nombre LIKE CONCAT('%', p_valor_busqueda, '%');
    ELSE
        -- Si el tipo de búsqueda no es válido
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tipo de búsqueda no válido. Use "cedula" o "nombre".';
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_EliminarSolicitud` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_EliminarSolicitud`(
    IN p_sol_solicitud VARCHAR(255),
    IN p_sol_documento VARCHAR(255)
)
BEGIN
    -- Verifica si existe una solicitud con los parámetros proporcionados
    IF EXISTS (
        SELECT 1
        FROM solicitudes
        WHERE sol_solicitud = p_sol_solicitud AND sol_documento = p_sol_documento
    ) THEN
        -- Elimina la solicitud
        DELETE FROM solicitudes
        WHERE sol_solicitud = p_sol_solicitud AND sol_documento = p_sol_documento;

        SELECT 'Solicitud eliminada correctamente' AS mensaje;
    ELSE
        SELECT 'No se encontró ninguna solicitud con los datos proporcionados' AS mensaje;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_GetSolicitudes` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_GetSolicitudes`()
BEGIN
    SELECT 
        s.sol_id,
        s.sol_fecha,
        s.sol_solicitud,
        s.sol_documento,
        es.estado_nombre
    FROM 
        solicitudes s
    INNER JOIN 
        estado_solicitud es ON s.estado_id = es.estado_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_GetSolicitudesEstId` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_GetSolicitudesEstId`(
    IN p_est_id INT
)
BEGIN
    SELECT 
        s.sol_id,
        s.sol_fecha,
        s.sol_solicitud,
        s.sol_documento,
        es.estado_nombre
    FROM 
        solicitudes s
    INNER JOIN 
        estado_solicitud es ON s.estado_id = es.estado_id
    WHERE 
        s.est_id = p_est_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_InsertarSolicitud` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_InsertarSolicitud`(
    IN p_est_id INT,
    IN p_sol_solicitud VARCHAR(255),
    IN p_sol_documento VARCHAR(255),
    IN p_estado_id INT, -- Parámetro adicional para el estado
    OUT p_sol_id INT -- Parámetro de salida para devolver la ID de la solicitud
)
BEGIN
    -- Insertar la nueva solicitud
    INSERT INTO `solicitudes` (
        `est_id`,
        `sol_solicitud`,
        `sol_documento`,
        `estado_id`
    ) 
    VALUES (
        p_est_id,
        p_sol_solicitud,
        p_sol_documento,
        p_estado_id -- Se utiliza el parámetro proporcionado para el estado
    );

    -- Obtener la última ID insertada y asignarla al parámetro de salida
    SET p_sol_id = LAST_INSERT_ID();
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_Login` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_Login`(
    IN p_login VARCHAR(150),
    IN p_password VARCHAR(64)
)
BEGIN
    -- Verificar en la tabla de estudiantes
    SELECT 
        'Estudiante' AS Rol,
        est_id AS UsuarioID,
        est_nombre AS Nombre,
        est_correoInstitucional AS Correo
    FROM estudiantes_ist17j.estudiante
    WHERE est_login = p_login 
      AND est_clave = p_password  -- Comparar directamente la contraseña
      AND est_condicion = 1 -- Opcional, si deseas verificar que el estudiante esté activo

    UNION

    -- Verificar en la tabla de usuarios administrativos
    SELECT 
        usuario_rol AS Rol,
        usuario_id AS UsuarioID,
        usuario_nombre AS Nombre,
        usuario_correo AS Correo
    FROM atencion_ciudadana_ist17j.usuarios
    WHERE usuario_login = p_login 
      AND usuario_clave = p_password;  -- Comparar directamente la contraseña
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_MOSTRAR_SOLICITUDES` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MOSTRAR_SOLICITUDES`()
BEGIN
    SELECT 
        s.sol_id,
        s.sol_fecha,
        s.sol_solicitud,
        s.sol_documento,
        es.estado_nombre AS estado_solicitud,  -- Obtiene el nombre del estado
        e.est_nombre AS nombre_estudiante,
        e.est_correoPersonal AS correo_personal,
        e.est_correoInstitucional AS correo_institucional,
        e.est_celular AS celular
    FROM solicitudes s
    INNER JOIN estudiantes_ist17j.estudiante e
        ON s.est_id = e.est_id
    INNER JOIN estado_solicitud es
        ON s.estado_id = es.estado_id;  -- Se une con la tabla estado_solicitud para obtener el nombre del estado
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_Seguimiento` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_Seguimiento`(
    IN op INT, 
    IN p_sol_id INT, 
    IN p_est_id INT, 
    IN p_seg_accion VARCHAR(255), 
    IN p_seg_comentario TEXT, 
    IN p_seg_visto TINYINT(1)
)
BEGIN
    IF op = 1 THEN
        -- Seleccionar todos los registros de seguimiento para un sol_id dado
        SELECT * FROM seguimiento WHERE sol_id = p_sol_id;
    
    ELSEIF op = 2 THEN
        -- Insertar un nuevo registro en seguimiento
        INSERT INTO seguimiento (sol_id, est_id, seg_accion, seg_comentario, seg_visto)
        VALUES (p_sol_id, p_est_id, p_seg_accion, p_seg_comentario, p_seg_visto);

        -- Mostrar los registros de seguimiento para el sol_id insertado
        SELECT * FROM seguimiento WHERE sol_id = p_sol_id;
    
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-02 20:08:20
