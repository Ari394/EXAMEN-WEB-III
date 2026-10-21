-- Esquema adicional para las carpetas de MyBOX.
-- Ejecutar en la base de datos mybox que ya contiene la tabla usuarios.

CREATE TABLE IF NOT EXISTS carpetas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario VARCHAR(15) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    creada_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_carpetas_usuario_nombre (usuario, nombre),
    KEY idx_carpetas_usuario (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS carpetas_compartidas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    carpeta_id INT UNSIGNED NOT NULL,
    usuario_compartido VARCHAR(15) NOT NULL,
    compartida_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_carpeta_usuario_compartido (carpeta_id, usuario_compartido),
    KEY idx_compartidas_usuario (usuario_compartido),
    CONSTRAINT fk_compartidas_carpeta
        FOREIGN KEY (carpeta_id) REFERENCES carpetas (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
