-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-06-2025 a las 22:39:18
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
-- Base de datos: `biblioteca`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_libros`
--

CREATE TABLE `categorias_libros` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#007bff',
  `icono` varchar(50) DEFAULT 'mdi-book',
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_libros`
--

INSERT INTO `categorias_libros` (`id`, `nombre`, `descripcion`, `color`, `icono`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Ficción', 'Novelas, cuentos y relatos de ficción literaria', '#e74c3c', 'mdi-book-open-variant', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(2, 'Ciencia y Tecnología', 'Libros sobre ciencias exactas, tecnología e innovación', '#3498db', 'mdi-flask', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(3, 'Historia', 'Libros de historia mundial, nacional y biografías históricas', '#f39c12', 'mdi-history', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(4, 'Filosofía', 'Textos filosóficos, ética y pensamiento crítico', '#9b59b6', 'mdi-lightbulb-on', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(5, 'Literatura Clásica', 'Obras literarias clásicas y universales', '#2ecc71', 'mdi-book-open-page-variant', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(6, 'Ciencias Sociales', 'Sociología, antropología, política y estudios sociales', '#e67e22', 'mdi-account-group', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(7, 'Arte y Cultura', 'Historia del arte, música, teatro y expresiones culturales', '#f1c40f', 'mdi-palette', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(8, 'Biografías', 'Biografías y autobiografías de personajes destacados', '#1abc9c', 'mdi-account-circle', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(9, 'Educación', 'Pedagogía, metodología educativa y desarrollo académico', '#34495e', 'mdi-school', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(10, 'Psicología', 'Psicología, desarrollo personal y comportamiento humano', '#8e44ad', 'mdi-brain', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(11, 'Economía y Negocios', 'Economía, administración, finanzas y emprendimiento', '#27ae60', 'mdi-chart-line', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(12, 'Derecho', 'Textos jurídicos, leyes y ciencias jurídicas', '#2c3e50', 'mdi-gavel', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(13, 'Medicina y Salud', 'Medicina, anatomía, salud y bienestar', '#e74c3c', 'mdi-medical-bag', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(14, 'Ingeniería', 'Textos de ingeniería en todas sus ramas', '#95a5a6', 'mdi-tools', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(15, 'Matemáticas', 'Matemáticas puras y aplicadas', '#3498db', 'mdi-calculator', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(16, 'Física', 'Física teórica y experimental', '#9b59b6', 'mdi-atom', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(17, 'Química', 'Química orgánica, inorgánica y aplicada', '#e67e22', 'mdi-test-tube', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(18, 'Biología', 'Biología, ecología y ciencias de la vida', '#2ecc71', 'mdi-leaf', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(19, 'Informática', 'Programación, sistemas y tecnologías de la información', '#34495e', 'mdi-laptop', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(20, 'Idiomas', 'Diccionarios, gramáticas y aprendizaje de idiomas', '#f39c12', 'mdi-translate', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(21, 'Religión y Espiritualidad', 'Textos religiosos, teología y espiritualidad', '#8e44ad', 'mdi-church', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(22, 'Deportes y Recreación', 'Deportes, actividad física y entretenimiento', '#e74c3c', 'mdi-soccer', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(23, 'Cocina y Gastronomía', 'Recetas, técnicas culinarias y gastronomía', '#f1c40f', 'mdi-chef-hat', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(24, 'Viajes y Geografía', 'Guías de viaje, geografía y culturas del mundo', '#1abc9c', 'mdi-map', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(25, 'Autoayuda', 'Desarrollo personal, motivación y crecimiento personal', '#e67e22', 'mdi-human-handsup', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(26, 'Infantil y Juvenil', 'Literatura para niños y jóvenes', '#ff6b6b', 'mdi-teddy-bear', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(27, 'Poesía', 'Poesía clásica y contemporánea', '#fd79a8', 'mdi-feather', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(28, 'Teatro', 'Obras teatrales y dramaturgia', '#a29bfe', 'mdi-drama-masks', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(29, 'Ensayo', 'Ensayos literarios, políticos y sociales', '#6c5ce7', 'mdi-fountain-pen-tip', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07'),
(30, 'Referencia', 'Enciclopedias, diccionarios y obras de consulta', '#74b9ff', 'mdi-library', 1, '2025-06-05 12:57:07', '2025-06-05 12:57:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadisticas`
--

CREATE TABLE `estadisticas` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `total_prestamos` int(11) DEFAULT NULL,
  `total_devoluciones` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autor` varchar(255) NOT NULL,
  `genero` varchar(100) DEFAULT NULL,
  `tipo_libro` enum('digital','fisico') DEFAULT 'fisico',
  `anio_publicacion` int(11) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  `archivo_pdf` varchar(255) DEFAULT NULL,
  `codigo_qr` varchar(100) DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp(),
  `actualizado_en` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `titulo`, `autor`, `genero`, `tipo_libro`, `anio_publicacion`, `isbn`, `descripcion`, `disponible`, `archivo_pdf`, `codigo_qr`, `creado_en`, `actualizado_en`, `categoria_id`) VALUES
(1, 'El Principito 2', 'Antoine de Saint-ExupÃ©ry', 'Novela', 'fisico', 1967, '9780307474728', 'Obra maestra de la literatura latinoamericana.', 0, NULL, NULL, '2025-06-03 08:00:40', '2025-06-05 14:37:25', NULL),
(2, 'El Principito', 'Antoine de Saint-Exupéry', 'Fábula', 'fisico', 1943, '9780156012195', 'Un clásico para niños y adultos.', 1, NULL, NULL, '2025-06-03 08:00:40', '2025-06-05 05:49:46', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `leido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `usuario_id`, `tipo`, `mensaje`, `fecha`, `leido`) VALUES
(3, 1, 'prestamo', 'Nuevo prÃ©stamo realizado por el estudiante ID: 6', '2025-06-05 01:13:46', 0),
(8, 1, 'prestamo', 'PRUEBA: Nuevo prÃ©stamo registrado para el libro \"Don Quijote\"', '2025-06-05 07:08:55', 0),
(9, 1, 'devolucion', 'PRUEBA: Libro \"Cien aÃ±os de soledad\" devuelto correctamente', '2025-06-05 07:08:55', 0),
(10, 1, 'vencimiento', 'PRUEBA: El prÃ©stamo del libro \"El Principito\" vence maÃ±ana', '2025-06-05 07:08:55', 0),
(11, 7, 'devolucion', 'Libro devuelto correctamente.', '2025-06-05 14:29:13', 0),
(12, 7, 'devolucion', 'Libro devuelto correctamente.', '2025-06-05 14:35:21', 0),
(13, 2, 'devolucion', 'Libro devuelto correctamente.', '2025-06-05 14:37:16', 0),
(14, 3, 'prestamo', 'Nuevo préstamo registrado. Fecha devolución: 2025-06-13', '2025-06-05 14:37:25', 0),
(15, 1, 'prestamo', 'Nuevo préstamo realizado por el estudiante ID: 3', '2025-06-05 14:37:25', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id_prestamo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_libro` int(11) NOT NULL,
  `fecha_prestamo` date NOT NULL,
  `fecha_devolucion` date NOT NULL,
  `status` varchar(100) NOT NULL,
  `qr_prestamo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id_prestamo`, `id_usuario`, `id_libro`, `fecha_prestamo`, `fecha_devolucion`, `status`, `qr_prestamo`) VALUES
(1, 2, 1, '2025-06-01', '2025-06-10', 'no entregado', NULL),
(2, 3, 2, '2025-06-02', '2025-06-12', 'no entregado', NULL),
(4, 7, 1, '2025-06-04', '2025-06-13', 'devuelto', NULL),
(5, 7, 2, '2025-06-05', '2025-07-03', 'devuelto', NULL),
(6, 2, 2, '2025-06-12', '2025-06-20', 'devuelto', NULL),
(7, 3, 1, '2025-06-05', '2025-06-13', 'no entregado', 'uploads/qr/prestamo_7.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recomendaciones`
--

CREATE TABLE `recomendaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `libro_id` int(11) DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resenas`
--

CREATE TABLE `resenas` (
  `id` int(11) NOT NULL,
  `libro_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `calificacion` int(11) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `gmail_institucional` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `carnet` varchar(8) DEFAULT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('estudiante','docente','admin','super_admin') NOT NULL,
  `creado_en` datetime DEFAULT current_timestamp(),
  `actualizado_en` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `activo` tinyint(1) DEFAULT 1,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `gmail_institucional`, `telefono`, `carnet`, `contrasena`, `rol`, `creado_en`, `actualizado_en`, `activo`, `reset_token`, `reset_token_expires`) VALUES
(1, 'Admin Principal', 'admin@gmail.com', '77778888', '00000001', '$2y$10$szoj6VvpLUbyo90iTurV7e5gml8ymC7VDQFCYLAbmCfP6LOH7I/1S', 'admin', '2025-06-03 08:00:40', '2025-06-05 04:13:34', 1, NULL, NULL),
(2, 'Juan Pérez', 'juan.perez@colegio.edu', '77778889', '12345678', '$2y$10$wH1Qw8Qw8Qw8Qw8Qw8Qw8eQw8Qw8Qw8Qw8Qw8Qw8Qw8Qw8Qw8', 'estudiante', '2025-06-03 08:00:40', '2025-06-03 08:00:40', 1, NULL, NULL),
(3, 'Ana López', 'ana.lopez@colegio.edu', '77778890', '87654321', '$2y$10$wH1Qw8Qw8Qw8Qw8Qw8Qw8eQw8Qw8Qw8Qw8Qw8Qw8Qw8Qw8Qw8', 'estudiante', '2025-06-03 08:00:40', '2025-06-03 08:00:40', 1, NULL, NULL),
(7, 'Luis Manuel Ramos Herrera', 'luisravanzo24@gmail.com', '76191279', '20110007', '$2y$10$Pf3Xlyxx0jylfZvj.5VdTOzaUmPXvPmCt06ZTSkc2k5gQKqTFGhGW', 'estudiante', '2025-06-05 14:11:38', '2025-06-05 14:11:38', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_libros_categorias`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_libros_categorias` (
`id` int(11)
,`titulo` varchar(255)
,`autor` varchar(255)
,`genero` varchar(100)
,`tipo_libro` enum('digital','fisico')
,`anio_publicacion` int(11)
,`isbn` varchar(50)
,`descripcion` text
,`disponible` tinyint(1)
,`archivo_pdf` varchar(255)
,`codigo_qr` varchar(100)
,`creado_en` datetime
,`actualizado_en` datetime
,`categoria_id` int(11)
,`categoria_nombre` varchar(100)
,`categoria_descripcion` text
,`categoria_color` varchar(7)
,`categoria_icono` varchar(50)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_libros_categorias`
--
DROP TABLE IF EXISTS `vista_libros_categorias`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_libros_categorias`  AS SELECT `l`.`id` AS `id`, `l`.`titulo` AS `titulo`, `l`.`autor` AS `autor`, `l`.`genero` AS `genero`, `l`.`tipo_libro` AS `tipo_libro`, `l`.`anio_publicacion` AS `anio_publicacion`, `l`.`isbn` AS `isbn`, `l`.`descripcion` AS `descripcion`, `l`.`disponible` AS `disponible`, `l`.`archivo_pdf` AS `archivo_pdf`, `l`.`codigo_qr` AS `codigo_qr`, `l`.`creado_en` AS `creado_en`, `l`.`actualizado_en` AS `actualizado_en`, `l`.`categoria_id` AS `categoria_id`, `cl`.`nombre` AS `categoria_nombre`, `cl`.`descripcion` AS `categoria_descripcion`, `cl`.`color` AS `categoria_color`, `cl`.`icono` AS `categoria_icono` FROM (`libros` `l` left join `categorias_libros` `cl` on(`l`.`categoria_id` = `cl`.`id`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias_libros`
--
ALTER TABLE `categorias_libros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `idx_categorias_nombre` (`nombre`),
  ADD KEY `idx_categorias_activo` (`activo`);

--
-- Indices de la tabla `estadisticas`
--
ALTER TABLE `estadisticas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_libro_titulo` (`titulo`(191)),
  ADD KEY `idx_libros_categoria` (`categoria_id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id_prestamo`),
  ADD KEY `idx_prestamo_usuario` (`id_usuario`),
  ADD KEY `idx_prestamo_libro` (`id_libro`);

--
-- Indices de la tabla `recomendaciones`
--
ALTER TABLE `recomendaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `libro_id` (`libro_id`);

--
-- Indices de la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `libro_id` (`libro_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `carnet` (`carnet`),
  ADD KEY `idx_usuario_carnet` (`carnet`),
  ADD KEY `idx_reset_token` (`reset_token`(191));

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias_libros`
--
ALTER TABLE `categorias_libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `estadisticas`
--
ALTER TABLE `estadisticas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id_prestamo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `recomendaciones`
--
ALTER TABLE `recomendaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resenas`
--
ALTER TABLE `resenas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `libros`
--
ALTER TABLE `libros`
  ADD CONSTRAINT `libros_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_libros` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`id_libro`) REFERENCES `libros` (`id`);

--
-- Filtros para la tabla `recomendaciones`
--
ALTER TABLE `recomendaciones`
  ADD CONSTRAINT `recomendaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `recomendaciones_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`);

--
-- Filtros para la tabla `resenas`
--
ALTER TABLE `resenas`
  ADD CONSTRAINT `resenas_ibfk_1` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`),
  ADD CONSTRAINT `resenas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
