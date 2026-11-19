-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-10-2026 a las 00:37:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `unm_aulas`
--
CREATE DATABASE IF NOT EXISTS `unm_aulas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `unm_aulas`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aulas`
--

CREATE TABLE `aulas` (
  `id` varchar(30) NOT NULL,
  `edificio_id` varchar(20) NOT NULL,
  `piso` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `etiqueta` varchar(30) NOT NULL,
  `ancho` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `tipo` enum('aula','sum','info') NOT NULL DEFAULT 'aula',
  `compu` tinyint(1) NOT NULL DEFAULT 0,
  `detalle` varchar(150) DEFAULT NULL,
  `horario_texto` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `aulas`
--

INSERT INTO `aulas` (`id`, `edificio_id`, `piso`, `orden`, `etiqueta`, `ancho`, `tipo`, `compu`, `detalle`, `horario_texto`) VALUES
('d1-01-03', 'daract1', 0, 1, '01-03', 2, 'aula', 0, NULL, NULL),
('d1-02', 'daract1', 0, 2, '02', 1, 'aula', 0, NULL, NULL),
('d1-04', 'daract1', 0, 5, '04', 1, 'aula', 0, NULL, NULL),
('d1-05-07', 'daract1', 0, 3, '05-07', 2, 'aula', 0, NULL, NULL),
('d1-06', 'daract1', 0, 4, '06', 1, 'aula', 0, NULL, NULL),
('d1-08', 'daract1', 0, 6, '08', 1, 'aula', 0, NULL, NULL),
('d1-09-11', 'daract1', 1, 1, '09-11', 2, 'aula', 0, NULL, NULL),
('d1-10', 'daract1', 1, 2, '10', 1, 'aula', 0, NULL, NULL),
('d1-12', 'daract1', 1, 5, '12', 1, 'aula', 0, NULL, NULL),
('d1-13-15', 'daract1', 1, 3, '13-15', 2, 'aula', 0, NULL, NULL),
('d1-14', 'daract1', 1, 4, '14', 1, 'aula', 0, NULL, NULL),
('d1-16', 'daract1', 1, 6, '16', 1, 'aula', 0, NULL, NULL),
('d1-17', 'daract1', 1, 7, '17', 1, 'aula', 0, NULL, NULL),
('d1-18', 'daract1', 1, 8, '18', 1, 'aula', 0, NULL, NULL),
('d1-19', 'daract1', 1, 9, '19', 1, 'aula', 0, NULL, NULL),
('d1-biblioteca', 'daract1', 0, 7, 'Biblioteca', 1, 'info', 0, NULL, 'Lun a Vie 8:00 - 21:00 · Sáb 8:00 - 14:00'),
('d2-01-03', 'daract2', 0, 1, '01-03', 2, 'aula', 0, NULL, NULL),
('d2-02', 'daract2', 0, 2, '02', 1, 'aula', 0, NULL, NULL),
('d2-04', 'daract2', 0, 3, '04', 1, 'aula', 0, NULL, NULL),
('d2-05', 'daract2', 0, 4, '05', 1, 'aula', 0, NULL, NULL),
('d2-06', 'daract2', 0, 5, '06', 1, 'aula', 0, NULL, NULL),
('d2-07', 'daract2', 1, 1, '07', 1, 'aula', 0, NULL, NULL),
('d2-08', 'daract2', 0, 6, '08', 1, 'aula', 0, NULL, NULL),
('d2-09', 'daract2', 1, 2, '09', 1, 'aula', 0, NULL, NULL),
('d2-10', 'daract2', 1, 3, '10', 1, 'aula', 0, NULL, NULL),
('d2-11-13', 'daract2', 1, 4, '11-13', 2, 'aula', 0, NULL, NULL),
('d2-12', 'daract2', 1, 5, '12', 1, 'aula', 0, NULL, NULL),
('d2-14', 'daract2', 1, 8, '14', 1, 'aula', 0, NULL, NULL),
('d2-15-17', 'daract2', 1, 6, '15-17', 2, 'aula', 0, NULL, NULL),
('d2-16', 'daract2', 1, 7, '16', 1, 'aula', 0, NULL, NULL),
('d2-sum', 'daract2', 0, 7, 'SUM', 1, 'sum', 0, 'Salón de usos múltiples', NULL),
('do-01', 'dorrego1', 0, 1, '01', 1, 'aula', 0, NULL, NULL),
('do-02', 'dorrego1', 0, 2, '02', 1, 'aula', 0, NULL, NULL),
('do-03', 'dorrego1', 0, 3, '03', 1, 'aula', 1, NULL, NULL),
('do-04', 'dorrego1', 0, 4, '04', 1, 'aula', 0, NULL, NULL),
('do-05', 'dorrego1', 0, 5, '05', 1, 'aula', 0, NULL, NULL),
('do-06', 'dorrego1', 0, 6, '06', 1, 'aula', 0, NULL, NULL),
('do-07', 'dorrego1', 0, 7, '07', 1, 'aula', 0, NULL, NULL),
('do-08', 'dorrego1', 0, 8, '08', 1, 'aula', 0, NULL, NULL),
('do-09', 'dorrego1', 1, 1, '09', 1, 'aula', 0, NULL, NULL),
('do-10', 'dorrego1', 1, 2, '10', 1, 'aula', 0, NULL, NULL),
('do-11', 'dorrego1', 1, 3, '11', 1, 'aula', 0, NULL, NULL),
('do-12', 'dorrego1', 1, 4, '12', 1, 'aula', 0, NULL, NULL),
('do-13', 'dorrego1', 1, 5, '13', 1, 'aula', 0, NULL, NULL),
('do-14', 'dorrego1', 1, 6, '14', 1, 'aula', 1, NULL, NULL),
('do-15', 'dorrego1', 1, 7, '15', 1, 'aula', 1, NULL, NULL),
('do-16', 'dorrego1', 1, 8, '16', 1, 'aula', 1, NULL, NULL),
('do-17', 'dorrego1', 1, 9, '17', 1, 'aula', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

CREATE TABLE `carreras` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carreras`
--

INSERT INTO `carreras` (`id`, `nombre`) VALUES
(13, 'Abogacía'),
(4, 'Arquitectura'),
(12, 'Contador Público Nacional'),
(6, 'Diseño de Indumentaria'),
(7, 'Diseño en Comunicación Visual'),
(5, 'Diseño Industrial'),
(8, 'Diseño Multimedial'),
(1, 'Ingeniería Electrónica'),
(9, 'Lic. en Administración'),
(3, 'Lic. en Biotecnología'),
(15, 'Lic. en Comunicación Social'),
(11, 'Lic. en Economía'),
(2, 'Lic. en Gestión Ambiental'),
(10, 'Lic. en Relaciones del Trabajo'),
(14, 'Lic. en Trabajo Social');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comisiones`
--

CREATE TABLE `comisiones` (
  `id` int(10) UNSIGNED NOT NULL,
  `materia_id` int(10) UNSIGNED NOT NULL,
  `docente_id` int(10) UNSIGNED DEFAULT NULL,
  `numero` varchar(20) NOT NULL,
  `cuatrimestre` tinyint(3) UNSIGNED NOT NULL,
  `anio` smallint(5) UNSIGNED NOT NULL
) ;

--
-- Volcado de datos para la tabla `comisiones`
--

INSERT INTO `comisiones` (`id`, `materia_id`, `docente_id`, `numero`, `cuatrimestre`, `anio`) VALUES
(1, 1, 1, 'COM 2', 2, 2026),
(2, 2, 2, 'COM 2', 2, 2026),
(3, 3, 3, 'COM 1', 3, 2026),
(4, 4, 4, 'COM 3', 2, 2026),
(5, 5, 5, 'COM 2', 2, 2026),
(6, 6, 6, 'COM 1', 2, 2026),
(7, 7, 7, 'COM 7', 2, 2026),
(8, 8, 8, 'COM 3', 2, 2026),
(9, 9, 9, 'COM 2', 2, 2026),
(10, 10, 10, 'COM 1', 2, 2026),
(11, 11, 11, 'COM 1', 3, 2026),
(12, 11, 12, 'COM 2', 3, 2026),
(13, 11, 11, 'COM 3', 3, 2026),
(14, 11, 13, 'COM 4', 3, 2026),
(15, 12, 14, 'COM 5', 2, 2026),
(16, 13, 15, 'COM 5', 2, 2026),
(17, 13, 16, 'COM 6', 2, 2026),
(18, 14, 17, 'COM 3', 2, 2026),
(19, 14, 18, 'COM 4', 2, 2026),
(20, 14, 19, 'COM 5', 2, 2026),
(21, 14, 20, 'COM 6', 2, 2026),
(22, 15, 21, 'COM 2', 2, 2026),
(23, 15, 22, 'COM 3', 2, 2026),
(24, 15, 21, 'COM 4', 2, 2026),
(25, 15, 22, 'COM 5', 2, 2026),
(26, 16, 23, 'COM 2', 2, 2026),
(27, 16, 24, 'COM 3', 2, 2026),
(28, 16, 25, 'COM 4', 2, 2026),
(29, 16, 26, 'COM 5', 2, 2026),
(30, 17, 27, 'COM 3', 3, 2026),
(31, 18, 28, 'COM 1', 3, 2026),
(32, 18, 29, 'COM 2', 3, 2026),
(33, 18, 30, 'COM 3', 3, 2026),
(34, 19, 29, 'COM 1', 2, 2026),
(35, 19, 31, 'COM 2', 2, 2026),
(36, 20, 32, 'COM 1', 2, 2026),
(37, 20, 33, 'COM 2', 2, 2026),
(38, 21, 34, 'COM 1', 2, 2026),
(39, 21, 35, 'COM 2', 2, 2026),
(40, 22, 36, 'COM 1', 3, 2026),
(41, 22, 37, 'COM 2', 3, 2026),
(42, 23, 38, 'COM 1', 2, 2026),
(43, 23, 32, 'COM 2', 2, 2026),
(44, 24, 39, 'COM 1', 2, 2026),
(45, 24, 40, 'COM 2', 2, 2026),
(46, 25, 41, 'COM 1', 3, 2026),
(47, 25, 36, 'COM 2', 3, 2026),
(48, 26, 40, 'COM 1', 2, 2026),
(49, 26, 40, 'COM 2', 2, 2026),
(50, 27, 42, 'COM 1', 2, 2026),
(51, 28, 43, 'COM 1', 2, 2026),
(52, 28, 43, 'COM 2', 2, 2026),
(53, 29, 44, 'COM 1', 2, 2026),
(54, 29, 44, 'COM 3', 2, 2026),
(55, 30, 45, 'COM 1', 2, 2026),
(56, 30, 46, 'COM 2', 2, 2026),
(57, 31, 44, 'COM 1', 2, 2026),
(58, 31, 47, 'COM 2', 2, 2026),
(59, 32, 45, 'COM 1', 2, 2026),
(60, 32, 48, 'COM 2', 2, 2026),
(61, 33, 49, 'COM 3', 2, 2026),
(62, 33, 50, 'COM 4', 2, 2026),
(63, 34, 51, 'COM 4', 2, 2026),
(64, 35, 52, 'COM 1', 2, 2026),
(65, 35, 53, 'COM 3', 2, 2026),
(66, 36, 54, 'COM 1', 2, 2026),
(67, 36, 55, 'COM 2', 2, 2026),
(68, 37, 56, 'COM 1', 2, 2026),
(69, 37, 57, 'COM 2', 2, 2026),
(70, 38, 57, 'COM 1', 2, 2026),
(71, 39, 58, 'COM 1', 2, 2026),
(72, 39, 59, 'COM 2', 2, 2026),
(73, 40, 60, 'COM 1', 2, 2026),
(74, 40, 61, 'COM 2', 2, 2026),
(75, 4, 62, 'COM 17', 2, 2026),
(76, 41, 63, 'COM 11', 2, 2026),
(77, 42, 64, 'COM 7', 2, 2026),
(78, 43, 65, 'COM 1', 2, 2026),
(79, 44, 66, 'COM 1', 2, 2026),
(80, 45, 67, 'COM 1', 2, 2026),
(81, 46, 68, 'COM 1', 2, 2026),
(82, 47, 69, 'COM 1', 2, 2026),
(83, 48, 70, 'COM 1', 2, 2026),
(84, 49, 71, 'COM 2', 2, 2026),
(85, 50, 72, 'COM 1', 2, 2026),
(86, 51, 73, 'COM 1', 2, 2026),
(87, 52, 74, 'COM 2', 2, 2026),
(88, 53, 75, 'COM 3', 2, 2026),
(89, 54, 76, 'COM 1', 2, 2026),
(90, 55, 77, 'COM 2', 2, 2026),
(91, 56, 78, 'COM 3', 2, 2026),
(92, 57, 79, 'COM 3', 2, 2026),
(93, 58, 80, 'COM 1', 3, 2026),
(94, 58, 81, 'COM 2', 3, 2026),
(95, 59, 82, 'COM 1', 3, 2026),
(96, 60, 83, 'COM 2', 2, 2026),
(97, 60, 83, 'COM 3', 2, 2026),
(98, 61, 84, 'COM 2', 2, 2026),
(99, 62, 85, 'COM 2', 2, 2026),
(100, 63, 86, 'COM 1', 2, 2026),
(101, 63, 86, 'COM 2', 2, 2026),
(102, 64, 87, 'COM 1', 2, 2026),
(103, 64, 87, 'COM 2', 2, 2026),
(104, 65, 88, 'COM 2', 2, 2026),
(105, 66, 89, 'COM 1', 2, 2026);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comision_carreras`
--

CREATE TABLE `comision_carreras` (
  `comision_id` int(10) UNSIGNED NOT NULL,
  `carrera_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `comision_carreras`
--

INSERT INTO `comision_carreras` (`comision_id`, `carrera_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 2),
(5, 2),
(6, 2),
(7, 3),
(8, 3),
(9, 3),
(10, 3),
(11, 4),
(12, 4),
(13, 4),
(14, 4),
(15, 4),
(16, 4),
(17, 4),
(18, 4),
(19, 4),
(20, 4),
(21, 4),
(22, 4),
(23, 4),
(24, 4),
(25, 4),
(26, 4),
(27, 4),
(28, 4),
(29, 4),
(30, 4),
(31, 4),
(32, 4),
(33, 4),
(34, 4),
(35, 4),
(36, 4),
(37, 4),
(38, 4),
(39, 4),
(40, 4),
(41, 4),
(42, 4),
(43, 4),
(44, 4),
(45, 4),
(46, 4),
(47, 4),
(48, 4),
(49, 4),
(50, 4),
(51, 5),
(51, 6),
(51, 7),
(51, 8),
(52, 5),
(52, 6),
(52, 7),
(52, 8),
(53, 5),
(53, 6),
(53, 7),
(53, 8),
(54, 5),
(54, 6),
(54, 7),
(54, 8),
(55, 5),
(55, 6),
(56, 5),
(56, 6),
(57, 5),
(57, 6),
(58, 5),
(58, 6),
(59, 5),
(59, 6),
(60, 5),
(60, 6),
(61, 5),
(61, 6),
(61, 7),
(61, 8),
(62, 5),
(62, 6),
(62, 7),
(62, 8),
(63, 7),
(64, 7),
(64, 8),
(65, 7),
(65, 8),
(66, 7),
(66, 8),
(67, 7),
(67, 8),
(68, 7),
(68, 8),
(69, 7),
(69, 8),
(70, 7),
(71, 8),
(72, 8),
(73, 8),
(74, 8),
(75, 9),
(75, 10),
(75, 11),
(75, 12),
(76, 9),
(76, 10),
(76, 11),
(76, 12),
(77, 9),
(77, 10),
(77, 11),
(77, 12),
(78, 9),
(79, 9),
(80, 9),
(81, 9),
(81, 10),
(82, 10),
(83, 10),
(84, 10),
(84, 12),
(85, 11),
(86, 11),
(87, 13),
(88, 13),
(89, 13),
(90, 14),
(91, 14),
(92, 14),
(93, 14),
(94, 14),
(95, 14),
(96, 15),
(97, 15),
(98, 15),
(99, 15),
(100, 15),
(101, 15),
(102, 15),
(103, 15),
(104, 15),
(105, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

CREATE TABLE `docentes` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `docentes`
--

INSERT INTO `docentes` (`id`, `nombre`) VALUES
(18, 'Aboy Rosa'),
(12, 'Acevedo Aylén'),
(72, 'Agostino Rodrigo'),
(24, 'Aguilar Cecilia'),
(9, 'Aguilera Pablo'),
(64, 'Alegre Alejandro'),
(20, 'Angerami Maria Clara'),
(35, 'Aramburu Germán'),
(16, 'Arias Manuel Ricardo'),
(70, 'Baldi Eduardo'),
(61, 'Barabino Federico'),
(80, 'Barrios Olga'),
(29, 'Bascans Mariano'),
(82, 'Basterrechea Marcela'),
(63, 'Bermudez Bárbara'),
(75, 'Brega Lisandro'),
(15, 'Cabezón Mariano'),
(30, 'Cadillac Diego'),
(62, 'Cassetti Julia Analía'),
(8, 'Ceniquel Marcos'),
(51, 'Cherny Laura'),
(47, 'Civiello Soledad'),
(78, 'Ciz Camila'),
(17, 'Corradini Claudia'),
(19, 'Daich Varela Leandro'),
(43, 'De Rose Roberto'),
(4, 'Delgadino Daiana'),
(66, 'Dell Inmagine Guillermo'),
(79, 'Di Salvo Carolina'),
(39, 'Discacciati Mariela'),
(74, 'Efron Federico'),
(11, 'Estarellas Victoria'),
(41, 'Etcheverry Daniel'),
(69, 'Fazzito Osvaldo José'),
(73, 'Fiorito Alejandro'),
(81, 'Fracchia Pablo Luis'),
(32, 'García Claudia'),
(13, 'Godoy Leandro Alberto'),
(38, 'Gonzales Ariel'),
(58, 'Gutierrez Mauricio'),
(22, 'Hermida Adriana'),
(85, 'Icardi Marcos'),
(83, 'Idez Ariel'),
(44, 'Jauregui Marianela'),
(45, 'Kaczan Juan Ignacio'),
(50, 'Kondakjian Ana'),
(76, 'Lefevre Rosana'),
(27, 'Lerner Lautaro'),
(37, 'Machin Diego'),
(5, 'Maiorana Miguel'),
(33, 'Marcovecchio Claudia'),
(10, 'Martini Maria de los Angeles'),
(55, 'Menéndez Fernando José'),
(53, 'Menendez Natalia'),
(86, 'Minguzzi Armando'),
(88, 'Nahabedian Javier'),
(71, 'Nievas Gustavo Rodolfo'),
(3, 'Olivares Daniel'),
(48, 'Orozco Marcelo'),
(77, 'Orquera Rolando'),
(6, 'Ostera Juan Manuel'),
(49, 'Pain Malena'),
(2, 'Palermo Cecilia Anabella'),
(34, 'Parra Martin'),
(1, 'Pini Osvaldo'),
(52, 'Pinto Carlos'),
(40, 'Pirolo Gabriel'),
(54, 'Real Melisa'),
(67, 'Repetto Vanesa Verónica'),
(57, 'Reyes Manuel'),
(65, 'Robledo Andrea'),
(60, 'Rodriguez Natalia Monserrat'),
(7, 'Rodriguez Ureta Hernán'),
(56, 'Romero Marchesini'),
(26, 'Roth Manuela'),
(36, 'Salvarredy Julián'),
(42, 'Segura María Verónica'),
(28, 'Segura Mariana'),
(23, 'Sosa Maria Alejandra'),
(84, 'Speranza Adriana'),
(25, 'Stagni Lucia'),
(46, 'Tanuz Leila'),
(14, 'Taramasso María'),
(31, 'Testardini Pablo'),
(21, 'Tucci Adriana'),
(68, 'Van Autenboer Alberto Carlos'),
(89, 'Vara Ana María'),
(87, 'Vazquez Laura'),
(59, 'Zavatarelli Sebastián');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `edificios`
--

CREATE TABLE `edificios` (
  `id` varchar(20) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `foto` varchar(120) NOT NULL DEFAULT '',
  `color1` char(7) NOT NULL DEFAULT '#888888',
  `color2` char(7) NOT NULL DEFAULT '#444444',
  `orden` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `edificios`
--

INSERT INTO `edificios` (`id`, `nombre`, `foto`, `color1`, `color2`, `orden`) VALUES
('daract1', 'Daract I', 'img/daract1.WEBP', '#b9a46a', '#5d6f78', 2),
('daract2', 'Daract II', 'img/daract2.WEBP', '#7f9a8c', '#3f5a62', 3),
('dorrego1', 'Dorrego I', 'img/dorrego1.WEBP', '#3f9a8a', '#2d4d57', 4),
('historico', 'Histórico', 'img/historico.WEBP', '#8d98a3', '#566370', 1),
('laboratorio', 'Laboratorio', 'img/laboratorio.WEBP', '#8a7aa8', '#3d3560', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `titulo`, `fecha_inicio`, `fecha_fin`) VALUES
(1, 'Feriado: Año Nuevo', '2026-01-01', NULL),
(2, 'Receso académico', '2026-01-01', '2026-02-01'),
(3, 'Receso de atención al público', '2026-01-01', '2026-01-25'),
(4, 'Inscripción a exámenes · turno febrero-marzo', '2026-02-11', '2026-02-12'),
(5, 'Feriado: Carnaval', '2026-02-16', '2026-02-17'),
(6, 'Exámenes · turno febrero-marzo', '2026-02-16', '2026-03-07'),
(7, 'Inscripción a asignaturas 1.er cuatrimestre · 1.er llamado', '2026-03-16', '2026-03-17'),
(8, 'Bajas de inscripción 1.er cuatrimestre', '2026-03-20', '2026-04-11'),
(9, 'Inicio del 1.er cuatrimestre', '2026-03-23', NULL),
(10, 'Feriado: Día de la Memoria por la Verdad y la Justicia', '2026-03-24', NULL),
(11, 'Inscripción a asignaturas 1.er cuatrimestre · 2.º llamado', '2026-03-30', '2026-03-31'),
(12, 'Feriado: Día del Veterano y de los Caídos en Malvinas · Jueves Santo', '2026-04-02', NULL),
(13, 'Feriado: Viernes Santo', '2026-04-03', NULL),
(14, 'Feriado: Día del Trabajo', '2026-05-01', NULL),
(15, 'Feriado: Día de la Revolución de Mayo', '2026-05-25', NULL),
(16, 'Feriado: Paso a la Inmortalidad del General Güemes (trasladado desde el 17/06)', '2026-06-15', NULL),
(17, 'Feriado: Día de la Bandera', '2026-06-20', NULL),
(18, 'Feriado: Día de la Independencia', '2026-07-09', NULL),
(19, 'Fin de la cursada del 1.er cuatrimestre', '2026-07-11', NULL),
(20, 'Inscripción a exámenes · turno julio', '2026-07-15', '2026-07-16'),
(21, 'Exámenes · turno julio', '2026-07-20', '2026-08-01'),
(22, 'Inscripción a asignaturas 2.º cuatrimestre · 1.er llamado', '2026-08-03', '2026-08-04'),
(23, 'Bajas de inscripción 2.º cuatrimestre', '2026-08-07', '2026-08-29'),
(24, 'Inicio del 2.º cuatrimestre', '2026-08-10', NULL),
(25, 'Inscripción a asignaturas 2.º cuatrimestre · 2.º llamado', '2026-08-13', '2026-08-14'),
(26, 'Feriado: Paso a la Inmortalidad del General San Martín', '2026-08-17', NULL),
(27, 'Sin clases: Día del Estudiante', '2026-09-21', NULL),
(28, 'Feriado: Día del Respeto a la Diversidad Cultural', '2026-10-12', NULL),
(29, 'Feriado: Día de la Soberanía Nacional (trasladado desde el 20/11)', '2026-11-23', NULL),
(30, 'Fin de la cursada del 2.º cuatrimestre', '2026-11-28', NULL),
(31, 'Inscripción a exámenes · turno diciembre', '2026-12-01', '2026-12-02'),
(32, 'Exámenes · turno diciembre', '2026-12-07', '2026-12-19'),
(33, 'Feriado: Inmaculada Concepción de María', '2026-12-08', NULL),
(34, 'Feriado: Navidad', '2026-12-25', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `excepciones`
--

CREATE TABLE `excepciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `horario_id` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `tipo` enum('cancelada','movida') NOT NULL,
  `aula_id` varchar(30) DEFAULT NULL,
  `inicio` time DEFAULT NULL,
  `fin` time DEFAULT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `creado_por` int(10) UNSIGNED DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `aula_id` varchar(30) NOT NULL,
  `dia` tinyint(3) UNSIGNED NOT NULL,
  `inicio` time NOT NULL,
  `fin` time NOT NULL,
  `comision_id` int(10) UNSIGNED DEFAULT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `organizador` varchar(120) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `nota_interna` varchar(250) DEFAULT NULL
) ;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id`, `aula_id`, `dia`, `inicio`, `fin`, `comision_id`, `titulo`, `organizador`, `fecha`, `nota_interna`) VALUES
(1, 'd2-01-03', 1, '13:30:00', '17:30:00', 69, NULL, NULL, NULL, NULL),
(2, 'd2-01-03', 1, '18:00:00', '22:00:00', 77, NULL, NULL, NULL, NULL),
(3, 'd2-01-03', 2, '09:00:00', '11:00:00', 71, NULL, NULL, NULL, NULL),
(4, 'd2-01-03', 2, '09:00:00', '11:00:00', 72, NULL, NULL, NULL, NULL),
(5, 'd2-01-03', 2, '14:00:00', '18:00:00', 88, NULL, NULL, NULL, NULL),
(6, 'd2-01-03', 2, '18:00:00', '21:00:00', 84, NULL, NULL, NULL, NULL),
(7, 'd2-01-03', 4, '11:00:00', '15:00:00', 87, NULL, NULL, NULL, NULL),
(8, 'd2-01-03', 5, '11:00:00', '15:00:00', 89, NULL, NULL, NULL, 'Superposición real en el aula con: Introducción al Cálculo COM 7.'),
(9, 'd2-01-03', 5, '14:00:00', '17:00:00', 7, NULL, NULL, NULL, 'Superposición real en el aula con: Derecho Comercial I COM 1.'),
(10, 'd2-01-03', 5, '18:00:00', '21:00:00', 92, NULL, NULL, NULL, NULL),
(11, 'd2-01-03', 6, '09:00:00', '13:00:00', 9, NULL, NULL, NULL, NULL),
(12, 'd2-02', 1, '09:00:00', '12:00:00', 93, NULL, NULL, NULL, NULL),
(13, 'd2-02', 1, '17:00:00', '22:00:00', 6, NULL, NULL, NULL, NULL),
(14, 'd2-02', 2, '14:00:00', '18:00:00', 96, NULL, NULL, NULL, NULL),
(15, 'd2-02', 2, '18:00:00', '22:00:00', 97, NULL, NULL, NULL, NULL),
(16, 'd2-02', 5, '08:00:00', '11:00:00', 91, NULL, NULL, NULL, NULL),
(17, 'd2-02', 5, '14:00:00', '18:00:00', 96, NULL, NULL, NULL, NULL),
(18, 'd2-02', 5, '18:00:00', '22:00:00', 97, NULL, NULL, NULL, NULL),
(19, 'd2-02', 6, '09:00:00', '12:00:00', 78, NULL, NULL, NULL, NULL),
(20, 'd2-04', 1, '08:00:00', '12:00:00', 75, NULL, NULL, NULL, NULL),
(21, 'd2-04', 1, '18:00:00', '22:00:00', 79, NULL, NULL, NULL, NULL),
(22, 'd2-04', 2, '15:00:00', '18:00:00', 100, NULL, NULL, NULL, NULL),
(23, 'd2-04', 2, '18:00:00', '21:00:00', 101, NULL, NULL, NULL, NULL),
(24, 'd2-04', 3, '18:00:00', '20:00:00', 79, NULL, NULL, NULL, NULL),
(25, 'd2-04', 3, '20:00:00', '22:00:00', 80, NULL, NULL, NULL, NULL),
(26, 'd2-04', 4, '15:00:00', '18:00:00', 100, NULL, NULL, NULL, NULL),
(27, 'd2-04', 4, '18:00:00', '21:00:00', 101, NULL, NULL, NULL, NULL),
(28, 'd2-04', 5, '08:00:00', '11:00:00', 95, NULL, NULL, NULL, NULL),
(29, 'd2-04', 5, '15:00:00', '18:00:00', 99, NULL, NULL, NULL, NULL),
(30, 'd2-04', 5, '18:00:00', '22:00:00', 80, NULL, NULL, NULL, NULL),
(31, 'd2-04', 6, '09:00:00', '12:00:00', 99, NULL, NULL, NULL, NULL),
(32, 'd2-05', 2, '09:00:00', '13:00:00', 40, NULL, NULL, NULL, NULL),
(33, 'd2-05', 2, '13:30:00', '17:30:00', 66, NULL, NULL, NULL, NULL),
(34, 'd2-05', 2, '18:00:00', '22:00:00', 41, NULL, NULL, NULL, NULL),
(35, 'd2-05', 3, '08:00:00', '13:00:00', 42, NULL, NULL, NULL, NULL),
(36, 'd2-05', 3, '15:00:00', '18:00:00', 61, NULL, NULL, NULL, NULL),
(37, 'd2-05', 3, '18:00:00', '23:00:00', 43, NULL, NULL, NULL, NULL),
(38, 'd2-05', 4, '08:00:00', '13:00:00', 44, NULL, NULL, NULL, NULL),
(39, 'd2-05', 4, '13:30:00', '17:30:00', 65, NULL, NULL, NULL, NULL),
(40, 'd2-05', 4, '18:00:00', '23:00:00', 45, NULL, NULL, NULL, NULL),
(41, 'd2-05', 5, '09:00:00', '12:00:00', 40, NULL, NULL, NULL, NULL),
(42, 'd2-05', 5, '18:00:00', '21:00:00', 41, NULL, NULL, NULL, NULL),
(43, 'd2-06', 1, '17:00:00', '22:00:00', 8, NULL, NULL, NULL, NULL),
(44, 'd2-06', 2, '18:00:00', '21:00:00', 104, NULL, NULL, NULL, NULL),
(45, 'd2-06', 3, '12:00:00', '15:00:00', 102, NULL, NULL, NULL, NULL),
(46, 'd2-06', 3, '15:00:00', '18:00:00', 103, NULL, NULL, NULL, NULL),
(47, 'd2-06', 3, '18:00:00', '22:00:00', 3, NULL, NULL, NULL, NULL),
(48, 'd2-06', 4, '09:30:00', '11:30:00', 4, NULL, NULL, NULL, NULL),
(49, 'd2-06', 4, '18:00:00', '21:00:00', 104, NULL, NULL, NULL, NULL),
(50, 'd2-06', 5, '18:00:00', '21:00:00', 90, NULL, NULL, NULL, NULL),
(51, 'd2-06', 6, '09:00:00', '12:00:00', 102, NULL, NULL, NULL, NULL),
(52, 'd2-06', 6, '12:00:00', '15:00:00', 103, NULL, NULL, NULL, NULL),
(53, 'd2-07', 1, '08:00:00', '13:00:00', 26, NULL, NULL, NULL, NULL),
(54, 'd2-07', 1, '08:00:00', '13:00:00', 27, NULL, NULL, NULL, NULL),
(55, 'd2-07', 1, '13:00:00', '15:00:00', 53, NULL, NULL, NULL, NULL),
(56, 'd2-07', 1, '13:00:00', '15:00:00', 54, NULL, NULL, NULL, NULL),
(57, 'd2-07', 1, '15:00:00', '18:00:00', 51, NULL, NULL, NULL, NULL),
(58, 'd2-07', 1, '15:00:00', '18:00:00', 52, NULL, NULL, NULL, NULL),
(59, 'd2-07', 1, '18:00:00', '23:00:00', 28, NULL, NULL, NULL, NULL),
(60, 'd2-07', 1, '18:00:00', '23:00:00', 29, NULL, NULL, NULL, NULL),
(61, 'd2-07', 2, '09:00:00', '13:00:00', 11, NULL, NULL, NULL, NULL),
(62, 'd2-07', 2, '09:00:00', '13:00:00', 12, NULL, NULL, NULL, NULL),
(63, 'd2-07', 2, '13:30:00', '17:30:00', 67, NULL, NULL, NULL, NULL),
(64, 'd2-07', 2, '18:00:00', '22:00:00', 13, NULL, NULL, NULL, NULL),
(65, 'd2-07', 2, '18:00:00', '22:00:00', 14, NULL, NULL, NULL, NULL),
(66, 'd2-07', 3, '08:00:00', '13:00:00', 18, NULL, NULL, NULL, NULL),
(67, 'd2-07', 3, '08:00:00', '13:00:00', 19, NULL, NULL, NULL, NULL),
(68, 'd2-07', 3, '13:00:00', '15:00:00', 53, NULL, NULL, NULL, NULL),
(69, 'd2-07', 3, '13:00:00', '15:00:00', 54, NULL, NULL, NULL, NULL),
(70, 'd2-07', 3, '15:00:00', '18:00:00', 51, NULL, NULL, NULL, NULL),
(71, 'd2-07', 3, '15:00:00', '18:00:00', 52, NULL, NULL, NULL, NULL),
(72, 'd2-07', 3, '18:00:00', '23:00:00', 20, NULL, NULL, NULL, NULL),
(73, 'd2-07', 3, '18:00:00', '23:00:00', 21, NULL, NULL, NULL, NULL),
(74, 'd2-07', 4, '09:00:00', '13:00:00', 22, NULL, NULL, NULL, NULL),
(75, 'd2-07', 4, '09:00:00', '13:00:00', 23, NULL, NULL, NULL, NULL),
(76, 'd2-07', 4, '13:00:00', '17:00:00', 73, NULL, NULL, NULL, NULL),
(77, 'd2-07', 4, '13:00:00', '17:00:00', 74, NULL, NULL, NULL, NULL),
(78, 'd2-07', 4, '18:00:00', '22:00:00', 24, NULL, NULL, NULL, NULL),
(79, 'd2-07', 4, '18:00:00', '22:00:00', 25, NULL, NULL, NULL, NULL),
(80, 'd2-07', 5, '09:00:00', '12:00:00', 11, NULL, NULL, NULL, NULL),
(81, 'd2-07', 5, '09:00:00', '12:00:00', 12, NULL, NULL, NULL, NULL),
(82, 'd2-07', 5, '12:00:00', '16:00:00', 55, NULL, NULL, NULL, NULL),
(83, 'd2-07', 5, '12:00:00', '16:00:00', 56, NULL, NULL, NULL, NULL),
(84, 'd2-07', 5, '18:00:00', '21:00:00', 13, NULL, NULL, NULL, NULL),
(85, 'd2-07', 5, '18:00:00', '21:00:00', 14, NULL, NULL, NULL, NULL),
(86, 'd2-07', 6, '09:00:00', '13:00:00', 15, NULL, NULL, NULL, NULL),
(87, 'd2-08', 1, '18:00:00', '21:00:00', 85, NULL, NULL, NULL, NULL),
(88, 'd2-08', 2, '09:00:00', '12:00:00', 98, NULL, NULL, NULL, NULL),
(89, 'd2-08', 2, '15:00:00', '18:00:00', 105, NULL, NULL, NULL, NULL),
(90, 'd2-08', 4, '09:00:00', '12:00:00', 98, NULL, NULL, NULL, NULL),
(91, 'd2-08', 4, '15:00:00', '18:00:00', 105, NULL, NULL, NULL, NULL),
(92, 'd2-08', 5, '09:00:00', '14:00:00', 5, NULL, NULL, NULL, NULL),
(93, 'd2-08', 5, '18:00:00', '21:00:00', 85, NULL, NULL, NULL, NULL),
(94, 'd2-08', 6, '08:00:00', '12:00:00', 2, NULL, NULL, NULL, NULL),
(95, 'd2-10', 2, '18:00:00', '21:00:00', 86, NULL, NULL, NULL, NULL),
(96, 'd2-10', 3, '18:00:00', '23:00:00', 1, NULL, NULL, NULL, NULL),
(97, 'd2-10', 6, '09:00:00', '12:00:00', 86, NULL, NULL, NULL, NULL),
(98, 'd2-11-13', 1, '08:00:00', '13:00:00', 34, NULL, NULL, NULL, NULL),
(99, 'd2-11-13', 1, '13:30:00', '17:30:00', 59, NULL, NULL, NULL, NULL),
(100, 'd2-11-13', 1, '18:00:00', '23:00:00', 35, NULL, NULL, NULL, NULL),
(101, 'd2-11-13', 2, '09:00:00', '13:00:00', 31, NULL, NULL, NULL, NULL),
(102, 'd2-11-13', 2, '13:30:00', '17:30:00', 30, NULL, NULL, NULL, 'Superposición real en el aula con: Taller de Arquitectura III COM 2.'),
(103, 'd2-11-13', 2, '13:30:00', '17:30:00', 32, NULL, NULL, NULL, 'Superposición real en el aula con: Taller de Arquitectura II COM 3.'),
(104, 'd2-11-13', 2, '18:00:00', '22:00:00', 33, NULL, NULL, NULL, NULL),
(105, 'd2-11-13', 3, '08:00:00', '13:00:00', 36, NULL, NULL, NULL, NULL),
(106, 'd2-11-13', 3, '13:30:00', '17:30:00', 16, NULL, NULL, NULL, NULL),
(107, 'd2-11-13', 3, '18:00:00', '23:00:00', 37, NULL, NULL, NULL, NULL),
(108, 'd2-11-13', 4, '13:30:00', '17:30:00', 57, NULL, NULL, NULL, NULL),
(109, 'd2-11-13', 5, '09:00:00', '12:00:00', 31, NULL, NULL, NULL, NULL),
(110, 'd2-11-13', 5, '14:00:00', '18:00:00', 30, NULL, NULL, NULL, 'Superposición real en el aula con: Taller de Arquitectura III COM 2. Horario del viernes a verificar (la celda de la grilla está cortada).'),
(111, 'd2-11-13', 5, '14:00:00', '18:00:00', 32, NULL, NULL, NULL, 'Superposición real en el aula con: Taller de Arquitectura II COM 3.'),
(112, 'd2-11-13', 5, '18:00:00', '21:00:00', 33, NULL, NULL, NULL, NULL),
(113, 'd2-11-13', 6, '09:00:00', '12:00:00', 50, NULL, NULL, NULL, NULL),
(114, 'd2-12', 1, '13:30:00', '17:30:00', 60, NULL, NULL, NULL, NULL),
(115, 'd2-12', 2, '14:00:00', '17:00:00', 63, NULL, NULL, NULL, NULL),
(116, 'd2-12', 3, '13:30:00', '17:30:00', 17, NULL, NULL, NULL, NULL),
(117, 'd2-12', 4, '08:00:00', '13:00:00', 38, NULL, NULL, NULL, NULL),
(118, 'd2-12', 4, '13:30:00', '17:30:00', 58, NULL, NULL, NULL, NULL),
(119, 'd2-12', 4, '18:00:00', '23:00:00', 39, NULL, NULL, NULL, NULL),
(120, 'd2-15-17', 1, '09:00:00', '13:00:00', 70, NULL, NULL, NULL, NULL),
(121, 'd2-15-17', 1, '13:30:00', '17:30:00', 68, NULL, NULL, NULL, NULL),
(122, 'd2-15-17', 1, '18:00:00', '22:00:00', 76, NULL, NULL, NULL, NULL),
(123, 'd2-15-17', 2, '09:00:00', '13:00:00', 46, NULL, NULL, NULL, NULL),
(124, 'd2-15-17', 2, '13:00:00', '17:00:00', 10, NULL, NULL, NULL, NULL),
(125, 'd2-15-17', 2, '18:00:00', '22:00:00', 47, NULL, NULL, NULL, NULL),
(126, 'd2-15-17', 3, '15:00:00', '18:00:00', 62, NULL, NULL, NULL, NULL),
(127, 'd2-15-17', 3, '18:00:00', '22:00:00', 48, NULL, NULL, NULL, NULL),
(128, 'd2-15-17', 3, '18:00:00', '22:00:00', 49, NULL, NULL, NULL, NULL),
(129, 'd2-15-17', 4, '13:30:00', '17:30:00', 64, NULL, NULL, NULL, NULL),
(130, 'd2-15-17', 4, '18:00:00', '21:00:00', 84, NULL, NULL, NULL, NULL),
(131, 'd2-15-17', 5, '09:00:00', '12:00:00', 46, NULL, NULL, NULL, NULL),
(132, 'd2-15-17', 5, '18:00:00', '21:00:00', 47, NULL, NULL, NULL, NULL),
(133, 'd2-16', 1, '18:00:00', '22:00:00', 83, NULL, NULL, NULL, NULL),
(134, 'd2-16', 2, '18:00:00', '22:00:00', 82, NULL, NULL, NULL, NULL),
(135, 'd2-16', 4, '18:00:00', '21:00:00', 94, NULL, NULL, NULL, NULL),
(136, 'd2-16', 5, '19:00:00', '20:00:00', 82, NULL, NULL, NULL, NULL),
(137, 'd2-16', 5, '20:00:00', '22:00:00', 83, NULL, NULL, NULL, NULL),
(138, 'd2-16', 6, '09:00:00', '12:00:00', 81, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `nombre`) VALUES
(3, 'Análisis de Señales y Sistemas'),
(9, 'Bioestadística'),
(10, 'Bioética y Filosofía de la Ciencia'),
(44, 'Comercialización'),
(19, 'Construcciones I'),
(41, 'Contabilidad I'),
(43, 'Costos para la Toma de Decisiones'),
(56, 'Derecho'),
(54, 'Derecho Comercial I'),
(46, 'Derecho Laboral II'),
(52, 'Derecho Penal I'),
(53, 'Derecho Privado II'),
(5, 'Desarrollo Sustentable y Derechos Humanos'),
(12, 'Dibujo Arquitectónico I'),
(45, 'Dirección y Gestión de Organizaciones de la Economía Social'),
(27, 'Diseño Bioclimático'),
(59, 'Economía II'),
(50, 'Economía Social'),
(20, 'Estructuras II'),
(23, 'Estructuras III'),
(63, 'Estructuras Narrativas Audiovisuales'),
(13, 'Física'),
(64, 'Historia de los Medios de Comunicación Nacional Latinoamericana'),
(2, 'Inglés Técnico II'),
(21, 'Instalaciones I'),
(24, 'Instalaciones II'),
(14, 'Introducción a la Historia de la Arquitectura'),
(15, 'Introducción a la Tecnología'),
(7, 'Introducción al Cálculo'),
(55, 'Investigación Social I'),
(61, 'Lingüística'),
(42, 'Matemática Financiera'),
(4, 'Matemática I'),
(34, 'Morfología'),
(16, 'Morfología I'),
(1, 'Óptica y Sonido'),
(51, 'Política Económica Argentina'),
(57, 'Psicología de los Grupos e Instituciones'),
(6, 'Química Ambiental II'),
(8, 'Química Orgánica'),
(47, 'Relaciones del Trabajo en el Sector Público'),
(48, 'Relaciones del Trabajo en la Economía Social y Pymes'),
(26, 'Seminario de Redes Infraestructura Urbana'),
(66, 'Seminario sobre Discurso Científico'),
(33, 'Semiótica Aplicada al Diseño II'),
(49, 'Sistemas Administrativos'),
(37, 'Sistemas de Representación Orientado II - Com Visual - Multimedial'),
(38, 'Sistemas de Representación Orientado II - Comunicación Visual'),
(32, 'Sistemas de Representación Orientado II - Industrial - Indumentaria'),
(11, 'Taller de Arquitectura I'),
(17, 'Taller de Arquitectura II'),
(18, 'Taller de Arquitectura III'),
(22, 'Taller de Arquitectura IV'),
(25, 'Taller de Arquitectura V'),
(28, 'Taller de Diseño II'),
(40, 'Taller de Diseño Multimedial II'),
(35, 'Taller de Diseño Orientado II - Com Visual - Multimedial'),
(30, 'Taller de Diseño Orientado II - Industrial - Indumentaria'),
(60, 'Taller de Expresión Oral y Escrita'),
(39, 'Técnicas de Maquetación para Estructuras Multimediales'),
(36, 'Tecnología de Diseño Orientado II - Com Visual - Multimedial'),
(31, 'Tecnología de Diseño Orientado II - Industrial - Indumentaria'),
(29, 'Tecnología II'),
(65, 'Teoría de la Argumentación'),
(62, 'Teorías de la Comunicación I'),
(58, 'Trabajo Social IV');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `mensaje` varchar(300) NOT NULL,
  `enlace` varchar(200) DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `creada_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `usuario_id`, `mensaje`, `enlace`, `leida`, `creada_en`) VALUES
(1, 1, 'Prueba: la clase que seguís cambió de aula. Ahora es en el aula 05.', 'aula.html?id=d2-05&e=daract2&d=1', 1, '2026-10-25 12:00:11'),
(2, 1, 'Prueba: las mesas de examen de diciembre empiezan el 07/12.', 'calendario.html', 1, '2026-10-25 12:00:11'),
(3, 1, 'El lunes 26/10, Morfología I pasa a Daract II 09. Motivo: Se cayo una vieja.', 'aula.html?id=d2-09&e=daract2&d=1', 1, '2026-10-25 16:52:40'),
(4, 1, 'Se restableció el horario habitual de Morfología I del lunes 26/10.', 'aula.html?id=d2-07&e=daract2&d=1', 0, '2026-10-25 16:55:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodos`
--

CREATE TABLE `periodos` (
  `id` int(10) UNSIGNED NOT NULL,
  `anio` smallint(5) UNSIGNED NOT NULL,
  `cuatrimestre` tinyint(3) UNSIGNED NOT NULL,
  `inicio` date NOT NULL,
  `fin` date NOT NULL
) ;

--
-- Volcado de datos para la tabla `periodos`
--

INSERT INTO `periodos` (`id`, `anio`, `cuatrimestre`, `inicio`, `fin`) VALUES
(1, 2026, 1, '2026-03-23', '2026-07-11'),
(2, 2026, 2, '2026-08-10', '2026-11-28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_cambios`
--

CREATE TABLE `registro_cambios` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `accion` varchar(40) NOT NULL,
  `detalle` varchar(500) NOT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `registro_cambios`
--

INSERT INTO `registro_cambios` (`id`, `usuario_id`, `accion`, `detalle`, `creado_en`) VALUES
(1, 2, 'mover_fecha', 'El lunes 26/10, Morfología I pasa a Daract II 09. Motivo: Se cayo una vieja. Avisados: 1.', '2026-10-25 16:52:40'),
(2, 2, 'quitar_cambio', 'Se restableció el horario habitual de Morfología I del lunes 26/10. Avisados: 1.', '2026-10-25 16:55:53'),
(3, 2, 'reservar', 'Reservó Daract II SUM el sábado 07/11 de 07:00 a 15:00: Boliche Joda.', '2026-10-25 16:58:46'),
(4, 2, 'borrar_reserva', 'Borró la reserva: Boliche Joda.', '2026-10-25 16:59:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimientos`
--

CREATE TABLE `seguimientos` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `horario_id` int(10) UNSIGNED NOT NULL,
  `nota` varchar(500) NOT NULL DEFAULT '',
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `seguimientos`
--

INSERT INTO `seguimientos` (`id`, `usuario_id`, `horario_id`, `nota`, `creado_en`) VALUES
(16, 12, 3, '', '2026-10-25 11:59:37'),
(17, 12, 4, '', '2026-10-25 11:59:37'),
(19, 1, 53, 'Palas comprar', '2026-10-25 12:04:21'),
(20, 1, 54, 'Palas comprar', '2026-10-25 12:04:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `dni` varchar(8) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL DEFAULT '',
  `carrera_id` int(10) UNSIGNED DEFAULT NULL,
  `rol` enum('alumno','administrador') NOT NULL DEFAULT 'alumno',
  `estado` enum('activa','pendiente','bloqueada') NOT NULL DEFAULT 'activa',
  `intentos_fallidos` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `dni`, `password_hash`, `nombre`, `apellido`, `carrera_id`, `rol`, `estado`, `intentos_fallidos`, `bloqueado_hasta`, `creado_en`) VALUES
(1, '1', '$2y$10$hmtVDoS2kB7UEJ563ZeD.ucYw7TUF1OiTGD0c/PnrDEHxgc1IRF3O', 'Pepe', 'Pérez', 8, 'alumno', 'activa', 0, NULL, '2026-10-24 23:27:41'),
(2, '2', '$2y$10$6zi1an.1Z.LFpH8t5NEL.Ocl6vyi/fMEQHkZMFu4XdV/53b..ZWJ2', 'Administrador', '', NULL, 'administrador', 'activa', 0, NULL, '2026-10-24 23:27:43'),
(12, '46117520', '$2y$10$zQYMtm/2bYPVg7YvvZ7klu2cl.LpVix3m5jrBaNTd5gBUzkO8OWN2', 'juancho', 'talarga', 11, 'alumno', 'activa', 0, NULL, '2026-10-25 11:33:21');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aulas`
--
ALTER TABLE `aulas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_aula_edificio` (`edificio_id`,`piso`,`orden`);

--
-- Indices de la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_carrera_nombre` (`nombre`);

--
-- Indices de la tabla `comisiones`
--
ALTER TABLE `comisiones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_comision` (`materia_id`,`numero`,`cuatrimestre`,`anio`),
  ADD KEY `ix_com_docente` (`docente_id`);

--
-- Indices de la tabla `comision_carreras`
--
ALTER TABLE `comision_carreras`
  ADD PRIMARY KEY (`comision_id`,`carrera_id`),
  ADD KEY `fk_cc_carrera` (`carrera_id`);

--
-- Indices de la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_docente_nombre` (`nombre`);

--
-- Indices de la tabla `edificios`
--
ALTER TABLE `edificios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_evento_fecha` (`fecha_inicio`);

--
-- Indices de la tabla `excepciones`
--
ALTER TABLE `excepciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_excepcion` (`horario_id`,`fecha`),
  ADD KEY `ix_exc_fecha` (`fecha`),
  ADD KEY `fk_exc_aula` (`aula_id`),
  ADD KEY `fk_exc_usuario` (`creado_por`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_hor_aula_dia` (`aula_id`,`dia`,`inicio`),
  ADD KEY `ix_hor_comision` (`comision_id`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_materia_nombre` (`nombre`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_notif_usuario` (`usuario_id`,`leida`,`creada_en`);

--
-- Indices de la tabla `periodos`
--
ALTER TABLE `periodos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_periodo` (`anio`,`cuatrimestre`);

--
-- Indices de la tabla `registro_cambios`
--
ALTER TABLE `registro_cambios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_reg_fecha` (`creado_en`),
  ADD KEY `fk_reg_usuario` (`usuario_id`);

--
-- Indices de la tabla `seguimientos`
--
ALTER TABLE `seguimientos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_seguimiento` (`usuario_id`,`horario_id`),
  ADD KEY `fk_seg_horario` (`horario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuario_dni` (`dni`),
  ADD KEY `fk_usuario_carrera` (`carrera_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carreras`
--
ALTER TABLE `carreras`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `comisiones`
--
ALTER TABLE `comisiones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `docentes`
--
ALTER TABLE `docentes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `excepciones`
--
ALTER TABLE `excepciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `periodos`
--
ALTER TABLE `periodos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registro_cambios`
--
ALTER TABLE `registro_cambios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `seguimientos`
--
ALTER TABLE `seguimientos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `aulas`
--
ALTER TABLE `aulas`
  ADD CONSTRAINT `fk_aula_edificio` FOREIGN KEY (`edificio_id`) REFERENCES `edificios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `comisiones`
--
ALTER TABLE `comisiones`
  ADD CONSTRAINT `fk_com_docente` FOREIGN KEY (`docente_id`) REFERENCES `docentes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_com_materia` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `comision_carreras`
--
ALTER TABLE `comision_carreras`
  ADD CONSTRAINT `fk_cc_carrera` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cc_comision` FOREIGN KEY (`comision_id`) REFERENCES `comisiones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `excepciones`
--
ALTER TABLE `excepciones`
  ADD CONSTRAINT `fk_exc_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_exc_horario` FOREIGN KEY (`horario_id`) REFERENCES `horarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_exc_usuario` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `fk_hor_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hor_comision` FOREIGN KEY (`comision_id`) REFERENCES `comisiones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `fk_notif_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `registro_cambios`
--
ALTER TABLE `registro_cambios`
  ADD CONSTRAINT `fk_reg_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `seguimientos`
--
ALTER TABLE `seguimientos`
  ADD CONSTRAINT `fk_seg_horario` FOREIGN KEY (`horario_id`) REFERENCES `horarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_seg_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_carrera` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
