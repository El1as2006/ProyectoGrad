-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-06-2025 a las 06:30:06
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
  `categoria` varchar(100) DEFAULT NULL,
  `genero` varchar(100) DEFAULT NULL,
  `tipo_libro` enum('digital','fisico') DEFAULT 'fisico',
  `año_publicacion` int(11) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  `archivo_pdf` varchar(255) DEFAULT NULL,
  `codigo_qr` varchar(100) DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp(),
  `actualizado_en` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `titulo`, `autor`, `categoria`, `genero`, `tipo_libro`, `año_publicacion`, `isbn`, `descripcion`, `disponible`, `archivo_pdf`, `codigo_qr`, `creado_en`, `actualizado_en`) VALUES
(1, 'Cien años de soledad', 'Gabriel García Márquez', NULL, 'Novela', 'fisico', 1967, '9780307474728', 'Obra maestra de la literatura latinoamericana.', 0, NULL, NULL, '2025-06-03 08:00:40', '2025-06-04 22:12:07'),
(2, 'El Principito', 'Antoine de Saint-Exupéry', NULL, 'Fábula', 'fisico', 1943, '9780156012195', 'Un clásico para niños y adultos.', 1, 'uploads/libro_6840db8b898309.32337496.pdf', NULL, '2025-06-03 08:00:40', '2025-06-04 17:49:31');

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
(1, 6, 'registro', 'Registro exitoso en la biblioteca.', '2025-06-04 10:44:49', 0),
(2, 2, 'devolucion', 'Libro devuelto correctamente.', '2025-06-04 22:01:30', 0);

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
  `fecha_vencimiento` date DEFAULT NULL,
  `status` varchar(100) NOT NULL,
  `qr_prestamo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id_prestamo`, `id_usuario`, `id_libro`, `fecha_prestamo`, `fecha_devolucion`, `fecha_vencimiento`, `status`, `qr_prestamo`) VALUES
(1, 2, 1, '2025-06-01', '2025-06-10', NULL, 'no entregado', NULL),
(2, 3, 2, '2025-06-02', '2025-06-12', NULL, 'no entregado', NULL),
(3, 2, 1, '2025-06-04', '2025-06-12', NULL, 'no entregado', NULL),
(4, 2, 1, '2025-06-04', '2025-06-12', NULL, 'devuelto', NULL);

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
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `gmail_institucional`, `telefono`, `carnet`, `contrasena`, `rol`, `creado_en`, `actualizado_en`, `activo`) VALUES
(1, 'Admin Principal', 'admin@gmail.com', '77778888', '00000001', '$2y$10$szoj6VvpLUbyo90iTurV7e5gml8ymC7VDQFCYLAbmCfP6LOH7I/1S', 'admin', '2025-06-03 08:00:40', '2025-06-04 17:25:29', 1),
(2, 'Juan Pérez', 'juan.perez@colegio.edu', '77778889', '12345678', '$2y$10$wH1Qw8Qw8Qw8Qw8Qw8Qw8eQw8Qw8Qw8Qw8Qw8Qw8Qw8Qw8Qw8', 'estudiante', '2025-06-03 08:00:40', '2025-06-03 08:00:40', 1),
(3, 'Ana López', 'ana.lopez@colegio.edu', '77778890', '87654321', '$2y$10$wH1Qw8Qw8Qw8Qw8Qw8Qw8eQw8Qw8Qw8Qw8Qw8Qw8Qw8Qw8Qw8', 'estudiante', '2025-06-03 08:00:40', '2025-06-03 08:00:40', 1),
(6, 'Luis Manuel Ramos Herrera', 'gemc11705@gmail.com', '71538575', '20110007', '$2y$10$RL4OksztvnX9JaBP9yPjpO6njOzqxk3W4XNzEis5MWGF.sURngP52', 'estudiante', '2025-06-04 10:44:47', '2025-06-04 21:36:43', 1);

--
-- Índices para tablas volcadas
--

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
  ADD KEY `idx_libro_titulo` (`titulo`(191));

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
  ADD KEY `idx_usuario_carnet` (`carnet`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id_prestamo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
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
