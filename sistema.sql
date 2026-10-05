-- =====================================================================
-- Sistema Institucional UPTP-LMR - PNFI
-- Departamento de Informatica
-- =====================================================================
-- ARCHIVO UNICO DE ESQUEMA DE BASE DE DATOS
-- Cualquier cambio (creacion, modificacion, eliminacion de tablas,
-- columnas, indices, constraints, seeds) debe registrarse en este archivo.
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistema`
--

-- =====================================================================
-- HISTORIAL DE VERSIONES
-- =====================================================================
--   v1.0  - Esquema inicial: tablas usuario e historial (auth basico).
--   v1.1  - Refactor seguridad: campo telefono pasa de INT a VARCHAR(20)
--           para soportar formato 04XX-XXXXXXX. Agregada columna
--           estado=2 al estado de usuario (deshabilitado explicito).
--   v1.2  - Nucleo academico (Fase 1):
--             * nivel (4 niveles fijos)
--             * periodo_academico
--             * seccion + estudiante_seccion
--             * unidad + unidad_archivo
--             * progreso_unidad
--   v1.3  - Modulos del estudiante (Fase 2):
--             * dinamica + pregunta_dinamica + opcion_respuesta
--             * intento_dinamica + respuesta_intento
--             * escenario + asignacion_escenario
--             * entrega_escenario + evaluacion_escenario
--   v1.4  - Fix: PRIMARY KEY de tablas v1.0 (usuario, historial) agregadas
--           en la seccion de indices. Era necesario para que AUTO_INCREMENT
--           funcionara en MySQL (error #1075).
--   v1.5  - Seed: usuarios admin/docente/estudiante con hashes bcrypt
--           (reemplaza el admin con MD5 heredado).
--   v1.6  - Seeds del banco pedagogico:
--             * 8 escenarios hipoteticos (2 por nivel)
--             * 24 dinamicas interactivas (6 por nivel, 2 preguntas c/u,
--               4 opciones por pregunta, con retroalimentacion).
--   v1.7  - Materia: nueva tabla materia (id, nombre) y FK id_materia
--           en seccion. CRUD admin.
--   v1.8  - docente_materia: tabla puente N:M entre usuarios con rol 2
--           (docentes) y materia. Un docente puede impartir varias
--           materias; una materia puede ser impartida por varios docentes.
--   v1.9  - Seccion - Materia N:M: nueva tabla puente seccion_materia.
--           Una seccion puede tener 1 o mas materias impartidas por el
--           mismo docente. Se elimina la columna seccion.id_materia.
--           El estudiante inscrito en una seccion ve las materias de ella.
--   v2.0  - Actividades (escenarios de evaluacion) por materia:
--             * actividad + actividad_parte (partes con seccion introductoria
--               y evaluacion con contador de tiempo)
--             * actividad_parte_pregunta + actividad_parte_opcion (opcion multiple)
--             * seccion_actividad (acceso: actividad asignada a 1+ secciones)
--             * actividad_entrega + actividad_parte_respuesta (progreso del
--               estudiante, parte por parte, con calificacion automatica)
--           Reemplaza el modelo de niveles/unidades/dinamicas/escenarios.
--   v2.1  - Seccion: cada materia de la seccion tiene su propio docente.
--           seccion_materia.id_docente indica quien imparte esa materia en la
--           seccion. Se elimina seccion.id_docente (el docente ya no imparte
--           todas las materias de la seccion).
--   v2.2  - Seeds de prueba del modelo actual (Actividades).
--   v2.3  - Seeds de prueba de Unidades (Gestor de Contenido).
--   v2.4  - Materia -> Unidad -> Actividad: unidad.id_materia (admin crea
--           unidades por materia) y actividad.id_unidad (obligatoria). El
--           docente solo puede crear actividades dentro de una unidad
--           creada por el administrador.
--   v2.5  - Fix asignacion_escenario: se agrega UNIQUE KEY
--           uk_ae_esc_est_sec (id_escenario, id_estudiante, id_seccion) para
--           dar soporte al ON DUPLICATE KEY del formulario de asignacion
--           (evita duplicar la misma asignacion). Nota: la columna id_periodo
--           no existe en la tabla; el periodo se obtiene via seccion.id_periodo.
-- =====================================================================

-- =====================================================================
-- v1.0 - ESQUEMA INICIAL
-- =====================================================================

-- Tabla: usuario
-- Roles: 1=Administrador, 2=Docente, 3=Estudiante
-- clave: hash de password_hash() (migracion automatica desde MD5 al login)
-- 1	admin@admin.com	admin123	Administrador
-- 2	docente@admin.com	docente123	Docente
-- 3	estudiante@admin.com	estudiante123	Estudiante
CREATE TABLE `usuario` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `correo` varchar(100) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `telefono` int NOT NULL,
  `clave` varchar(300) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `rol` int NOT NULL,
  `estado` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: historial (auditoria)
CREATE TABLE `historial` (
  `id` int NOT NULL,
  `accion` varchar(200) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `fecha` date NOT NULL,
  `id_usuario` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- =====================================================================
-- v1.1 - REFACTOR SEGURIDAD
-- =====================================================================

-- telefono pasa de INT a VARCHAR(20) y permite NULL
ALTER TABLE `usuario`
  MODIFY COLUMN `telefono` VARCHAR(20) NULL;

-- Indice unico por correo (evita duplicados al crear usuarios)
ALTER TABLE `usuario`
  ADD UNIQUE KEY `uk_usuario_correo` (`correo`);

-- =====================================================================
-- v1.2 - NUCLEO ACADEMICO (Fase 1)
-- =====================================================================

-- Tabla: nivel
-- 4 niveles fijos con desbloqueo progresivo por completitud
CREATE TABLE `nivel` (
  `id` int NOT NULL,
  `numero` tinyint NOT NULL,
  `nombre` varchar(80) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_spanish2_ci NULL,
  `porcentaje_desbloqueo` tinyint NOT NULL DEFAULT 80,
  `estado` tinyint NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: periodo_academico
CREATE TABLE `periodo_academico` (
  `id` int NOT NULL,
  `nombre` varchar(80) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` tinyint NOT NULL DEFAULT 1 COMMENT '1=activo, 2=cerrado',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: materia
