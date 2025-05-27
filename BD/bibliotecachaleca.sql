-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 03:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bibliotecachaleca`
--
CREATE DATABASE IF NOT EXISTS `bibliotecachaleca` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `bibliotecachaleca`;

-- --------------------------------------------------------

--
-- Table structure for table `libros`
--

CREATE TABLE `libros` (
  `id_book` int(11) NOT NULL,
  `book_name` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `image_cover` varchar(200) NOT NULL,
  `author` varchar(200) NOT NULL,
  `category` varchar(200) NOT NULL,
  `estado` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `libros`
--

INSERT INTO `libros` (`id_book`, `book_name`, `description`, `image_cover`, `author`, `category`, `estado`) VALUES
(1, 'librito', 'Libro para leer', 'e.png', 'fonso', 'estudio', 1),
(2, 'Las tres mosqueteras', 'Libro de lenguaje', 'e.png', 'pruebita', 'estudio', 1);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id_user` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `pass` varchar(256) NOT NULL,
  `email` varchar(200) NOT NULL,
  `user_type` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id_user`, `name`, `pass`, `email`, `user_type`) VALUES
(2, 'elias', '$2y$10$lPQS3ZRq2TpQl2D4o/zshewCf1CeIXg3IAmLya22DWjMcWQ8oN2UW', 'elias@gmail.com', 1),
(3, 'fonso', '$2y$10$lPQS3ZRq2TpQl2D4o/zshewCf1CeIXg3IAmLya22DWjMcWQ8oN2UW', 'fonso@gmail.com', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id_book`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `libros`
--
ALTER TABLE `libros`
  MODIFY `id_book` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
