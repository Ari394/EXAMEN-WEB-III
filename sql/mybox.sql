CREATE DATABASE IF NOT EXISTS mybox
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE mybox;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS carpetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario_id INT NOT NULL,
    carpeta_padre_id INT NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_carpetas_usuario_padre (usuario_id, carpeta_padre_id),
    CONSTRAINT fk_carpetas_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_carpetas_padre
        FOREIGN KEY (carpeta_padre_id) REFERENCES carpetas(id)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS archivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    comentario VARCHAR(50) NOT NULL DEFAULT '',
    tipo VARCHAR(100) NOT NULL,
    tamano BIGINT UNSIGNED NOT NULL,
    contenido LONGBLOB NOT NULL,
    fecha_subida TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT NOT NULL,
    carpeta_id INT NULL,
    INDEX idx_archivos_usuario_carpeta (usuario_id, carpeta_id),
    CONSTRAINT fk_archivos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_archivos_carpeta
        FOREIGN KEY (carpeta_id) REFERENCES carpetas(id)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS carpetas_compartidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    carpeta_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_compartido TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_carpeta_usuario (carpeta_id, usuario_id),
    INDEX idx_compartidas_usuario (usuario_id),
    CONSTRAINT fk_compartidas_carpeta
        FOREIGN KEY (carpeta_id) REFERENCES carpetas(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_compartidas_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