-- Catalogo de materias que se imparten en el PNFI
CREATE TABLE `materia` (
  `id` int NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: docente_materia
-- Relacion N:M entre usuarios con rol=2 y materia
CREATE TABLE `docente_materia` (
  `id` int NOT NULL,
  `id_docente` int NOT NULL,
  `id_materia` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: seccion
CREATE TABLE `seccion` (
  `id` int NOT NULL,
  `nombre` varchar(80) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `id_periodo` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: seccion_materia (N:M)
-- Una seccion puede tener 1 o mas materias. Cada materia de la seccion es
-- impartida por el docente indicado en id_docente (pueden ser distintos).
-- El estudiante de la seccion accede a estas materias.
CREATE TABLE `seccion_materia` (
  `id` int NOT NULL,
  `id_seccion` int NOT NULL,
  `id_materia` int NOT NULL,
  `id_docente` int NOT NULL COMMENT 'Docente que imparte la materia en esa seccion',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- =====================================================================
-- v2.0 - ACTIVIDADES (escenarios de evaluacion)
-- =====================================================================

-- Tabla: actividad
-- Escenario de evaluacion creado por el docente para una materia.
-- id_nivel es opcional y solo sirve para agrupar/ordenar (1-4).
CREATE TABLE `actividad` (
  `id` int NOT NULL,
  `id_materia` int NOT NULL,
  `id_unidad` int NOT NULL,
  `titulo` varchar(150) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_spanish2_ci NULL,
  `id_nivel` int NULL,
  `estado` tinyint NOT NULL DEFAULT 1 COMMENT '1=publicada, 2=borrador',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: seccion_actividad (N:M)
-- Secciones de estudiantes que tendran acceso a la actividad.
CREATE TABLE `seccion_actividad` (
  `id` int NOT NULL,
  `id_actividad` int NOT NULL,
  `id_seccion` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: actividad_parte
-- Cada parte tiene una seccion introductoria (contenido_html) y una
-- evaluacion (preguntas) con contador de tiempo (tiempo_limite_min).
CREATE TABLE `actividad_parte` (
  `id` int NOT NULL,
  `id_actividad` int NOT NULL,
  `orden` int NOT NULL DEFAULT 1,
  `titulo` varchar(150) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `contenido_html` longtext COLLATE utf8mb4_spanish2_ci NULL,
  `tiempo_limite_min` int NULL COMMENT 'minutos del contador en la evaluacion',
  `estado` tinyint NOT NULL DEFAULT 1 COMMENT '1=activa, 2=inactiva',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: actividad_parte_pregunta
-- Preguntas de opcion multiple de la evaluacion de una parte.
CREATE TABLE `actividad_parte_pregunta` (
  `id` int NOT NULL,
  `id_parte` int NOT NULL,
  `enunciado` text COLLATE utf8mb4_spanish2_ci NOT NULL,
  `orden` int NOT NULL DEFAULT 1,
  `puntos` int NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: actividad_parte_opcion
-- Opciones de respuesta de cada pregunta.
CREATE TABLE `actividad_parte_opcion` (
  `id` int NOT NULL,
  `id_pregunta` int NOT NULL,
  `texto` text COLLATE utf8mb4_spanish2_ci NOT NULL,
  `es_correcta` tinyint NOT NULL DEFAULT 0,
  `orden` int NOT NULL DEFAULT 1,
  `retroalimentacion` text COLLATE utf8mb4_spanish2_ci NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: actividad_entrega
-- Entrega del estudiante sobre una actividad. estado: 1=en progreso, 2=completada.
CREATE TABLE `actividad_entrega` (
  `id` int NOT NULL,
  `id_actividad` int NOT NULL,
  `id_estudiante` int NOT NULL,
  `estado` tinyint NOT NULL DEFAULT 1,
  `calificacion` decimal(5,2) NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: actividad_parte_respuesta
-- Resultado de la evaluacion de cada parte. Bloquea el avance secuencial.
CREATE TABLE `actividad_parte_respuesta` (
  `id` int NOT NULL,
  `id_entrega` int NOT NULL,
  `id_parte` int NOT NULL,
  `puntaje` decimal(5,2) NULL COMMENT 'porcentaje 0-100 de la parte',
  `tiempo_usado_seg` int NULL,
  `respondida` tinyint NOT NULL DEFAULT 0,
  `inicio_at` datetime NULL DEFAULT NULL COMMENT 'inicio del temporizador de la parte',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: estudiante_seccion (N:M)
CREATE TABLE `estudiante_seccion` (
  `id` int NOT NULL,
  `id_estudiante` int NOT NULL,
  `id_seccion` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: unidad
CREATE TABLE `unidad` (
  `id` int NOT NULL,
  `titulo` varchar(150) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_spanish2_ci NULL,
  `id_materia` int NOT NULL,
  `id_nivel` int NOT NULL,
  `orden` int NOT NULL DEFAULT 1,
  `estado` tinyint NOT NULL DEFAULT 1 COMMENT '1=publicado, 2=borrador',
  `contenido_html` longtext COLLATE utf8mb4_spanish2_ci NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: unidad_archivo
CREATE TABLE `unidad_archivo` (
  `id` int NOT NULL,
  `id_unidad` int NOT NULL,
  `tipo` enum('mp4','pdf','pptx','html') COLLATE utf8mb4_spanish2_ci NOT NULL,
  `ruta` varchar(500) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `nombre_original` varchar(255) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `tamano_bytes` int NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: progreso_unidad
CREATE TABLE `progreso_unidad` (
  `id` int NOT NULL,
  `id_estudiante` int NOT NULL,
  `id_unidad` int NOT NULL,
  `completada` tinyint NOT NULL DEFAULT 1,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- =====================================================================
-- v1.3 - MODULOS DEL ESTUDIANTE (Fase 2)
-- =====================================================================

-- Tabla: dinamica
-- Banco precargado de dinamicas interactivas (24 total, 6 por nivel)
-- tipo: 1=opcion multiple, 2=verdadero/falso
CREATE TABLE `dinamica` (
  `id` int NOT NULL,
  `titulo` varchar(150) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_spanish2_ci NULL,
  `id_nivel` int NOT NULL,
  `tipo` tinyint NOT NULL DEFAULT 1,
  `estado` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: pregunta_dinamica
CREATE TABLE `pregunta_dinamica` (
  `id` int NOT NULL,
  `id_dinamica` int NOT NULL,
  `enunciado` text COLLATE utf8mb4_spanish2_ci NOT NULL,
  `orden` int NOT NULL DEFAULT 1,
  `puntos` int NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: opcion_respuesta
-- es_correcta: 1=correcta, 0=incorrecta
-- retroalimentacion: mensaje que se muestra al seleccionar esta opcion
CREATE TABLE `opcion_respuesta` (
  `id` int NOT NULL,
  `id_pregunta` int NOT NULL,
  `texto` text COLLATE utf8mb4_spanish2_ci NOT NULL,
  `es_correcta` tinyint NOT NULL DEFAULT 0,
  `orden` int NOT NULL DEFAULT 1,
  `retroalimentacion` text COLLATE utf8mb4_spanish2_ci NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: intento_dinamica
-- Un intento del estudiante sobre una dinamica
-- calificacion: porcentaje 0-100
CREATE TABLE `intento_dinamica` (
  `id` int NOT NULL,
  `id_estudiante` int NOT NULL,
  `id_dinamica` int NOT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` datetime NULL,
  `calificacion` decimal(5,2) NULL,
  `total_preguntas` int NOT NULL DEFAULT 0,
  `total_correctas` int NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: respuesta_intento
-- Respuesta del estudiante a una pregunta en un intento
CREATE TABLE `respuesta_intento` (
  `id` int NOT NULL,
  `id_intento` int NOT NULL,
  `id_pregunta` int NOT NULL,
  `id_opcion` int NOT NULL,
  `correcta` tinyint NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: escenario
-- Banco precargado de 8 escenarios hipoteticos
CREATE TABLE `escenario` (
  `id` int NOT NULL,
  `titulo` varchar(150) COLLATE utf8mb4_spanish2_ci NOT NULL,
  `contexto` text COLLATE utf8mb4_spanish2_ci NOT NULL,
  `problema` text COLLATE utf8mb4_spanish2_ci NOT NULL,
  `entregables_esperados` text COLLATE utf8mb4_spanish2_ci NULL,
  `id_nivel` int NOT NULL,
  `estado` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: asignacion_escenario
-- El docente asigna un escenario a un estudiante de su seccion
-- estado: 1=asignado, 2=entregado, 3=evaluado
CREATE TABLE `asignacion_escenario` (
  `id` int NOT NULL,
  `id_escenario` int NOT NULL,
  `id_estudiante` int NOT NULL,
  `id_docente` int NOT NULL,
  `id_seccion` int NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_limite` date NULL,
  `estado` tinyint NOT NULL DEFAULT 1,
  `observaciones_docente` text COLLATE utf8mb4_spanish2_ci NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: entrega_escenario
-- El estudiante entrega su respuesta al escenario
CREATE TABLE `entrega_escenario` (
  `id` int NOT NULL,
  `id_asignacion` int NOT NULL,
  `contenido` longtext COLLATE utf8mb4_spanish2_ci NOT NULL,
  `archivo_ruta` varchar(500) COLLATE utf8mb4_spanish2_ci NULL,
  `archivo_nombre` varchar(255) COLLATE utf8mb4_spanish2_ci NULL,
  `fecha_entrega` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Tabla: evaluacion_escenario
-- El docente evalua la entrega del estudiante
CREATE TABLE `evaluacion_escenario` (
  `id` int NOT NULL,
  `id_entrega` int NOT NULL,
  `id_docente` int NOT NULL,
  `calificacion` decimal(5,2) NOT NULL,
  `retroalimentacion` text COLLATE utf8mb4_spanish2_ci NULL,
  `fecha_evaluacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- =====================================================================
-- INDICES
-- =====================================================================

ALTER TABLE `nivel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_nivel_numero` (`numero`);

ALTER TABLE `periodo_academico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_periodo_estado` (`estado`);

ALTER TABLE `materia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_materia_nombre` (`nombre`);

ALTER TABLE `docente_materia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_docente_materia` (`id_docente`, `id_materia`),
  ADD KEY `idx_dm_docente` (`id_docente`),
  ADD KEY `idx_dm_materia` (`id_materia`);

ALTER TABLE `seccion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_seccion_periodo` (`id_periodo`);

ALTER TABLE `seccion_materia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_seccion_materia` (`id_seccion`, `id_materia`),
  ADD KEY `idx_sm_seccion` (`id_seccion`),
  ADD KEY `idx_sm_materia` (`id_materia`),
  ADD KEY `idx_sm_docente` (`id_docente`);

ALTER TABLE `actividad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_actividad_materia` (`id_materia`),
  ADD KEY `idx_actividad_nivel` (`id_nivel`),
  ADD KEY `idx_actividad_estado` (`estado`);

ALTER TABLE `seccion_actividad`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_seccion_actividad` (`id_actividad`, `id_seccion`),
  ADD KEY `idx_sa_actividad` (`id_actividad`),
  ADD KEY `idx_sa_seccion` (`id_seccion`);

ALTER TABLE `actividad_parte`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ap_actividad` (`id_actividad`);

ALTER TABLE `actividad_parte_pregunta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_app_parte` (`id_parte`);

ALTER TABLE `actividad_parte_opcion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_apo_pregunta` (`id_pregunta`);

ALTER TABLE `actividad_entrega`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_entrega_actividad_estudiante` (`id_actividad`, `id_estudiante`),
  ADD KEY `idx_entrega_estudiante` (`id_estudiante`);

ALTER TABLE `actividad_parte_respuesta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_respuesta_entrega_parte` (`id_entrega`, `id_parte`),
  ADD KEY `idx_respuesta_parte` (`id_parte`);

ALTER TABLE `estudiante_seccion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_estudiante_seccion` (`id_estudiante`, `id_seccion`),
  ADD KEY `idx_es_seccion` (`id_seccion`);

ALTER TABLE `unidad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_unidad_nivel` (`id_nivel`),
  ADD KEY `idx_unidad_estado` (`estado`);

ALTER TABLE `unidad_archivo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ua_unidad` (`id_unidad`);

ALTER TABLE `progreso_unidad`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_progreso_estudiante_unidad` (`id_estudiante`, `id_unidad`),
  ADD KEY `idx_progreso_unidad` (`id_unidad`);

ALTER TABLE `dinamica`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dinamica_nivel` (`id_nivel`),
  ADD KEY `idx_dinamica_estado` (`estado`);

ALTER TABLE `pregunta_dinamica`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pd_dinamica` (`id_dinamica`);

ALTER TABLE `opcion_respuesta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_or_pregunta` (`id_pregunta`);

ALTER TABLE `intento_dinamica`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_id_estudiante` (`id_estudiante`),
  ADD KEY `idx_id_dinamica` (`id_dinamica`),
  ADD KEY `idx_id_estudiante_dinamica` (`id_estudiante`, `id_dinamica`);

ALTER TABLE `respuesta_intento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ri_intento_pregunta` (`id_intento`, `id_pregunta`),
  ADD KEY `idx_ri_intento` (`id_intento`),
  ADD KEY `idx_ri_pregunta` (`id_pregunta`);

ALTER TABLE `escenario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_escenario_nivel` (`id_nivel`),
  ADD KEY `idx_escenario_estado` (`estado`);

ALTER TABLE `asignacion_escenario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ae_esc_est_sec` (`id_escenario`, `id_estudiante`, `id_seccion`),
  ADD KEY `idx_ae_escenario` (`id_escenario`),
  ADD KEY `idx_ae_estudiante` (`id_estudiante`),
  ADD KEY `idx_ae_docente` (`id_docente`),
  ADD KEY `idx_ae_seccion` (`id_seccion`),
  ADD KEY `idx_ae_estado` (`estado`);

ALTER TABLE `entrega_escenario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ee_asignacion` (`id_asignacion`),
  ADD KEY `idx_ee_asignacion` (`id_asignacion`);

ALTER TABLE `evaluacion_escenario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_eva_entrega` (`id_entrega`),
  ADD KEY `idx_eva_docente` (`id_docente`);

-- Primary keys de las tablas v1.0 (se aplican aqui para que AUTO_INCREMENT
-- funcione en MySQL, que requiere la columna AUTO_INCREMENT indexada)
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `historial`
  ADD PRIMARY KEY (`id`);

-- =====================================================================
-- AUTO_INCREMENT
-- =====================================================================

ALTER TABLE `usuario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `nivel`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `periodo_academico`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `materia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `docente_materia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `seccion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `estudiante_seccion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `seccion_materia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `actividad`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `seccion_actividad`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `actividad_parte`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `actividad_parte_pregunta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `actividad_parte_opcion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `actividad_entrega`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `actividad_parte_respuesta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `unidad`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `unidad_archivo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `progreso_unidad`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `dinamica`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `pregunta_dinamica`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `opcion_respuesta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `intento_dinamica`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `respuesta_intento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `escenario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `asignacion_escenario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `entrega_escenario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `evaluacion_escenario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `historial`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

-- =====================================================================
-- FOREIGN KEYS
-- =====================================================================

ALTER TABLE `seccion`
  ADD CONSTRAINT `fk_seccion_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_academico` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `seccion_materia`
  ADD CONSTRAINT `fk_sm_seccion` FOREIGN KEY (`id_seccion`) REFERENCES `seccion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sm_materia` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sm_docente` FOREIGN KEY (`id_docente`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `actividad`
  ADD CONSTRAINT `fk_act_materia` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_act_unidad` FOREIGN KEY (`id_unidad`) REFERENCES `unidad` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_act_nivel` FOREIGN KEY (`id_nivel`) REFERENCES `nivel` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `seccion_actividad`
  ADD CONSTRAINT `fk_sa_actividad` FOREIGN KEY (`id_actividad`) REFERENCES `actividad` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sa_seccion` FOREIGN KEY (`id_seccion`) REFERENCES `seccion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `actividad_parte`
  ADD CONSTRAINT `fk_ap_actividad` FOREIGN KEY (`id_actividad`) REFERENCES `actividad` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `actividad_parte_pregunta`
  ADD CONSTRAINT `fk_app_parte` FOREIGN KEY (`id_parte`) REFERENCES `actividad_parte` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `actividad_parte_opcion`
  ADD CONSTRAINT `fk_apo_pregunta` FOREIGN KEY (`id_pregunta`) REFERENCES `actividad_parte_pregunta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `actividad_entrega`
  ADD CONSTRAINT `fk_entrega_actividad` FOREIGN KEY (`id_actividad`) REFERENCES `actividad` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entrega_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `actividad_parte_respuesta`
  ADD CONSTRAINT `fk_resp_entrega` FOREIGN KEY (`id_entrega`) REFERENCES `actividad_entrega` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_resp_parte` FOREIGN KEY (`id_parte`) REFERENCES `actividad_parte` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `estudiante_seccion`
  ADD CONSTRAINT `fk_es_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_es_seccion` FOREIGN KEY (`id_seccion`) REFERENCES `seccion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `unidad`
  ADD CONSTRAINT `fk_unidad_nivel` FOREIGN KEY (`id_nivel`) REFERENCES `nivel` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_unidad_materia` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `unidad_archivo`
  ADD CONSTRAINT `fk_ua_unidad` FOREIGN KEY (`id_unidad`) REFERENCES `unidad` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `progreso_unidad`
  ADD CONSTRAINT `fk_progreso_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_progreso_unidad` FOREIGN KEY (`id_unidad`) REFERENCES `unidad` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `dinamica`
  ADD CONSTRAINT `fk_dinamica_nivel` FOREIGN KEY (`id_nivel`) REFERENCES `nivel` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `pregunta_dinamica`
  ADD CONSTRAINT `fk_pd_dinamica` FOREIGN KEY (`id_dinamica`) REFERENCES `dinamica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `opcion_respuesta`
  ADD CONSTRAINT `fk_or_pregunta` FOREIGN KEY (`id_pregunta`) REFERENCES `pregunta_dinamica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `intento_dinamica`
  ADD CONSTRAINT `fk_id_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_dinamica` FOREIGN KEY (`id_dinamica`) REFERENCES `dinamica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `respuesta_intento`
  ADD CONSTRAINT `fk_ri_intento` FOREIGN KEY (`id_intento`) REFERENCES `intento_dinamica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ri_pregunta` FOREIGN KEY (`id_pregunta`) REFERENCES `pregunta_dinamica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `escenario`
  ADD CONSTRAINT `fk_escenario_nivel` FOREIGN KEY (`id_nivel`) REFERENCES `nivel` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `asignacion_escenario`
  ADD CONSTRAINT `fk_ae_escenario` FOREIGN KEY (`id_escenario`) REFERENCES `escenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ae_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ae_docente` FOREIGN KEY (`id_docente`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ae_seccion` FOREIGN KEY (`id_seccion`) REFERENCES `seccion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `entrega_escenario`
  ADD CONSTRAINT `fk_ee_asignacion` FOREIGN KEY (`id_asignacion`) REFERENCES `asignacion_escenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `evaluacion_escenario`
  ADD CONSTRAINT `fk_eva_entrega` FOREIGN KEY (`id_entrega`) REFERENCES `entrega_escenario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eva_docente` FOREIGN KEY (`id_docente`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `docente_materia`
  ADD CONSTRAINT `fk_dm_docente` FOREIGN KEY (`id_docente`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dm_materia` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `historial`
  ADD CONSTRAINT `historial_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- =====================================================================
-- SEEDS INICIALES
-- =====================================================================

-- Usuarios de prueba (hashes bcrypt generados con password_hash)
--   admin@admin.com       / admin123      (rol 1 = Administrador)
--   docente@admin.com     / docente123    (rol 2 = Docente)
--   estudiante@admin.com  / estudiante123 (rol 3 = Estudiante)
-- Si necesitas regenerar el hash: php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
INSERT INTO `usuario` (`id`, `nombre`, `correo`, `telefono`, `clave`, `rol`, `estado`) VALUES
(1, 'Admin',                'admin@admin.com',      '04121234567', '$2y$10$PHjO9hp.MVy0lnYxpK23teojHJZn7kELfgP/c8hgy.LKhmyrfR7VC', 1, 1),
(2, 'Docente de Prueba',    'docente@admin.com',    '04141234567', '$2y$10$oK3yldrg4InTUDjaStF4Mur.ISh67t.3NBMw3STHiZOv1XBvXJs/C', 2, 1),
(3, 'Estudiante de Prueba', 'estudiante@admin.com', '04161234567', '$2y$10$0xfEzUHoKgP01UfrkggtGexUAkhEPbxz5WopyEyEBavMhEP4LSUda', 3, 1);

-- 4 niveles fijos del sistema
INSERT INTO `nivel` (`id`, `numero`, `nombre`, `descripcion`, `porcentaje_desbloqueo`, `estado`) VALUES
(1, 1, 'Nivel 1 - Fundamentos',           'Conceptos basicos y vocabulario del area.', 0,  1),
(2, 2, 'Nivel 2 - Analisis',              'Tecnicas de analisis y estudio de casos.', 80, 1),
(3, 3, 'Nivel 3 - Aplicacion',            'Aplicacion practica de los conceptos.',     80, 1),
(4, 4, 'Nivel 4 - Sintesis y Evaluacion', 'Integracion, proyectos y evaluacion final.', 80, 1);

-- =====================================================================
-- v1.5 - SEED: 8 ESCENARIOS HIPOTETICOS (2 por nivel)
-- =====================================================================

INSERT INTO `escenario` (`id`, `titulo`, `contexto`, `problema`, `entregables_esperados`, `id_nivel`, `estado`) VALUES
(1, 'Sistema de Biblioteca Escolar',
 'La Escuela Bolivariana "Simoncito" atiende a 450 estudiantes de primaria. Actualmente el control de prestamos de libros se hace en un cuaderno llevado por la bibliotecaria, lo que genera perdida de libros, duplicados y demoras en la atencion. La direccion ha decidido digitalizar este proceso y ha contratado un pequeño equipo de desarrollo para crear un sistema web accesible desde la sala de computacion.',
 'Como ingeniero de requisitos, tu primera tarea es: identificar y documentar los requisitos funcionales y no funcionales del sistema, proponer los stakeholders principales y sugerir las tecnicas de elicitar informacion mas adecuadas para el contexto escolar.',
 'a) Lista de al menos 8 requisitos funcionales priorizados.\nb) Lista de al menos 5 requisitos no funcionales medibles.\nc) Mapa de stakeholders (4-6 perfiles).\nd) Tecnicas de elicitar informacion recomendadas con justificacion.',
 1, 1),

(2, 'App de Delivery para Emprendimiento Local',
 'Maria, una emprendedora de 28 anos, quiere crear una aplicacion movil para conectar a los restaurantes de su ciudad con clientes que buscan comida a domicilio. El mercado objetivo son usuarios de 18 a 45 anos de clase media. Maria tiene un presupuesto limitado y necesita validar la idea antes de invertir en un desarrollo profesional.',
 'Elabora un documento de vision resumido que defina: el problema, la propuesta de valor, los segmentos de usuarios (minimo 3 perfiles), las funcionalidades minimas del MVP y los criterios de exito medibles para los primeros 6 meses.',
 'a) Documento de vision (1-2 paginas).\nb) 3 perfiles de usuario con necesidades.\nc) Lista de funcionalidades del MVP.\nd) 3 metricas de exito con metas cuantificables.',
 1, 1),

(3, 'Plataforma de E-learning con Problemas de Adopcion',
 'La plataforma educativa "Aula Virtual 2.0" fue lanzada hace 8 meses con 2000 docentes registrados, pero solo el 18% la usa regularmente. El equipo de producto ha realizado encuestas y descubre que: (a) los docentes mayores de 50 anos se sienten intimidados por la interfaz, (b) los jovenes la abandonan por falta de gamificacion, (c) la app movil tiene errores frecuentes en dispositivos de gama baja.',
 'Aplica el metodo de analisis de stakeholders (poder vs interes) y diseña una estrategia diferenciada: que funcionalidad mantendrias, que eliminarias y que anadirias para resolver los tres problemas detectados. Justifica cada decision con datos del contexto.',
 'a) Matriz poder-interes con 6+ stakeholders.\nb) Tabla de decisiones (mantener/eliminar/anadir) con justificacion.\nc) Roadmap de mejoras en 3 fases.',
 2, 1),

(4, 'Hospital en Transicion Digital',
 'El Hospital Central tiene 35 anos operando con historiales medicos en papel. La direccion aprueba un proyecto de digitalizacion de 3 anos de duracion y un presupuesto de 800.000 dolares. Hay 12 departamentos clinicos, 180 medicos, 240 enfermeros y un equipo de TI pequeno (4 personas). La preocupacion principal es la seguridad de los datos y la continuidad operativa durante la migracion.',
 'Elabora un plan de elicitacion de requisitos que considere: (1) las tecnicas a aplicar con cada grupo de usuarios, (2) la gestion de riesgos durante la migracion, (3) los requisitos de cumplimiento normativo (considera HIPAA como referencia) y (4) una estrategia de validacion de los requisitos elicitados.',
 'a) Plan de elicitacion por grupo de usuarios.\nb) Matriz de riesgos del proyecto (minimo 5 riesgos).\nc) Lista de requisitos de seguridad y cumplimiento.\nd) Criterios de validacion de requisitos elicitados.',
 2, 1),

(5, 'Refactorizacion de Sistema de Nomina Legacy',
 'La empresa "Servicios Administrativos S.A." opera con un sistema de nomina desarrollado en 2008 en Visual Basic 6 con base de datos Access. Procesa nomina de 1200 empleados cada quincena. El sistema presenta: caidas frecuentes los dias de pago, calculos incorrectos de impuestos cuando hay cambios legales, y solo lo conoce un desarrollador que se jubila en 8 meses.',
 'Como parte del equipo asignado a la modernizacion: (1) documenta los requisitos actuales mediante ingenieria inversa, (2) identifica el riesgo critico de concentracion de conocimiento, (3) propone una estrategia de transicion gradual y (4) define los criterios de aceptacion para el sistema nuevo.',
 'a) Inventario de funcionalidades del sistema actual.\nb) Plan de gestion del conocimiento (transferencia).\nc) Estrategia de migracion en fases con hitos.\nd) Criterios de aceptacion del nuevo sistema (8-10 medibles).',
 3, 1),

(6, 'App de Banca Movil con Brecha de Seguridad',
 'El Banco Regional lanza su app movil hace 2 anos. Recientemente un grupo de investigadores descubrio una vulnerabilidad que permitio el acceso a 350 cuentas mediante un ataque de ingenieria social combinado con un fallo en la autenticacion de dos factores. La direccion te contrata como analista senior para liderar la respuesta.',
 'Disena un plan integral que incluya: (a) respuesta inmediata al incidente, (b) auditoria de los requisitos de seguridad actuales, (c) requisitos nuevos para cierre de la brecha, (d) plan de comunicacion a clientes y (e) metricas para monitorear la postura de seguridad post-correccion.',
 'a) Plan de respuesta al incidente (primeras 72 horas).\nb) Lista de requisitos de seguridad nuevos/actualizados.\nc) Plan de comunicacion a clientes en 3 momentos.\nd) Dashboard de metricas de seguridad (minimo 5 KPIs).',
 3, 1),

(7, 'Falla del Proyecto "Sistema Unico Nacional"',
 'El gobierno contrato hace 2 anos el desarrollo de un sistema integrado para 14 ministerios. El proyecto fue cancelado tras consumir 12 millones de dolares y 30 meses. La auditoria revela: requisitos contradictorios entre ministerios, cambios de alcance constantes (18 ampliaciones), ausencia de un proceso formal de gestion de cambios, equipo de desarrollo fragmentado en 4 ciudades, y falta de validacion con usuarios finales hasta los ultimos 6 meses.',
 'Elabora un analisis post-mortem que identifique: (1) las causas raiz principales siguiendo categorias de defecto de Boehm o similar, (2) que procesos de ingenieria de requisitos se violaron, (3) las senales de alerta temprana que se ignoraron, y (4) recomendaciones concretas para evitar que se repita en futuros proyectos.',
 'a) Analisis de causa raiz (minimo 5 causas categorizadas).\nb) Mapeo a procesos de IR violados.\nc) 5 senales de alerta con sus indicadores.\nd) Recomendaciones accionables con responsables sugeridos.',
 4, 1),

(8, 'Transformacion Digital de Universidad Tradicional',
 'La "Universidad Bicentenaria" con 45 anos de historia, 12.000 estudiantes y 800 docentes, enfrenta el reto de modernizar todos sus procesos (matricula, gestion academica, biblioteca, pagos) en 3 anos. La resistencia al cambio es alta, especialmente en docentes con mas de 20 anos de servicio. El rector quiere resultados visibles en 6 meses para justificar el presupuesto ante el consejo universitario.',
 'Disena una estrategia integral de transformacion que contemple: (a) gestion del cambio organizacional, (b) priorizacion de procesos a digitalizar, (c) modelo de madurez objetivo a 3 anos, (d) plan de capacitacion diferenciado por perfil, y (e) como medir el exito de la transformacion mas alla de lo tecnologico.',
 'a) Analisis de cambio organizacional (ADKAR o similar).\nb) Roadmap priorizado de procesos (3 horizontes).\nc) Modelo de madurez actual vs objetivo.\nd) Plan de capacitacion por audiencia (4 perfiles).\ne) Balanced scorecard del proyecto (4 perspectivas, 3 indicadores c/u).',
 4, 1);

-- =====================================================================
-- v1.5 - SEED: 24 DINAMICAS INTERACTIVAS (6 por nivel)
-- =====================================================================
-- Estructura: cada dinamica tiene 2 preguntas, cada pregunta 4 opciones
-- (1 correcta, 3 incorrectas), todas con retroalimentacion.

-- ===== NIVEL 1: Fundamentos (6 dinamicas, IDs 1-6) =====
INSERT INTO `dinamica` (`id`, `titulo`, `descripcion`, `id_nivel`, `tipo`, `estado`) VALUES
(1, 'Conceptos Basicos de Requisitos',       'Identifica los conceptos fundamentales.', 1, 1, 1),
(2, 'Tipos de Requisitos',                   'Diferencia entre requisitos funcionales y no funcionales.', 1, 1, 1),
(3, 'Elicitacion de Requisitos',             'Conceptos clave del proceso de elicitar informacion.', 1, 1, 1),
(4, 'Stakeholders del Proyecto',             'Identifica y clasifica a los involucrados.', 1, 1, 1),
(5, 'Fases de la Ingenieria de Requisitos',  'Conoce el ciclo de vida de los requisitos.', 1, 1, 1),
(6, 'El Documento ERS',                      'Caracteristicas de la especificacion de requisitos.', 1, 1, 1);

-- Preguntas nivel 1 (IDs 1-12, 2 por dinamica)
INSERT INTO `pregunta_dinamica` (`id`, `id_dinamica`, `enunciado`, `orden`, `puntos`) VALUES
(1, 1, 'Cual es la definicion mas precisa de un "requisito" en el contexto de ingenieria de software?', 1, 1),
(2, 1, 'Segun la IEEE 830, los requisitos se clasifican principalmente en:', 2, 1),
(3, 2, 'Cual de los siguientes es un requisito NO funcional?', 1, 1),
(4, 2, 'Un requisito funcional describe:', 2, 1),
(5, 3, 'Que es la "elicitacion de requisitos"?', 1, 1),
(6, 3, 'Cual NO es una tecnica de elicitar informacion?', 2, 1),
(7, 4, 'Un "stakeholder primario" se caracteriza por:', 1, 1),
(8, 4, 'Cual de estos es un stakeholder secundario?', 2, 1),
(9, 5, 'En que fase se VALIDAN los requisitos con el cliente?', 1, 1),
(10, 5, 'Cual es la diferencia entre verificacion y validacion de requisitos?', 2, 1),
(11, 6, 'Que proposito principal tiene el documento ERS?', 1, 1),
(12, 6, 'Segun la IEEE 830, el ERS debe contener:', 2, 1);

-- Opciones nivel 1 (4 por pregunta, IDs 1-48)
INSERT INTO `opcion_respuesta` (`id`, `id_pregunta`, `texto`, `es_correcta`, `orden`, `retroalimentacion`) VALUES
(1, 1, 'Una caracteristica o restriccion que el sistema debe cumplir',                            1, 1, 'Correcto. Un requisito es una condicion o capacidad que el sistema debe satisfacer.'),
(2, 1, 'Un documento legal firmado por el cliente',                                                0, 2, 'Incorrecto. El contrato es un documento legal distinto, no la definicion de un requisito.'),
(3, 1, 'El codigo fuente del sistema',                                                            0, 3, 'Incorrecto. El codigo es la implementacion, no la definicion del requisito.'),
(4, 1, 'Una prueba automatizada del sistema',                                                      0, 4, 'Incorrecto. Las pruebas son medios de verificacion, no la definicion del requisito.'),
(5, 2, 'Funcionales y no funcionales',                                                            1, 1, 'Correcto. Esta es la clasificacion principal de la IEEE 830.'),
(6, 2, 'Internos y externos',                                                                       0, 2, 'Incorrecto. Esa es otra clasificacion posible pero no la principal de la IEEE 830.'),
(7, 2, 'Logicos y fisicos',                                                                        0, 3, 'Incorrecto. Esta no es una clasificacion estandar de requisitos.'),
(8, 2, 'Manuales y automaticos',                                                                   0, 4, 'Incorrecto. Esta es una clasificacion de pruebas, no de requisitos.'),
(9, 3, 'El sistema debe responder en menos de 2 segundos',                                         1, 1, 'Correcto. Es un requisito de rendimiento, por lo tanto no funcional.'),
(10, 3, 'El sistema debe permitir registrar usuarios',                                              0, 2, 'Incorrecto. Esto es una funcion del sistema, es un requisito funcional.'),
(11, 3, 'El sistema debe generar reportes mensuales',                                               0, 3, 'Incorrecto. Generar un reporte es una funcionalidad, requisito funcional.'),
(12, 3, 'El sistema debe calcular el impuesto sobre la renta',                                       0, 4, 'Incorrecto. Calcular el impuesto es una funcion, requisito funcional.'),
(13, 4, 'Una funcion o comportamiento especifico que el sistema debe realizar',                    1, 1, 'Correcto. Los requisitos funcionales describen QUE hace el sistema.'),
(14, 4, 'La velocidad de respuesta del sistema',                                                    0, 2, 'Incorrecto. Eso es rendimiento, un requisito no funcional.'),
(15, 4, 'La plataforma tecnologica a usar',                                                         0, 3, 'Incorrecto. La plataforma es una decision de diseno, no un requisito funcional.'),
(16, 4, 'La metodologia de desarrollo del equipo',                                                  0, 4, 'Incorrecto. La metodologia es un tema de gestion del proyecto, no un requisito.'),
(17, 5, 'El proceso de descubrir, capturar y documentar los requisitos de los stakeholders',        1, 1, 'Correcto. La elicitar informacion es la primera fase y se enfoca en descubrir y documentar.'),
(18, 5, 'La codificacion del sistema en un lenguaje de programacion',                               0, 2, 'Incorrecto. La codificacion es parte de la implementacion, no de la elicitar informacion.'),
(19, 5, 'La instalacion del sistema en el servidor del cliente',                                   0, 3, 'Incorrecto. El despliegue es otra fase, no elicitar informacion.'),
(20, 5, 'La prueba automatizada del sistema',                                                       0, 4, 'Incorrecto. Las pruebas son verificacion, no elicitar informacion.'),
(21, 6, 'Compilacion automatica',                                                                   1, 1, 'Correcto. Compilar no es elicitar informacion, es una actividad tecnica.'),
(22, 6, 'Entrevistas',                                                                              0, 2, 'Incorrecto. Las entrevistas SON una tecnica clasica de elicitar informacion.'),
(23, 6, 'Talleres de trabajo (workshops)',                                                          0, 3, 'Incorrecto. Los workshops son una tecnica muy util de elicitar informacion.'),
(24, 6, 'Observacion directa',                                                                       0, 4, 'Incorrecto. La observacion es una tecnica fundamental de elicitar informacion.'),
(25, 7, 'Interactua directamente con el sistema y se beneficia o sufre su uso',                      1, 1, 'Correcto. Los stakeholders primarios usan el sistema directamente.'),
(26, 7, 'Solo lee los informes generados por el sistema',                                            0, 2, 'Incorrecto. Quien solo lee informes suele ser stakeholder secundario.'),
(27, 7, 'Es el jefe del proyecto de desarrollo',                                                     0, 3, 'Incorrecto. El jefe de proyecto es un actor, pero no necesariamente usuario primario.'),
(28, 7, 'Trabaja en el departamento de finanzas de la empresa',                                     0, 4, 'Incorrecto. No toda persona de finanzas es stakeholder primaria del sistema.'),
(29, 8, 'El proveedor de servicios en la nube que aloja el sistema',                                 1, 1, 'Correcto. Un proveedor externo que no usa el sistema pero se ve afectado es secundario.'),
(30, 8, 'El usuario final que inicia sesion cada dia',                                                0, 2, 'Incorrecto. El usuario final es claramente un stakeholder primario.'),
(31, 8, 'El administrador del sistema',                                                              0, 3, 'Incorrecto. El administrador interactua directamente, es primario.'),
(32, 8, 'El cliente que paga por el desarrollo',                                                     0, 4, 'Incorrecto. El cliente que paga es un stakeholder clave y primario.'),
(33, 9, 'En la fase de Validacion',                                                                 1, 1, 'Correcto. La validacion ocurre con el cliente para confirmar que los requisitos reflejan sus necesidades.'),
(34, 9, 'Solo en la fase de Elicitacion',                                                           0, 2, 'Incorrecto. La elicitar informacion descubre; validar confirma con el cliente.'),
(35, 9, 'Unicamente despues de la implementacion',                                                   0, 3, 'Incorrecto. Validar tarde es una de las principales causas de falla de proyectos.'),
(36, 9, 'Nunca se valida, solo se verifica',                                                        0, 4, 'Incorrecto. La validacion es una fase critica en la ingenieria de requisitos.'),
(37, 10, 'Verificacion: "lo estamos construyendo correctamente" / Validacion: "estamos construyendo lo correcto"', 1, 1, 'Correcto. Esta es la definicion clasica que distingue ambos conceptos.'),
(38, 10, 'Verificacion la hace el cliente / Validacion la hace el equipo',                          0, 2, 'Incorrecto. Ambos pueden participar en verificacion y validacion.'),
(39, 10, 'No hay diferencia, son sinonimos',                                                        0, 3, 'Incorrecto. Aunque relacionados, son conceptos distintos en gestion de calidad.'),
(40, 10, 'Verificacion es manual / Validacion es automatica',                                        0, 4, 'Incorrecto. La distincion no es sobre el metodo, sino sobre el proposito.'),
(41, 11, 'Documentar de forma completa y no ambigua los requisitos del software',                    1, 1, 'Correcto. El ERS es la especificacion formal y completa de los requisitos.'),
(42, 11, 'Reemplazar el contrato con el cliente',                                                   0, 2, 'Incorrecto. El ERS no reemplaza al contrato, es un anexo tecnico.'),
(43, 11, 'Definir la arquitectura interna del sistema',                                              0, 3, 'Incorrecto. La arquitectura es diseno, no esta en el ERS.'),
(44, 11, 'Estimar el costo del proyecto',                                                            0, 4, 'Incorrecto. La estimacion es gestion de proyectos, no alcance del ERS.'),
(45, 12, 'Descripcion de la interfaz de usuario, funciones del sistema y restricciones',              1, 1, 'Correcto. Segun IEEE 830 el ERS cubre estos tres grandes bloques.'),
(46, 12, 'Solo el codigo fuente del sistema',                                                        0, 2, 'Incorrecto. El ERS no incluye codigo.'),
(47, 12, 'Unicamente el cronograma del proyecto',                                                    0, 3, 'Incorrecto. El cronograma no es parte de la especificacion de requisitos.'),
(48, 12, 'La nomina del equipo de desarrollo',                                                       0, 4, 'Incorrecto. La nomina es informacion administrativa, no tecnica.');

-- ===== NIVEL 2: Analisis (6 dinamicas, IDs 7-12) =====
INSERT INTO `dinamica` (`id`, `titulo`, `descripcion`, `id_nivel`, `tipo`, `estado`) VALUES
(7,  'Tecnicas de Elicitacion',         'Conoce cuando aplicar cada tecnica.', 2, 1, 1),
(8,  'Tipos de Entrevistas',            'Estructurada, semi-estructurada y abierta.', 2, 1, 1),
(9,  'Casos de Uso',                    'Caracteristicas y componentes de un caso de uso.', 2, 1, 1),
(10, 'Historias de Usuario',            'Caracteristicas de las user stories en metodologias agiles.', 2, 1, 1),
(11, 'Priorizacion MoSCoW',             'Como clasificar requisitos por prioridad.', 2, 1, 1),
(12, 'Prototipado',                     'Tipos y proposito de los prototipos.', 2, 1, 1);

INSERT INTO `pregunta_dinamica` (`id`, `id_dinamica`, `enunciado`, `orden`, `puntos`) VALUES
(13, 7,  'Cuando es mas apropiado usar la tecnica de "observacion directa"?', 1, 1),
(14, 7,  'Que ventaja tiene elicitar informacion con un grupo focal frente a entrevistas individuales?', 2, 1),
(15, 8,  'Cual es la diferencia principal entre una entrevista estructurada y una semi-estructurada?', 1, 1),
(16, 8,  'En que situacion es preferible una entrevista NO estructurada?', 2, 1),
(17, 9,  'Cual es el proposito principal de un diagrama de caso de uso?', 1, 1),
(18, 9,  'Quien es el "actor" en un caso de uso?', 2, 1),
(19, 10, 'Segun la guia INVEST, una buena historia de usuario debe ser:', 1, 1),
(20, 10, 'Que representan los "criterios de aceptacion" en una historia de usuario?', 2, 1),
(21, 11, 'En la tecnica MoSCoW, la "M" significa:', 1, 1),
(22, 11, 'Cual seria la clasificacion correcta para un requisito "deseable pero no esencial"?', 2, 1),
(23, 12, 'Cual es la principal ventaja del prototipado?', 1, 1),
(24, 12, 'Un prototipo "desechable" (throwaway) se caracteriza por:', 2, 1);

INSERT INTO `opcion_respuesta` (`id`, `id_pregunta`, `texto`, `es_correcta`, `orden`, `retroalimentacion`) VALUES
(49, 13, 'Cuando necesitamos entender el contexto real de uso del sistema',                        1, 1, 'Correcto. La observacion directa es ideal para descubrir requisitos tacitos en contexto.'),
(50, 13, 'Cuando el cliente no tiene tiempo para una reunion',                                      0, 2, 'Incorrecto. La falta de tiempo se resuelve mejor con encuestas.'),
(51, 13, 'Cuando necesitamos documentar requisitos rapidamente',                                    0, 3, 'Incorrecto. La observacion consume tiempo, no es rapida.'),
(52, 13, 'Cuando el sistema ya esta implementado',                                                  0, 4, 'Incorrecto. La observacion es para elicitar informacion, no para auditar.'),
(53, 14, 'Permite identificar conflictos y sinergias entre stakeholders',                          1, 1, 'Correcto. El grupo focal es ideal para revelar discrepancias entre actores.'),
(54, 14, 'Es mas rapido y economico',                                                                0, 2, 'Incorrecto. Un grupo focal suele ser mas costoso en logistica.'),
(55, 14, 'Permite profundizar mas en cada persona',                                                 0, 3, 'Incorrecto. La entrevista individual profundiza mas por persona.'),
(56, 14, 'Elimina la necesidad de analisis posterior',                                               0, 4, 'Incorrecto. Siempre se requiere analisis de la informacion obtenida.'),
(57, 15, 'La estructurada usa preguntas fijas; la semi-estructurada combina preguntas fijas con flexibilidad', 1, 1, 'Correcto. La semi-estructurada permite profundizar en temas relevantes.'),
(58, 15, 'La estructurada es solo para clientes tecnicos',                                           0, 2, 'Incorrecto. La diferencia no es el perfil del entrevistado.'),
(59, 15, 'La semi-estructurada no permite tomar notas',                                               0, 3, 'Incorrecto. Siempre se debe documentar la entrevista.'),
(60, 15, 'La estructurada dura mas tiempo',                                                          0, 4, 'Incorrecto. La duracion no es lo que las distingue.'),
(61, 16, 'Cuando el dominio es complejo y queremos explorar libremente',                             1, 1, 'Correcto. La entrevista abierta es util para exploracion inicial de dominios complejos.'),
(62, 16, 'Cuando necesitamos datos cuantitativos exactos',                                          0, 2, 'Incorrecto. Para datos cuantitativos son mejores las encuestas.'),
(63, 16, 'Cuando todos los stakeholders tienen la misma opinion',                                    0, 3, 'Incorrecto. Si todos coinciden no se necesita explorar.'),
(64, 16, 'Cuando el sistema es muy pequeno y simple',                                                0, 4, 'Incorrecto. Para sistemas simples basta con una entrevista estructurada breve.'),
(65, 17, 'Mostrar las interacciones entre los actores y el sistema',                                  1, 1, 'Correcto. El caso de uso modela quien hace que con el sistema.'),
(66, 17, 'Mostrar la arquitectura interna del sistema',                                               0, 2, 'Incorrecto. Para arquitectura existen otros diagramas (componentes, despliegue).'),
(67, 17, 'Definir el modelo de datos del sistema',                                                    0, 3, 'Incorrecto. El modelo de datos se representa con diagramas ER o de clases.'),
(68, 17, 'Estimar el tiempo de desarrollo',                                                           0, 4, 'Incorrecto. La estimacion es gestion de proyectos, no un caso de uso.'),
(69, 18, 'Una entidad externa que interactua con el sistema',                                        1, 1, 'Correcto. El actor representa un rol externo al sistema.'),
(70, 18, 'Un modulo interno del sistema',                                                             0, 2, 'Incorrecto. Los modulos internos no son actores; son parte del sistema.'),
(71, 18, 'Una clase del modelo de datos',                                                            0, 3, 'Incorrecto. Las clases del modelo de datos son entidades pasivas.'),
(72, 18, 'El administrador de la base de datos',                                                     0, 4, 'Incorrecto. El DBA puede ser un actor si interactua con el sistema, pero la definicion es mas amplia.'),
(73, 19, 'Independiente, Negociable, Valiosa, Estimable, Small, Testeable',                          1, 1, 'Correcto. INVEST es el acronimo con las 6 caracteristicas de una buena historia.'),
(74, 19, 'Interna, Normal, Visible, Estable, Simple, Trivial',                                       0, 2, 'Incorrecto. Esas no son las caracteristicas INVEST.'),
(75, 19, 'Innovadora, Nueva, Valiosa, Extensa, Tecnica, Tentativa',                                   0, 3, 'Incorrecto. No es el acronimo correcto.'),
(76, 19, 'Incremental, Necesaria, Verificable, Escalable, Solid, Temporal',                          0, 4, 'Incorrecto. No corresponde a INVEST.'),
(77, 20, 'Las condiciones que deben cumplirse para considerar la historia como completada',         1, 1, 'Correcto. Los criterios de aceptacion definen "done" en agiles.'),
(78, 20, 'El costo en horas de la historia',                                                          0, 2, 'Incorrecto. El costo se estima en story points, no son los criterios de aceptacion.'),
(79, 20, 'Los stakeholders involucrados en la historia',                                              0, 3, 'Incorrecto. Los stakeholders se definen en otra parte.'),
(80, 20, 'El nombre del desarrollador asignado',                                                      0, 4, 'Incorrecto. La asignacion es gestion del equipo, no parte de la historia.'),
(81, 21, 'Must have (Debe tener)',                                                                    1, 1, 'Correcto. La M en MoSCoW es Must have, los requisitos innegociables.'),
(82, 21, 'Maybe (Quizas)',                                                                            0, 2, 'Incorrecto. Maybe no es la M, sino que Would/Could son las opcionales.'),
(83, 21, 'Main (Principal)',                                                                          0, 3, 'Incorrecto. MoSCoW no usa Main.'),
(84, 21, 'Maximum (Maximo)',                                                                          0, 4, 'Incorrecto. MoSCoW no usa Maximum.'),
(85, 22, 'Should have (Deberia tener) o Could have (Podria tener)',                                   1, 1, 'Correcto. Los requisitos "deseables pero no esenciales" caen en Should o Could.'),
(86, 22, 'Must have (Debe tener)',                                                                    0, 2, 'Incorrecto. Must have es para requisitos esenciales, no negociables.'),
(87, 22, 'Will not have (No tendra)',                                                                 0, 3, 'Incorrecto. Will not have es para requisitos explicitamente descartados.'),
(88, 22, 'Ninguna, todos son Must have',                                                              0, 4, 'Incorrecto. Siempre hay requisitos de distinta prioridad.'),
(89, 23, 'Permite validar requisitos ambiguos con el usuario antes de codificar',                   1, 1, 'Correcto. El prototipo reduce la ambiguedad al mostrar el sistema visualmente.'),
(90, 23, 'Reduce el tiempo de desarrollo drasticamente',                                             0, 2, 'Incorrecto. De hecho el prototipado suele añadir tiempo al inicio.'),
(91, 23, 'Elimina la necesidad de hacer pruebas',                                                     0, 3, 'Incorrecto. Las pruebas siguen siendo necesarias.'),
(92, 23, 'Reemplaza la documentacion de requisitos',                                                  0, 4, 'Incorrecto. El prototipo complementa, no reemplaza la documentacion.'),
(93, 24, 'Se construye rapido y se descarta, solo para explorar opciones',                            1, 1, 'Correcto. Los prototipos throwaway son temporales, para validar ideas.'),
(94, 24, 'Se convierte en el sistema final',                                                          0, 2, 'Incorrecto. Eso describe un prototipo evolutivo, no throwaway.'),
(95, 24, 'Solo se hace en papel',                                                                     0, 3, 'Incorrecto. Puede ser de baja fidelidad, pero no exclusivamente en papel.'),
(96, 24, 'No requiere participacion del usuario',                                                    0, 4, 'Incorrecto. Todo prototipo requiere retroalimentacion del usuario.');

-- ===== NIVEL 3: Aplicacion (6 dinamicas, IDs 13-18) =====
INSERT INTO `dinamica` (`id`, `titulo`, `descripcion`, `id_nivel`, `tipo`, `estado`) VALUES
(13, 'Trazabilidad de Requisitos',           'Como rastrear requisitos a lo largo del proyecto.', 3, 1, 1),
(14, 'Validacion vs Verificacion',           'Aplicacion practica de V&V en proyectos.', 3, 1, 1),
(15, 'Gestion de Cambios de Requisitos',     'Control de cambios en el alcance del proyecto.', 3, 1, 1),
(16, 'Matriz RACI',                          'Asignacion de responsabilidades en requisitos.', 3, 1, 1),
(17, 'Tecnicas de Negociacion',              'Resolucion de conflictos entre stakeholders.', 3, 1, 1),
(18, 'Pruebas de Aceptacion de Usuario (UAT)', 'Caracteristicas y proposito de las UAT.', 3, 1, 1);

INSERT INTO `pregunta_dinamica` (`id`, `id_dinamica`, `enunciado`, `orden`, `puntos`) VALUES
(25, 13, 'Que es la trazabilidad de requisitos?', 1, 1),
(26, 13, 'Cual NO es un tipo de trazabilidad?', 2, 1),
(27, 14, 'En un proyecto real, cuando se aplica la validacion de requisitos?', 1, 1),
(28, 14, 'Cual de estas actividades es un ejemplo de VERIFICACION de requisitos?', 2, 1),
(29, 15, 'Cual es el primer paso ante una solicitud de cambio de alcance?', 1, 1),
(30, 15, 'Que debe contener un registro formal de cambio de requisitos?', 2, 1),
(31, 16, 'En la matriz RACI, la "R" significa:', 1, 1),
(32, 16, 'Cual es la diferencia entre "Responsible" y "Accountable"?', 2, 1),
(33, 17, 'Ante dos stakeholders con requisitos conflictivos, lo primero que debes hacer es:', 1, 1),
(34, 17, 'Cual de las siguientes NO es una estrategia de negociacion efectiva?', 2, 1),
(35, 18, 'Cual es el objetivo principal de las Pruebas de Aceptacion de Usuario (UAT)?', 1, 1),
(36, 18, 'Quien debe ejecutar las UAT idealmente?', 2, 1);

INSERT INTO `opcion_respuesta` (`id`, `id_pregunta`, `texto`, `es_correcta`, `orden`, `retroalimentacion`) VALUES
(97, 25, 'La capacidad de seguir el ciclo de vida de un requisito desde su origen hasta su implementacion', 1, 1, 'Correcto. La trazabilidad conecta el requisito con su origen, diseno, codigo y pruebas.'),
(98, 25, 'Un metodo para encriptar requisitos sensibles',                                                0, 2, 'Incorrecto. La trazabilidad no tiene que ver con seguridad.'),
(99, 25, 'La firma digital de los requisitos aprobados',                                                  0, 3, 'Incorrecto. La aprobacion es parte, pero la trazabilidad es mas amplia.'),
(100, 25, 'El numero de paginas del documento de requisitos',                                              0, 4, 'Incorrecto. La trazabilidad es relacional, no de extension.'),
(101, 26, 'Trazabilidad emocional',                                                                       1, 1, 'Correcto. No existe la "trazabilidad emocional" como tipo formal.'),
(102, 26, 'Trazabilidad hacia adelante (forward)',                                                         0, 2, 'Incorrecto. La trazabilidad forward SI existe (del requisito al diseno/codigo).'),
(103, 26, 'Trazabilidad hacia atras (backward)',                                                          0, 3, 'Incorrecto. La trazabilidad backward SI existe (del requisito al origen).'),
(104, 26, 'Trazabilidad entre requisitos',                                                                 0, 4, 'Incorrecto. La trazabilidad entre requisitos (dependencias) SI es un tipo valido.'),
(105, 27, 'De forma continua durante todo el proyecto, especialmente al inicio y ante cambios',         1, 1, 'Correcto. La validacion no es un evento unico, es continua.'),
(106, 27, 'Solo al final del proyecto, antes de la entrega',                                              0, 2, 'Incorrecto. Validar solo al final es una causa clasica de fracaso.'),
(107, 27, 'Unicamente en la reunion de kick-off',                                                         0, 3, 'Incorrecto. En el kick-off se alinean expectativas, no se valida a profundidad.'),
(108, 27, 'Nunca, la validacion la hace solo el cliente al recibir el producto',                         0, 4, 'Incorrecto. La validacion es un proceso, no un evento.'),
(109, 28, 'Revisar que los requisitos cumplan las plantillas y estandares del proyecto',                 1, 1, 'Correcto. Esto es verificacion: confirmar que el producto esta bien construido.'),
(110, 28, 'Preguntar al cliente si el sistema cumple sus necesidades de negocio',                        0, 2, 'Incorrecto. Eso es validacion, no verificacion.'),
(111, 28, 'Hacer una demo al usuario final para confirmar su satisfaccion',                             0, 3, 'Incorrecto. La demo es validacion.'),
(112, 28, 'Realizar pruebas de aceptacion con el cliente',                                                0, 4, 'Incorrecto. Las UAT son validacion.'),
(113, 29, 'Evaluar el impacto y registrar formalmente la solicitud',                                      1, 1, 'Correcto. Toda solicitud de cambio debe pasar por un analisis de impacto.'),
(114, 29, 'Implementar el cambio inmediatamente para no retrasar al cliente',                             0, 2, 'Incorrecto. Hacer cambios sin analisis genera deuda tecnica y scope creep.'),
(115, 29, 'Negarse al cambio porque ya se definio el alcance',                                           0, 3, 'Incorrecto. Rechazar sin evaluar tambien es mala practica.'),
(116, 29, 'Esperar a la reunion mensual para decidir',                                                    0, 4, 'Incorrecto. Los cambios deben evaluarse con prontitud para no acumular.'),
(117, 30, 'Descripcion, justificacion, impacto, costo, fecha, aprobador',                                1, 1, 'Correcto. Una solicitud formal debe tener trazabilidad completa.'),
(118, 30, 'Solo la fecha en que se solicito el cambio',                                                   0, 2, 'Incorrecto. La fecha es solo uno de los datos necesarios.'),
(119, 30, 'Unicamente el nombre del solicitante',                                                         0, 3, 'Incorrecto. Es importante, pero no es suficiente.'),
(120, 30, 'Una descripcion breve sin mas detalles',                                                       0, 4, 'Incorrecto. La trazabilidad es clave en gestion de cambios.'),
(121, 31, 'Responsible (Responsable de ejecutar)',                                                        1, 1, 'Correcto. R es Responsible, quien ejecuta la tarea.'),
(122, 31, 'Reviewer (Revisor)',                                                                          0, 2, 'Incorrecto. RACI no usa Reviewer.'),
(123, 31, 'Recurrent (Recurrente)',                                                                      0, 3, 'Incorrecto. RACI no usa Recurrent.'),
(124, 31, 'Receiver (Receptor)',                                                                          0, 4, 'Incorrecto. RACI no usa Receiver.'),
(125, 32, 'Responsible ejecuta; Accountable es el unico dueno del resultado final',                      1, 1, 'Correcto. Solo hay un Accountable por tarea; varios Responsible pueden apoyarlo.'),
(126, 32, 'Son sinonimos',                                                                                0, 2, 'Incorrecto. La distincion es critica en gestion de responsabilidades.'),
(127, 32, 'Responsible es el jefe; Accountable es el empleado',                                          0, 3, 'Incorrecto. No se trata de jerarquia, sino de rol en la tarea.'),
(128, 32, 'Accountable ejecuta; Responsible aprueba',                                                     0, 4, 'Incorrecto. Es al reves: Responsible ejecuta, Accountable aprueba.'),
(129, 33, 'Entender las necesidades subyacentes de ambos stakeholders',                                  1, 1, 'Correcto. Negociar requiere comprender primero, proponer despues.'),
(130, 33, 'Decidir a favor del que pago mas por el proyecto',                                              0, 2, 'Incorrecto. El pago no da derecho a requisitos conflictivos con el alcance.'),
(131, 33, 'Implementar ambas opciones y que el cliente elija',                                            0, 3, 'Incorrecto. Esto genera retrabajo y diluye la responsabilidad.'),
(132, 33, 'Ignorar el conflicto esperando que se resuelva solo',                                          0, 4, 'Incorrecto. Los conflictos ignorados escalan y retrasan el proyecto.'),
(133, 34, 'Imponer la decision del jefe sin escuchar a las partes',                                       1, 1, 'Correcto. Imponer sin escuchar genera resentimiento y no resuelve.'),
(134, 34, 'Buscar opciones que satisfagan a ambas partes (win-win)',                                       0, 2, 'Incorrecto. Esto SI es una estrategia efectiva.'),
(135, 34, 'Usar criterios objetivos para priorizar (valor, riesgo, costo)',                              0, 3, 'Incorrecto. Esto SI es efectivo y recomendado.'),
(136, 34, 'Documentar el acuerdo y los compromisos de cada parte',                                        0, 4, 'Incorrecto. Documentar SI es una buena practica de negociacion.'),
(137, 35, 'Validar que el sistema cumple los requisitos de negocio desde la perspectiva del usuario',   1, 1, 'Correcto. Las UAT confirman que el sistema resuelve el problema del usuario.'),
(138, 35, 'Detectar todos los bugs tecnicos del sistema',                                                  0, 2, 'Incorrecto. Los bugs tecnicos se prueban antes, en pruebas unitarias e integrales.'),
(139, 35, 'Reemplazar las pruebas automatizadas',                                                         0, 3, 'Incorrecto. Las UAT complementan, no reemplazan.'),
(140, 35, 'Capacitar al usuario en el uso del sistema',                                                   0, 4, 'Incorrecto. La capacitacion es otra actividad, aunque relacionada.'),
(141, 36, 'Los usuarios finales con apoyo del equipo de QA',                                              1, 1, 'Correcto. Las UAT son ejecutadas por usuarios, con apoyo del equipo de calidad.'),
(142, 36, 'Solo los desarrolladores',                                                                     0, 2, 'Incorrecto. Si solo las hace desarrollo, no hay perspectiva del usuario.'),
(143, 36, 'Exclusivamente el jefe de proyecto',                                                           0, 3, 'Incorrecto. El PM no es quien valida el uso real.'),
(144, 36, 'Unicamente el director de la empresa',                                                         0, 4, 'Incorrecto. El director valida resultados de negocio, no uso del sistema.');

-- ===== NIVEL 4: Sintesis y Evaluacion (6 dinamicas, IDs 19-24) =====
INSERT INTO `dinamica` (`id`, `titulo`, `descripcion`, `id_nivel`, `tipo`, `estado`) VALUES
(19, 'Analisis de Riesgos en Requisitos',     'Identifica y prioriza riesgos del proyecto.', 4, 1, 1),
(20, 'Metricas de Calidad de Requisitos',     'Mide la calidad del proceso de requisitos.', 4, 1, 1),
(21, 'Revisiones de Requisitos',              'Tipos y tecnicas de inspection de requisitos.', 4, 1, 1),
(22, 'CMM y Madurez del Proceso',             'Niveles de capacidad y madurez.', 4, 1, 1),
(23, 'Herramientas CASE para IR',             'Tipos de herramientas de soporte.', 4, 1, 1),
(24, 'Mejora Continua en Ingenieria de Requisitos', 'Como iterar y mejorar el proceso.', 4, 1, 1);

INSERT INTO `pregunta_dinamica` (`id`, `id_dinamica`, `enunciado`, `orden`, `puntos`) VALUES
(37, 19, 'En la gestion de riesgos de requisitos, que representa la matriz Probabilidad x Impacto?', 1, 1),
(38, 19, 'Cual de estos es un riesgo tipico de los REQUISITOS (no del proyecto general)?', 2, 1),
(39, 20, 'Cual de las siguientes es una metrica valida de calidad de requisitos?', 1, 1),
(40, 20, 'Que mide el "indice de estabilidad de requisitos" (RSI)?', 2, 1),
(41, 21, 'Cual es el objetivo principal de una revision formal de requisitos?', 1, 1),
(42, 21, 'En la tecnica Fagan de inspection, cuantos roles principales participan?', 2, 1),
(43, 22, 'Cuantos niveles tiene el modelo CMM original (Capability Maturity Model)?', 1, 1),
(44, 22, 'En el nivel 3 de CMM (Definido), que caracteristica tiene el proceso?', 2, 1),
(45, 23, 'Cual es el objetivo principal de una herramienta CASE de tipo "upper" para IR?', 1, 1),
(46, 23, 'Que tipo de herramienta sirve principalmente para gestionar la trazabilidad?', 2, 1),
(47, 24, 'Cual es la base del modelo de mejora continua PDCA?', 1, 1),
(48, 24, 'Que actividad NO pertenece a la fase "Act" del ciclo PDCA?', 2, 1);

INSERT INTO `opcion_respuesta` (`id`, `id_pregunta`, `texto`, `es_correcta`, `orden`, `retroalimentacion`) VALUES
(145, 37, 'Permite priorizar riesgos segun la combinacion de su probabilidad de ocurrencia y su consecuencia', 1, 1, 'Correcto. La matriz PxI es la herramienta clasica de priorizacion de riesgos.'),
(146, 37, 'Es una forma de calcular el costo del proyecto',                                                0, 2, 'Incorrecto. La estimacion de costos es otra actividad.'),
(147, 37, 'Sirve para asignar responsables del proyecto',                                                  0, 3, 'Incorrecto. La asignacion de responsables se hace con RACI.'),
(148, 37, 'Es un grafico de Gantt para el cronograma',                                                     0, 4, 'Incorrecto. La matriz PxI no es una representacion temporal.'),
(149, 38, 'Requisitos ambiguos o contradictorios entre stakeholders',                                      1, 1, 'Correcto. La ambiguedad y los conflictos entre requisitos son riesgos clasicos de IR.'),
(150, 38, 'Falla del disco duro del servidor de desarrollo',                                                0, 2, 'Incorrecto. Eso es un riesgo tecnico/infraestructura, no de requisitos.'),
(151, 38, 'Renuncia de un programador junior del equipo',                                                   0, 3, 'Incorrecto. Es un riesgo de recursos humanos.'),
(152, 38, 'Aumento del precio del dolar para insumos importados',                                           0, 4, 'Incorrecto. Es un riesgo financiero, no de requisitos.'),
(153, 39, 'Numero promedio de requisitos por stakeholder',                                                  1, 1, 'Correcto. Es una metrica que se puede medir y da informacion sobre la complejidad.'),
(154, 39, 'Cantidad de lineas de codigo del sistema',                                                       0, 2, 'Incorrecto. Las lineas de codigo no miden calidad de requisitos.'),
(155, 39, 'Numero de bugs encontrados en produccion',                                                       0, 3, 'Incorrecto. Eso mide calidad del software, no de requisitos.'),
(156, 39, 'Cantidad de cafes tomados por el equipo de requisitos',                                          0, 4, 'Incorrecto. No es una metrica de calidad.'),
(157, 40, 'El porcentaje de requisitos que no cambian despues de una fase del proyecto',                    1, 1, 'Correcto. RSI mide cuantos requisitos se mantienen estables.'),
(158, 40, 'La cantidad total de requisitos en el documento ERS',                                            0, 2, 'Incorrecto. Eso seria "tamano", no estabilidad.'),
(159, 40, 'El tiempo promedio de elicitar informacion por requisito',                                         0, 3, 'Incorrecto. Eso mide productividad, no estabilidad.'),
(160, 40, 'El numero de stakeholders que participan en el proyecto',                                         0, 4, 'Incorrecto. Eso es alcance, no estabilidad.'),
(161, 41, 'Detectar defectos en los requisitos antes de pasar a la fase de diseno',                         1, 1, 'Correcto. La revision busca encontrar errores temprano cuando corregirlos es mas barato.'),
(162, 41, 'Aprobar formalmente el cronograma del proyecto',                                                 0, 2, 'Incorrecto. La revision de requisitos no cubre cronograma.'),
(163, 41, 'Asignar tareas al equipo de desarrollo',                                                        0, 3, 'Incorrecto. La asignacion de tareas se hace en planificacion.'),
(164, 41, 'Definir la arquitectura del sistema',                                                            0, 4, 'Incorrecto. La arquitectura es diseno, no requisitos.'),
(165, 42, '6 roles: Moderador, Autor, Lector, Registrador, Inspector y Product Owner',                       1, 1, 'Correcto. La inspeccion de Fagan define 6 roles principales.'),
(166, 42, '3 roles: Desarrollador, Tester y Project Manager',                                                0, 2, 'Incorrecto. Esa no es la distribucion de Fagan.'),
(167, 42, '2 roles: Cliente y desarrollador',                                                               0, 3, 'Incorrecto. Fagan requiere roles mas especificos.'),
(168, 42, '1 solo rol: el analista senior hace todo',                                                        0, 4, 'Incorrecto. La inspeccion formal requiere separacion de roles.'),
(169, 43, '5 niveles (Inicial, Repetible, Definido, Administrado, Optimizado)',                             1, 1, 'Correcto. CMM clasico tiene 5 niveles de madurez.'),
(170, 43, '3 niveles (Basico, Intermedio, Avanzado)',                                                        0, 2, 'Incorrecto. CMM clasico tiene 5 niveles.'),
(171, 43, '7 niveles (como ISO 15504)',                                                                      0, 3, 'Incorrecto. ISO 15504 es otro modelo, no CMM clasico.'),
(172, 43, '10 niveles (como CMMi v2)',                                                                       0, 4, 'Incorrecto. CMMI v2 tiene areas de practica, no 10 niveles.'),
(173, 44, 'El proceso esta documentado, estandarizado y es proactivo',                                       1, 1, 'Correcto. En el nivel 3 el proceso esta definido y es gestionado activamente.'),
(174, 44, 'El proceso no existe todavia',                                                                    0, 2, 'Incorrecto. Eso seria nivel 1.'),
(175, 44, 'El proceso depende del heroismo individual',                                                     0, 3, 'Incorrecto. Eso es nivel 1.'),
(176, 44, 'El proceso se mide y optimiza continuamente',                                                     0, 4, 'Incorrecto. Eso es nivel 5 (Optimizado).'),
(177, 45, 'Soportan las fases de modelado y analisis de requisitos (diagramas, especificaciones)',         1, 1, 'Correcto. Las herramientas upper CASE apoyan la especificacion y modelado.'),
(178, 45, 'Generan el codigo de la aplicacion automaticamente',                                              0, 2, 'Incorrecto. Eso seria una herramienta de generacion de codigo, no upper CASE.'),
(179, 45, 'Gestionan la nomina del equipo',                                                                  0, 3, 'Incorrecto. No es el alcance de CASE para IR.'),
(180, 45, 'Reemplazan al equipo de QA en las pruebas',                                                      0, 4, 'Incorrecto. CASE no sustituye al equipo de calidad.'),
(181, 46, 'Una herramienta de gestion de requisitos (RM tool)',                                             1, 1, 'Correcto. Las RM tools (como Jama, DOORS, Orcanos) gestionan trazabilidad entre requisitos.'),
(182, 46, 'Un compilador de codigo',                                                                          0, 2, 'Incorrecto. El compilador es una herramienta de desarrollo, no de gestion.'),
(183, 46, 'Un editor de imagenes',                                                                           0, 3, 'Incorrecto. No es relevante para trazabilidad.'),
(184, 46, 'Una hoja de calculo cualquiera',                                                                  0, 4, 'Incorrecto. Aunque se puede usar informalmente, no es la herramienta adecuada.'),
(185, 47, 'Planificar - Hacer - Verificar - Actuar (ciclo de mejora continua)',                             1, 1, 'Correcto. PDCA es el ciclo clasico de mejora continua de Deming.'),
(186, 47, 'Disenar - Programar - Compilar - Depurar',                                                        0, 2, 'Incorrecto. Eso describe el ciclo de desarrollo de software.'),
(187, 47, 'Iniciar - Planificar - Ejecutar - Cerrar',                                                        0, 3, 'Incorrecto. Eso es el ciclo de vida del proyecto (PMBOK).'),
(188, 47, 'Descubrir - Disenar - Desarrollar - Deployar',                                                    0, 4, 'Incorrecto. Eso es parte de Design Thinking, no PDCA.'),
(189, 48, 'Implementar las mejoras identificadas y estandarizarlas',                                         1, 1, 'Correcto. Act es actuar: implementar y estandarizar lo aprendido.'),
(190, 48, 'Ejecutar el plan definido en la fase Do',                                                         0, 2, 'Incorrecto. Eso es la fase Do, no Act.'),
(191, 48, 'Definir los objetivos y el plan de mejora',                                                       0, 3, 'Incorrecto. Eso es la fase Plan, no Act.'),
(192, 48, 'Medir los resultados y compararlos con lo planificado',                                           0, 4, 'Incorrecto. Eso es la fase Check, no Act.');

-- =====================================================================
-- v2.2 - SEED: DATOS DE PRUEBA DEL MODELO ACTUAL (Actividades)
-- =====================================================================
-- Usuarios adicionales de prueba (misma clave que los originales)
INSERT INTO `usuario` (`id`, `nombre`, `correo`, `telefono`, `clave`, `rol`, `estado`) VALUES
(4, 'Docente de Prueba 2',   'docente2@admin.com',   '04151234567', '$2y$10$oK3yldrg4InTUDjaStF4Mur.ISh67t.3NBMw3STHiZOv1XBvXJs/C', 2, 1),
(5, 'Estudiante de Prueba 2','estudiante2@admin.com','04171234567', '$2y$10$0xfEzUHoKgP01UfrkggtGexUAkhEPbxz5WopyEyEBavMhEP4LSUda', 3, 1);

-- Periodo academico activo
INSERT INTO `periodo_academico` (`id`, `nombre`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(1, '2026-I', '2026-01-05', '2026-07-31', 1);

-- Materias del catalogo
INSERT INTO `materia` (`id`, `nombre`) VALUES
(1, 'Ingeniería de Requisitos'),
(2, 'Bases de Datos');

-- Que materias dicta cada docente (docente_materia)
INSERT INTO `docente_materia` (`id`, `id_docente`, `id_materia`) VALUES
(1, 2, 1),
(2, 4, 2);

-- Secciones del periodo 2026-I
INSERT INTO `seccion` (`id`, `nombre`, `id_periodo`) VALUES
(1, 'Sección A', 1),
(2, 'Sección B', 1);

-- Materias por seccion, cada una con su docente
-- Seccion A: Ing. de Requisitos (Docente de Prueba) + Bases de Datos (Docente de Prueba 2)
-- Seccion B: Ing. de Requisitos (Docente de Prueba)
INSERT INTO `seccion_materia` (`id`, `id_seccion`, `id_materia`, `id_docente`) VALUES
(1, 1, 1, 2),
(2, 1, 2, 4),
(3, 2, 1, 2);

-- Estudiantes inscritos
INSERT INTO `estudiante_seccion` (`id`, `id_estudiante`, `id_seccion`) VALUES
(1, 3, 1),
(2, 5, 2);

-- Actividades por materia (cada una ligada a una unidad creada por el admin)
INSERT INTO `actividad` (`id`, `id_materia`, `id_unidad`, `titulo`, `descripcion`, `id_nivel`, `estado`) VALUES
(1, 1, 1, 'Actividad 1 - Elicitación de Requisitos', 'Identifica las técnicas de elicitación y los entregables del proceso.', 1, 1),
(2, 1, 4, 'Actividad 2 - Modelado de Casos de Uso', 'Practica la identificación de actores y casos de uso.', 3, 1),
(3, 2, 9, 'Actividad 1 - Fundamentos de SQL', 'Evaluación básica de consultas sobre bases de datos.', 1, 1);

-- Asignacion de actividades a secciones
INSERT INTO `seccion_actividad` (`id`, `id_actividad`, `id_seccion`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 3, 1);

-- Partes de cada actividad
INSERT INTO `actividad_parte` (`id`, `id_actividad`, `orden`, `titulo`, `contenido_html`, `tiempo_limite_min`, `estado`) VALUES
(1, 1, 1, 'Introducción a la elicitación', '<p>La <b>elicitación</b> es la primera etapa de la ingeniería de requisitos. Su objetivo es descubrir las necesidades reales de los usuarios mediante técnicas como entrevistas, observación y prototipado.</p>', 10, 1),
(2, 1, 2, 'Validación de requisitos', '<p>Una vez elicitados, los requisitos deben <b>validarse</b> con el usuario para confirmar que responden a sus necesidades.</p>', 8, 1),
(3, 2, 1, 'Casos de uso', '<p>Un <b>caso de uso</b> describe una interacción entre un actor y el sistema para lograr un objetivo.</p>', 6, 1),
(4, 3, 1, 'Consultas básicas SQL', '<p><b>SELECT</b> es la sentencia que permite consultar información de una o varias tablas.</p>', 5, 1);

-- Preguntas de las evaluaciones
INSERT INTO `actividad_parte_pregunta` (`id`, `id_parte`, `enunciado`, `orden`, `puntos`) VALUES
(1, 1, '¿Cuál es la técnica más adecuada para conocer el flujo de trabajo actual de los usuarios?', 1, 1),
(2, 1, '¿Qué documento se genera al finalizar la etapa de elicitación?', 2, 1),
(3, 2, '¿Qué técnica permite validar los requisitos mostrando una versión preliminar del sistema?', 1, 1),
(4, 2, 'En la matriz de stakeholders, ¿qué eje mide la capacidad de influir en el proyecto?', 2, 1),
(5, 3, '¿Cómo se denomina la entidad externa que interactúa con el sistema en un diagrama de casos de uso?', 1, 1),
(6, 3, '¿Qué elemento del caso de uso indica el resultado esperado cuando el actor logra su objetivo?', 2, 1),
(7, 4, '¿Qué sentencia SQL se usa para consultar datos de una tabla?', 1, 1),
(8, 4, '¿Qué cláusula se utiliza para filtrar filas en una consulta?', 2, 1);

-- Opciones de respuesta (es_correcta = 1)
INSERT INTO `actividad_parte_opcion` (`id`, `id_pregunta`, `texto`, `es_correcta`, `orden`, `retroalimentacion`) VALUES
(1,  1, 'Observación directa',          0, 1, 'La observación ayuda, pero la entrevista estructurada suele dar más detalle del flujo actual.'),
(2,  1, 'Entrevista estructurada',      1, 2, 'Correcto. La entrevista estructurada permite indagar a fondo sobre el flujo de trabajo actual.'),
(3,  1, 'Prototipado',                  0, 3, 'El prototipado valida soluciones, no descubre el flujo actual.'),
(4,  1, 'Brainstorming',                0, 4, 'El brainstorming genera ideas, no documenta el flujo actual.'),
(5,  2, 'Manual de usuario',            0, 1, 'El manual se produce después de construir el sistema.'),
(6,  2, 'Plan de proyecto',             0, 2, 'El plan de proyecto pertenece a la gestión del proyecto, no a la elicitación.'),
(7,  2, 'Especificación de Requisitos', 1, 3, 'Correcto. La especificación de requisitos es el entregable principal de la etapa.'),
(8,  2, 'Diagrama de Gantt',            0, 4, 'El diagrama de Gantt es una herramienta de planificación.'),
(9,  3, 'Encuesta',                     0, 1, 'Las encuestas son útiles pero no muestran una versión preliminar del sistema.'),
(10, 3, 'Análisis de documentos',       0, 2, 'Esa técnica revisa documentos existentes.'),
(11, 3, 'Prototipado',                  1, 3, 'Correcto. El prototipado valida los requisitos con una versión preliminar.'),
(12, 3, 'Benchmarking',                 0, 4, 'El benchmarking compara con otros productos del mercado.'),
(13, 4, 'Costo vs Beneficio',           0, 1, 'Ese eje pertenece al análisis económico.'),
(14, 4, 'Poder vs Interés',             1, 2, 'Correcto. La matriz de stakeholders usa los ejes poder e interés.'),
(15, 4, 'Riesgo vs Impacto',            0, 3, 'Ese eje se usa en matrices de riesgo.'),
(16, 4, 'Alcance vs Tiempo',            0, 4, 'Esos son criterios de planificación del proyecto.'),
(17, 5, 'Clase',                        0, 1, 'Las clases pertenecen a los diagramas de clases, no de casos de uso.'),
(18, 5, 'Actor',                        1, 2, 'Correcto. El actor representa la entidad externa que interactúa con el sistema.'),
(19, 5, 'Entidad',                      0, 3, 'Las entidades pertenecen al modelo de datos.'),
(20, 5, 'Paquete',                      0, 4, 'Los paquetes agrupan elementos, no representan actores.'),
(21, 6, 'Precondiciones',               0, 1, 'Las precondiciones indican el estado inicial.'),
(22, 6, 'Flujo principal',              0, 2, 'El flujo principal describe los pasos de la interacción.'),
(23, 6, 'Garantía de éxito',            1, 3, 'Correcto. La garantía de éxito describe el resultado que el actor espera.'),
(24, 6, 'Extensiones',                  0, 4, 'Las extensiones describen flujos alternativos.'),
(25, 7, 'SELECT',                       1, 1, 'Correcto. SELECT consulta datos de una tabla.'),
(26, 7, 'INSERT',                       0, 2, 'INSERT agrega filas, no consulta.'),
(27, 7, 'UPDATE',                       0, 3, 'UPDATE modifica filas existentes.'),
(28, 7, 'DELETE',                       0, 4, 'DELETE elimina filas.'),
(29, 8, 'ORDER BY',                     0, 1, 'ORDER BY ordena los resultados, no los filtra.'),
(30, 8, 'WHERE',                        1, 2, 'Correcto. WHERE filtra las filas de una consulta.'),
(31, 8, 'GROUP BY',                     0, 3, 'GROUP BY agrupa resultados.'),
(32, 8, 'HAVING',                       0, 4, 'HAVING filtra grupos, se usa con GROUP BY.');

-- =====================================================================
-- v2.3 - SEED: UNIDADES DE PRUEBA (Gestor de Contenido, modelo legacy)
-- =====================================================================
INSERT INTO `unidad` (`id`, `titulo`, `descripcion`, `id_materia`, `id_nivel`, `orden`, `estado`, `contenido_html`) VALUES
(1, 'Introducción a la Ingeniería de Requisitos',
 'Qué es la ingeniería de requisitos y su importancia en el desarrollo de software.',
 1, 1, 1, 1,
 '<p>La <b>Ingeniería de Requisitos</b> (IR) es la disciplina que se encarga de descubrir, documentar y mantener los requisitos de un sistema. Una buena gestión de requisitos reduce errores costosos en etapas tardías del proyecto.</p>'),
(2, 'El proceso de desarrollo de software',
 'Ciclos de vida del software y dónde encaja la IR.',
 1, 1, 2, 1,
 '<p>En el ciclo de vida clásico, la IR se ubica en las etapas de <b>análisis y especificación</b>, antes del diseño y la construcción. En metodologías ágiles se realiza de forma iterativa.</p>'),
(3, 'Técnicas de Elicitación de Requisitos',
 'Entrevistas, observación, prototipado y otras técnicas.',
 1, 2, 1, 1,
 '<p>La <b>elicitación</b> reúne las necesidades reales de los usuarios mediante técnicas como entrevistas, grupos focales, observación directa y prototipado.</p>'),
(4, 'Análisis y Modelado de Requisitos',
 'Casos de uso, historias de usuario y diagramas.',
 1, 2, 2, 1,
 '<p>El <b>modelado</b> organiza los requisitos elicitados. Los <b>casos de uso</b> describen interacciones actor-sistema y las historias de usuario expresan necesidades desde la perspectiva del usuario.</p>'),
(5, 'Especificación de Requisitos',
 'Cómo documentar requisitos funcionales y no funcionales.',
 1, 3, 1, 1,
 '<p>La <b>especificación</b> documenta los requisitos de forma clara, medible y verificable. Los requisitos funcionales describen comportamientos; los no funcionales, atributos como rendimiento y seguridad.</p>'),
(6, 'Gestión de Requisitos y Trazabilidad',
 'Priorización, cambios y trazabilidad.',
 1, 3, 2, 1,
 '<p>La <b>gestión de requisitos</b> controla los cambios y mantiene la <b>trazabilidad</b> desde el origen hasta la implementación y las pruebas.</p>'),
(7, 'Validación y Verificación de Requisitos',
 'Confirmar que se construye el producto correcto.',
 1, 4, 1, 1,
 '<p>La <b>validación</b> confirma que los requisitos reflejan las necesidades del usuario; la <b>verificación</b> confirma que el producto cumple los requisitos especificados.</p>'),
(8, 'Calidad del Software y Mejora Continua',
 'Estándares, revisiones y el ciclo PDCA.',
 1, 4, 2, 1,
 '<p>La calidad se asegura con <b>revisiones, estándares</b> y el ciclo de <b>mejora continua</b> (Planificar-Hacer-Verificar-Actuar).</p>'),
(9, 'Fundamentos de Bases de Datos',
 'Introducción a las bases de datos relacionales.',
 2, 1, 1, 1,
 '<p>Una <b>base de datos</b> relacional organiza la información en <b>tablas</b> relacionadas entre sí mediante claves.</p>'),
(10, 'Modelo Relacional y SQL',
 'Tablas, claves primarias/foráneas y consultas.',
 2, 2, 2, 1,
 '<p><b>SQL</b> es el lenguaje estándar para consultar y manipular datos. <b>SELECT</b> recupera información y <b>WHERE</b> filtra filas.</p>');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
