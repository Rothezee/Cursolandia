CREATE DATABASE IF NOT EXISTS cursolandia
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE cursolandia;

DROP TABLE IF EXISTS notificaciones;
DROP TABLE IF EXISTS respuestas;
DROP TABLE IF EXISTS preguntas;
DROP TABLE IF EXISTS curso_etiquetas;
DROP TABLE IF EXISTS materiales;
DROP TABLE IF EXISTS inscripciones;
DROP TABLE IF EXISTS etiquetas;
DROP TABLE IF EXISTS cursos;
DROP TABLE IF EXISTS solicitudes_pro;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  email VARCHAR(191) NOT NULL,
  telefono VARCHAR(50) DEFAULT NULL,
  usuario VARCHAR(50) NOT NULL,
  intereses TEXT DEFAULT NULL,
  antecedentes TEXT DEFAULT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  rol ENUM('usuario', 'admin') NOT NULL DEFAULT 'usuario',
  plan ENUM('gratuito', 'pro') NOT NULL DEFAULT 'gratuito',
  plan_vencimiento DATE DEFAULT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_usuarios_email (email),
  UNIQUE KEY uk_usuarios_usuario (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE solicitudes_pro (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NOT NULL,
  comprobante VARCHAR(255) NOT NULL,
  estado ENUM('pendiente', 'aprobada', 'rechazada') NOT NULL DEFAULT 'pendiente',
  fecha_solicitud TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_resolucion DATE DEFAULT NULL,
  KEY idx_solicitudes_usuario (usuario_id, estado),
  CONSTRAINT fk_solicitudes_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cursos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  creador_id INT UNSIGNED NOT NULL,
  titulo VARCHAR(150) NOT NULL,
  descripcion TEXT NOT NULL,
  costo DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  acceso ENUM('publico', 'privado') NOT NULL DEFAULT 'publico',
  cupo INT UNSIGNED DEFAULT NULL,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_cursos_creador (creador_id),
  KEY idx_cursos_fechas (fecha_inicio, fecha_fin),
  CONSTRAINT fk_cursos_creador
    FOREIGN KEY (creador_id) REFERENCES usuarios (id),
  CONSTRAINT chk_cursos_costo CHECK (costo >= 0),
  CONSTRAINT chk_cursos_fechas CHECK (fecha_fin >= fecha_inicio),
  CONSTRAINT chk_cursos_cupo CHECK (cupo IS NULL OR cupo > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE etiquetas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,
  UNIQUE KEY uk_etiquetas_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE curso_etiquetas (
  curso_id INT UNSIGNED NOT NULL,
  etiqueta_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (curso_id, etiqueta_id),
  KEY idx_curso_etiquetas_etiqueta (etiqueta_id),
  CONSTRAINT fk_curso_etiquetas_curso
    FOREIGN KEY (curso_id) REFERENCES cursos (id) ON DELETE CASCADE,
  CONSTRAINT fk_curso_etiquetas_etiqueta
    FOREIGN KEY (etiqueta_id) REFERENCES etiquetas (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE materiales (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  curso_id INT UNSIGNED NOT NULL,
  nombre VARCHAR(150) NOT NULL,
  ruta VARCHAR(255) NOT NULL,
  tamano INT UNSIGNED NOT NULL,
  subido_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_materiales_curso (curso_id),
  CONSTRAINT fk_materiales_curso
    FOREIGN KEY (curso_id) REFERENCES cursos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE inscripciones (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  curso_id INT UNSIGNED NOT NULL,
  usuario_id INT UNSIGNED NOT NULL,
  estado ENUM('pendiente', 'confirmada') NOT NULL DEFAULT 'pendiente',
  fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_inscripciones_curso_usuario (curso_id, usuario_id),
  KEY idx_inscripciones_usuario (usuario_id, estado),
  CONSTRAINT fk_inscripciones_curso
    FOREIGN KEY (curso_id) REFERENCES cursos (id) ON DELETE CASCADE,
  CONSTRAINT fk_inscripciones_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE preguntas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  curso_id INT UNSIGNED NOT NULL,
  autor_id INT UNSIGNED NOT NULL,
  titulo VARCHAR(150) NOT NULL,
  cuerpo TEXT NOT NULL,
  destacada TINYINT(1) NOT NULL DEFAULT 0,
  estado ENUM('abierta', 'cerrada') NOT NULL DEFAULT 'abierta',
  creada_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_preguntas_curso (curso_id),
  KEY idx_preguntas_autor (autor_id),
  CONSTRAINT fk_preguntas_curso
    FOREIGN KEY (curso_id) REFERENCES cursos (id) ON DELETE CASCADE,
  CONSTRAINT fk_preguntas_autor
    FOREIGN KEY (autor_id) REFERENCES usuarios (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE respuestas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pregunta_id INT UNSIGNED NOT NULL,
  autor_id INT UNSIGNED NOT NULL,
  cuerpo TEXT NOT NULL,
  aceptada TINYINT(1) NOT NULL DEFAULT 0,
  creada_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_respuestas_pregunta (pregunta_id),
  KEY idx_respuestas_autor (autor_id),
  CONSTRAINT fk_respuestas_pregunta
    FOREIGN KEY (pregunta_id) REFERENCES preguntas (id) ON DELETE CASCADE,
  CONSTRAINT fk_respuestas_autor
    FOREIGN KEY (autor_id) REFERENCES usuarios (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notificaciones (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NOT NULL,
  mensaje VARCHAR(255) NOT NULL,
  enlace VARCHAR(255) DEFAULT NULL,
  leida TINYINT(1) NOT NULL DEFAULT 0,
  creada_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_notificaciones_usuario (usuario_id, leida),
  CONSTRAINT fk_notificaciones_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- Todos los usuarios usan la contraseña: demo1234
INSERT INTO usuarios
  (id, nombre, apellido, email, telefono, usuario, intereses, antecedentes, avatar, password, rol, plan, plan_vencimiento)
VALUES
  (1, 'Administrador', 'Cursolandia', 'admin@cursolandia.local', NULL, 'admin',
   'Gestión de la plataforma', 'Cuenta de prueba con rol administrador', NULL,
   '$2y$10$/oUig4dMIqofh6iVj7F8ke4K3dMf9NueAf5g.FdD17Ah8BibnrYqK', 'admin', 'gratuito', NULL),

  (2, 'María', 'López', 'maria@cursolandia.local', '2664000001', 'maria',
   'php, mysql, backend', 'Docente de programación', 'img/avatars/maria.png',
   '$2y$10$/oUig4dMIqofh6iVj7F8ke4K3dMf9NueAf5g.FdD17Ah8BibnrYqK', 'usuario', 'gratuito', NULL),

  (3, 'Carlos', 'Ruiz', 'carlos@cursolandia.local', '2664000002', 'carlos',
   'css, ui, frontend', 'Diseñador de interfaces', 'img/avatars/carlos.png',
   '$2y$10$/oUig4dMIqofh6iVj7F8ke4K3dMf9NueAf5g.FdD17Ah8BibnrYqK', 'usuario', 'pro', '2026-12-31'),

  (4, 'Lucía', 'Mena', 'lucia@cursolandia.local', '2664000003', 'lucia',
   'php, foro, accesibilidad', 'Estudiante de la Tecnicatura Web', 'img/avatars/lucia.png',
   '$2y$10$/oUig4dMIqofh6iVj7F8ke4K3dMf9NueAf5g.FdD17Ah8BibnrYqK', 'usuario', 'gratuito', NULL),

  (5, 'Diego', 'Soto', 'diego@cursolandia.local', '2664000004', 'diego',
   'javascript, mysql, frontend', 'Desarrollador freelance', 'img/avatars/diego.png',
   '$2y$10$/oUig4dMIqofh6iVj7F8ke4K3dMf9NueAf5g.FdD17Ah8BibnrYqK', 'usuario', 'pro', '2027-03-31');

INSERT INTO solicitudes_pro
  (usuario_id, comprobante, estado, fecha_solicitud, fecha_resolucion)
VALUES
  (2, 'uploads/comprobantes/maria.pdf', 'pendiente', '2026-11-01 10:00:00', NULL),
  (3, 'uploads/comprobantes/carlos.pdf', 'aprobada', '2026-10-15 09:30:00', '2026-10-16');

INSERT INTO cursos
  (id, creador_id, titulo, descripcion, costo, acceso, cupo, fecha_inicio, fecha_fin)
VALUES
  (1, 2, 'Introducción a PHP y MySQL',
   'Fundamentos del backend con PHP procedural y consultas seguras a MySQL. Recorré formularios, sesiones, consultas preparadas y buenas prácticas de seguridad.',
   0.00, 'publico', 40, '2026-03-01', '2026-12-15'),

  (2, 3, 'Diseño de interfaces modernas',
   'Layouts responsivos, glassmorphism y sistemas de diseño coherentes con CSS actual. Construí interfaces accesibles con variables CSS, grids y componentes reutilizables.',
   15000.00, 'publico', NULL, '2026-04-15', '2026-12-20'),

  (3, 2, 'Foros Q&A en aplicaciones web',
   'Patrones para preguntas, respuestas aceptadas, moderación docente y notificaciones. Caso de estudio directo del enunciado de Cursolandia.',
   0.00, 'publico', 30, '2026-03-10', '2026-12-01'),

  (4, 3, 'JavaScript para el DOM',
   'Manipulación del DOM, consumo de JSON y renderizado dinámico con JavaScript vanilla. Curso privado para alumnos ya inscriptos.',
   0.00, 'privado', 15, '2026-05-01', '2026-09-30');

INSERT INTO etiquetas (id, nombre) VALUES
  (1, 'php'),
  (2, 'mysql'),
  (3, 'backend'),
  (4, 'css'),
  (5, 'ui'),
  (6, 'frontend'),
  (7, 'foro'),
  (8, 'comunidad'),
  (9, 'javascript'),
  (10, 'accesibilidad');

INSERT INTO curso_etiquetas (curso_id, etiqueta_id) VALUES
  (1, 1), (1, 2), (1, 3),
  (2, 4), (2, 5), (2, 6),
  (3, 7), (3, 8), (3, 3),
  (4, 9), (4, 6);

INSERT INTO materiales (curso_id, nombre, ruta, tamano) VALUES
  (1, 'Guía PHP — módulo 1.pdf', 'uploads/cursos/1/guia-php.pdf', 1258291),
  (1, 'Script base de datos.sql', 'uploads/cursos/1/base-datos.sql', 49152),
  (2, 'Design tokens Cursolandia.fig', 'uploads/cursos/2/design-tokens.fig', 2202009),
  (3, 'Diagrama flujo Q&A.png', 'uploads/cursos/3/flujo-qa.png', 245760);

INSERT INTO inscripciones (curso_id, usuario_id, estado, fecha) VALUES
  (1, 4, 'confirmada', '2026-03-05 18:00:00'),
  (1, 5, 'confirmada', '2026-03-06 11:00:00'),
  (2, 5, 'pendiente', '2026-11-05 20:15:00'),
  (3, 4, 'confirmada', '2026-03-12 09:00:00');

INSERT INTO preguntas
  (id, curso_id, autor_id, titulo, cuerpo, destacada, estado, creada_en)
VALUES
  (1, 1, 4, '¿Cómo preparo statements con PDO?',
   'Tengo dudas con bindParam y los tipos cuando la consulta tiene varios placeholders.',
   1, 'cerrada', '2026-03-10 14:00:00'),

  (2, 3, 5, 'Regla de no responder dos veces seguidas',
   '¿Cómo se implementa en el backend para evitar spam en un hilo?',
   0, 'abierta', '2026-03-11 10:30:00');

INSERT INTO respuestas (pregunta_id, autor_id, cuerpo, aceptada, creada_en) VALUES
  (1, 2, 'Usá bindValue si pasás variables en un loop; bindParam enlaza por referencia y puede sorprenderte.',
   0, '2026-03-10 15:00:00'),
  (1, 5, 'Te dejo un ejemplo mínimo con prepare, execute y fetchAll asociativo.',
   1, '2026-03-11 09:00:00'),
  (1, 4, 'Perfecto, con bindValue me funcionó. ¡Gracias!',
   0, '2026-03-11 12:00:00'),
  (2, 4, 'Se puede comparar el autor de la última respuesta antes de insertar la nueva.',
   0, '2026-03-11 11:00:00');

INSERT INTO notificaciones (usuario_id, mensaje, enlace, leida, creada_en) VALUES
  (4, 'Tu respuesta fue aceptada en «¿Cómo preparo statements con PDO?».', 'src/foro-hilo.php?id=1', 0, '2026-03-11 09:05:00'),
  (2, 'Nueva respuesta en «¿Cómo preparo statements con PDO?».', 'src/foro-hilo.php?id=1', 1, '2026-03-11 09:05:00'),
  (5, 'Tu inscripción al curso «Diseño de interfaces modernas» está pendiente de confirmación.', 'src/curso.php?id=2', 0, '2026-11-05 20:15:00');
