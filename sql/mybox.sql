
-- Esquema de carpetas y carpetas compartidas de myBox.
-- Requiere la tabla usuarios(id INT AUTO_INCREMENT PRIMARY KEY).

USE mybox;

-- Carpetas y subcarpetas
CREATE TABLE IF NOT EXISTS carpetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario_id INT NOT NULL,
    carpeta_padre_id INT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_carpetas_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_carpetas_padre
        FOREIGN KEY (carpeta_padre_id)
        REFERENCES carpetas(id)
        ON DELETE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- Carpetas compartidas
CREATE TABLE IF NOT EXISTS carpetas_compartidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    carpeta_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_compartido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_carpeta_usuario (
        carpeta_id,
        usuario_id
    ),

    CONSTRAINT fk_compartidas_carpeta
        FOREIGN KEY (carpeta_id)
        REFERENCES carpetas(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_compartidas_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;
