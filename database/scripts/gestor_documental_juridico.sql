-- =============================================================================
-- Gestor Documental Jurídico (SGDJ)
-- Script de creación de la base de datos — Fase 2 (aprobada)
-- Motor: MySQL / MariaDB · InnoDB · utf8mb4 / utf8mb4_unicode_ci
-- Ejecutar con:  mysql -u root -p < gestor_documental_juridico.sql
-- Orden de creación: roles, usuarios, clientes, tipos_proceso,
--                    estados_proceso, procesos, tipos_documento, documentos
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `gestor_documental_juridico`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `gestor_documental_juridico`;

-- -----------------------------------------------------------------------------
-- 1. roles
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(50)  NOT NULL,
    `descripcion` VARCHAR(255) NULL DEFAULT NULL,
    `estado`      TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_roles_nombre` (`nombre`),
    CONSTRAINT `chk_roles_estado` CHECK (`estado` IN (0, 1))
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. usuarios
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `rol_id`     INT UNSIGNED NOT NULL,
    `nombres`    VARCHAR(100) NOT NULL,
    `apellidos`  VARCHAR(100) NOT NULL,
    `correo`     VARCHAR(150) NOT NULL,
    `password`   VARCHAR(255) NOT NULL,
    `estado`     TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_usuarios_correo` (`correo`),
    KEY `idx_usuarios_rol_id` (`rol_id`),
    KEY `idx_usuarios_estado` (`estado`),
    CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`)
        REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `chk_usuarios_estado` CHECK (`estado` IN (0, 1))
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. clientes
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientes` (
    `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tipo_cliente`        ENUM('natural', 'juridica') NOT NULL,
    `tipo_identificacion` VARCHAR(20)  NOT NULL,
    `numero_identificacion` VARCHAR(40) NOT NULL,
    `nombres`             VARCHAR(100) NULL DEFAULT NULL,
    `apellidos`           VARCHAR(100) NULL DEFAULT NULL,
    `razon_social`        VARCHAR(200) NULL DEFAULT NULL,
    `correo`              VARCHAR(150) NULL DEFAULT NULL,
    `telefono`            VARCHAR(30)  NULL DEFAULT NULL,
    `direccion`           VARCHAR(255) NULL DEFAULT NULL,
    `estado`              TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_clientes_identificacion` (`tipo_identificacion`, `numero_identificacion`),
    KEY `idx_clientes_numero_identificacion` (`numero_identificacion`),
    KEY `idx_clientes_estado` (`estado`),
    CONSTRAINT `chk_clientes_estado` CHECK (`estado` IN (0, 1))
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. tipos_proceso
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tipos_proceso` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(100) NOT NULL,
    `descripcion` VARCHAR(255) NULL DEFAULT NULL,
    `estado`      TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_tipos_proceso_nombre` (`nombre`),
    CONSTRAINT `chk_tipos_proceso_estado` CHECK (`estado` IN (0, 1))
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. estados_proceso
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `estados_proceso` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(100) NOT NULL,
    `descripcion` VARCHAR(255) NULL DEFAULT NULL,
    `estado`      TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_estados_proceso_nombre` (`nombre`),
    CONSTRAINT `chk_estados_proceso_estado` CHECK (`estado` IN (0, 1))
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. procesos
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `procesos` (
    `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cliente_id`          INT UNSIGNED NOT NULL,
    `tipo_proceso_id`     INT UNSIGNED NOT NULL,
    `estado_proceso_id`   INT UNSIGNED NOT NULL,
    `responsable_id`      INT UNSIGNED NOT NULL,
    `numero_proceso`      VARCHAR(50)  NOT NULL,
    `titulo`              VARCHAR(200) NOT NULL,
    `descripcion`         TEXT         NULL,
    `fecha_inicio`        DATETIME     NOT NULL,
    `fecha_finalizacion`  DATETIME     NULL DEFAULT NULL,
    `created_at`          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_procesos_numero_proceso` (`numero_proceso`),
    KEY `idx_procesos_cliente_id` (`cliente_id`),
    KEY `idx_procesos_tipo_proceso_id` (`tipo_proceso_id`),
    KEY `idx_procesos_estado_proceso_id` (`estado_proceso_id`),
    KEY `idx_procesos_responsable_id` (`responsable_id`),
    KEY `idx_procesos_fecha_inicio` (`fecha_inicio`),
    CONSTRAINT `fk_procesos_cliente` FOREIGN KEY (`cliente_id`)
        REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_procesos_tipo_proceso` FOREIGN KEY (`tipo_proceso_id`)
        REFERENCES `tipos_proceso` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_procesos_estado_proceso` FOREIGN KEY (`estado_proceso_id`)
        REFERENCES `estados_proceso` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_procesos_responsable` FOREIGN KEY (`responsable_id`)
        REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `chk_procesos_fechas` CHECK (
        `fecha_finalizacion` IS NULL OR `fecha_finalizacion` >= `fecha_inicio`
    )
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. tipos_documento
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tipos_documento` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre`      VARCHAR(100) NOT NULL,
    `descripcion` VARCHAR(255) NULL DEFAULT NULL,
    `estado`      TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_tipos_documento_nombre` (`nombre`),
    CONSTRAINT `chk_tipos_documento_estado` CHECK (`estado` IN (0, 1))
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. documentos
-- Nota: el archivo físico NO se almacena como BLOB; la tabla guarda
-- únicamente metadatos y la ubicación del archivo en storage/documentos.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `documentos` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `proceso_id`        INT UNSIGNED NOT NULL,
    `tipo_documento_id` INT UNSIGNED NOT NULL,
    `usuario_id`        INT UNSIGNED NOT NULL,
    `nombre`            VARCHAR(200) NOT NULL,
    `descripcion`       TEXT         NULL,
    `nombre_archivo`    VARCHAR(255) NOT NULL,
    `ruta_archivo`      VARCHAR(255) NOT NULL,
    `extension`         VARCHAR(10)  NOT NULL,
    `tamano`            INT UNSIGNED NOT NULL,
    `fecha_documento`   DATETIME     NOT NULL,
    `estado`            TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_documentos_proceso_id` (`proceso_id`),
    KEY `idx_documentos_tipo_documento_id` (`tipo_documento_id`),
    KEY `idx_documentos_usuario_id` (`usuario_id`),
    KEY `idx_documentos_fecha_documento` (`fecha_documento`),
    KEY `idx_documentos_estado` (`estado`),
    CONSTRAINT `fk_documentos_proceso` FOREIGN KEY (`proceso_id`)
        REFERENCES `procesos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_documentos_tipo_documento` FOREIGN KEY (`tipo_documento_id`)
        REFERENCES `tipos_documento` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_documentos_usuario` FOREIGN KEY (`usuario_id`)
        REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `chk_documentos_estado` CHECK (`estado` IN (0, 1))
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- =============================================================================
-- DATOS INICIALES (catálogos)
-- =============================================================================

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
    (1, 'Administrador', 'Acceso total a la administración del sistema'),
    (2, 'Abogado', 'Gestión de clientes, procesos, documentos y búsquedas');

INSERT INTO `tipos_proceso` (`id`, `nombre`, `descripcion`) VALUES
    (1, 'Civil', 'Asuntos de naturaleza civil'),
    (2, 'Laboral', 'Asuntos de naturaleza laboral'),
    (3, 'Familia', 'Asuntos de derecho de familia'),
    (4, 'Administrativo', 'Asuntos de naturaleza administrativa');

INSERT INTO `estados_proceso` (`id`, `nombre`, `descripcion`) VALUES
    (1, 'Activo', 'Proceso en trámite'),
    (2, 'Suspendido', 'Proceso suspendido temporalmente'),
    (3, 'Finalizado', 'Proceso culminado'),
    (4, 'Archivado', 'Proceso archivado');

INSERT INTO `tipos_documento` (`id`, `nombre`, `descripcion`) VALUES
    (1, 'Demanda', 'Escrito con el que se inicia el proceso'),
    (2, 'Contrato', 'Documento contractual'),
    (3, 'Poder', 'Poder otorgado para representación'),
    (4, 'Sentencia', 'Decisión judicial'),
    (5, 'Notificación', 'Notificación judicial o administrativa'),
    (6, 'Prueba', 'Medio de prueba'),
    (7, 'Acta', 'Acta de audiencia o diligencia'),
    (8, 'Otro', 'Otro tipo de documento');

-- -----------------------------------------------------------------------------
-- Usuario administrador inicial (credenciales por defecto, se documentan en el README)
-- correo: admin@sgdj.local   contraseña: admin123
-- La contraseña se almacena con password_hash() (bcrypt).
-- -----------------------------------------------------------------------------
INSERT INTO `usuarios` (`rol_id`, `nombres`, `apellidos`, `correo`, `password`)
VALUES (1, 'Administrador', 'Sistema', 'admin@sgdj.local', '$2y$10$SCNRHFLaLhQXrGu5t5woRu.4wlqdD8NqAeN7RfzmH4m..8wsXvsoO');