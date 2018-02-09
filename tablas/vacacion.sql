-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-02-2017 a las 13:56:39
-- Versión del servidor: 10.1.16-MariaDB
-- Versión de PHP: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `vacacion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `id` int(11) NOT NULL,
  `empleado_id` int(11) DEFAULT NULL,
  `vacacion_cabecera_id` int(11) DEFAULT NULL,
  `tipo_permiso_id` int(11) DEFAULT NULL,
  `cen_costo` int(11) DEFAULT NULL,
  `fecha_solicitud` datetime NOT NULL,
  `fecha_regreso` datetime NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `tiempo_licencia` int(11) NOT NULL,
  `tipo` enum('HORAS','DIAS') COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado` int(11) NOT NULL,
  `motivo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `creado_el` datetime NOT NULL,
  `modificado_el` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suspendida`
--

CREATE TABLE `suspendida` (
  `id` int(11) NOT NULL,
  `vacacion_cabecera_id` int(11) DEFAULT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `dias` int(11) NOT NULL,
  `motivo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `creadoEl` datetime NOT NULL,
  `modificadoEl` datetime NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_permiso`
--

CREATE TABLE `tipo_permiso` (
  `id` int(11) NOT NULL,
  `nombre` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `cantidadDias` int(11) NOT NULL,
  `maxAnual` int(11) DEFAULT NULL,
  `creadoEl` datetime NOT NULL,
  `modificadoEl` datetime NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacacion_cabecera`
--

CREATE TABLE `vacacion_cabecera` (
  `id` int(11) NOT NULL,
  `empleado_id` int(11) DEFAULT NULL,
  `cen_costo` int(11) DEFAULT NULL,
  `fecha_solicitud` date NOT NULL,
  `gestion` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `fecha_regreso` date NOT NULL,
  `estado` int(11) NOT NULL,
  `total_dias` int(11) NOT NULL,
  `creado_el` datetime NOT NULL,
  `modificado_el` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacacion_detalle`
--

CREATE TABLE `vacacion_detalle` (
  `id` int(11) NOT NULL,
  `vacacion_gestion_id` int(11) DEFAULT NULL,
  `vacacion_cabecera_id` int(11) DEFAULT NULL,
  `dias` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `creado_el` datetime NOT NULL,
  `modificado_el` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacacion_gestion`
--

CREATE TABLE `vacacion_gestion` (
  `id` int(11) NOT NULL,
  `empleado_id` int(11) DEFAULT NULL,
  `gestion` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `dias` int(11) NOT NULL,
  `tomados` int(11) DEFAULT NULL,
  `estado` int(11) NOT NULL,
  `creado_el` datetime NOT NULL,
  `modificado_el` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `per_permiso`
--
ALTER TABLE `per_permiso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_FD7AAB9E952BE730` (`empleado_id`),
  ADD KEY `IDX_FD7AAB9E2340E23E` (`vacacion_cabecera_id`),
  ADD KEY `IDX_FD7AAB9EFD69AF1F` (`tipo_permiso_id`),
  ADD KEY `IDX_FD7AAB9E40837670` (`cen_costo`);

--
-- Indices de la tabla `suspendida`
--
ALTER TABLE `suspendida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3C2AAE7C2340E23E` (`vacacion_cabecera_id`);

--
-- Indices de la tabla `tipo_permiso`
--
ALTER TABLE `tipo_permiso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_EE838A3E3A909126` (`nombre`);

--
-- Indices de la tabla `vacacion_cabecera`
--
ALTER TABLE `vacacion_cabecera`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5F84407F952BE730` (`empleado_id`),
  ADD KEY `IDX_5F84407F40837670` (`cen_costo`);

--
-- Indices de la tabla `vacacion_detalle`
--
ALTER TABLE `vacacion_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E91BF7209BEC50BE` (`vacacion_gestion_id`),
  ADD KEY `IDX_E91BF7202340E23E` (`vacacion_cabecera_id`);

--
-- Indices de la tabla `vacacion_gestion`
--
ALTER TABLE `vacacion_gestion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B720DEA0952BE730` (`empleado_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `suspendida`
--
ALTER TABLE `suspendida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tipo_permiso`
--
ALTER TABLE `tipo_permiso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `vacacion_cabecera`
--
ALTER TABLE `vacacion_cabecera`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `vacacion_detalle`
--
ALTER TABLE `vacacion_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `vacacion_gestion`
--
ALTER TABLE `vacacion_gestion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD CONSTRAINT `FK_FD7AAB9E2340E23E` FOREIGN KEY (`vacacion_cabecera_id`) REFERENCES `vacacion_cabecera` (`id`),
  ADD CONSTRAINT `FK_FD7AAB9E40837670` FOREIGN KEY (`cen_costo`) REFERENCES `adm_centrocosto` (`id`),
  ADD CONSTRAINT `FK_FD7AAB9E952BE730` FOREIGN KEY (`empleado_id`) REFERENCES `per_empleado` (`id`),
  ADD CONSTRAINT `FK_FD7AAB9EFD69AF1F` FOREIGN KEY (`tipo_permiso_id`) REFERENCES `tipo_permiso` (`id`);

--
-- Filtros para la tabla `suspendida`
--
ALTER TABLE `suspendida`
  ADD CONSTRAINT `FK_3C2AAE7C2340E23E` FOREIGN KEY (`vacacion_cabecera_id`) REFERENCES `vacacion_cabecera` (`id`);

--
-- Filtros para la tabla `vacacion_cabecera`
--
ALTER TABLE `vacacion_cabecera`
  ADD CONSTRAINT `FK_5F84407F40837670` FOREIGN KEY (`cen_costo`) REFERENCES `adm_centrocosto` (`id`),
  ADD CONSTRAINT `FK_5F84407F952BE730` FOREIGN KEY (`empleado_id`) REFERENCES `per_empleado` (`id`);

--
-- Filtros para la tabla `vacacion_detalle`
--
ALTER TABLE `vacacion_detalle`
  ADD CONSTRAINT `FK_E91BF7202340E23E` FOREIGN KEY (`vacacion_cabecera_id`) REFERENCES `vacacion_cabecera` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_E91BF7209BEC50BE` FOREIGN KEY (`vacacion_gestion_id`) REFERENCES `vacacion_gestion` (`id`);

--
-- Filtros para la tabla `vacacion_gestion`
--
ALTER TABLE `vacacion_gestion`
  ADD CONSTRAINT `FK_B720DEA0952BE730` FOREIGN KEY (`empleado_id`) REFERENCES `per_empleado` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
