-- Cursolandia — esquema básico para pruebas locales (XAMPP / MySQL)
-- Usuario de prueba: admin / admin

CREATE DATABASE IF NOT EXISTS cursolandia
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE cursolandia;

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
  plan ENUM('gratuito', 'pro') NOT NULL DEFAULT 'gratuito',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_usuarios_email (email),
  UNIQUE KEY uk_usuarios_usuario (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO usuarios (
  nombre, apellido, email, telefono, usuario,
  intereses, antecedentes, avatar, password, plan
) VALUES (
  'Administrador',
  'Cursolandia',
  'admin@cursolandia.local',
  NULL,
  'admin',
  'Gestión de la plataforma',
  'Cuenta de prueba para desarrollo',
  NULL,
  'admin',
  'pro'
);
