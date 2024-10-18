-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2024 at 04:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cobranza`
--

-- --------------------------------------------------------

--
-- Table structure for table `archivo_xml`
--

CREATE TABLE `archivo_xml` (
  `Id` int(11) NOT NULL,
  `Nombre` text NOT NULL,
  `Id_Pago` int(11) NOT NULL,
  `Banco` text NOT NULL,
  `Registrante` text NOT NULL,
  `Fecha_Registro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archivo_xml`
--

INSERT INTO `archivo_xml` (`Id`, `Nombre`, `Id_Pago`, `Banco`, `Registrante`, `Fecha_Registro`) VALUES
(1, 'EPLAN Gui cs-CZ 2.7 (x64).xml', 1, 'Santander', 'Usuario_2', '2024-10-17');

-- --------------------------------------------------------

--
-- Table structure for table `capturadepago`
--

CREATE TABLE `capturadepago` (
  `id` int(11) NOT NULL,
  `Id_Pago` int(11) NOT NULL,
  `Numero_Pago` int(11) NOT NULL,
  `Numero_Factura` int(11) NOT NULL,
  `Nombre` text NOT NULL,
  `Tipo` varchar(15) NOT NULL,
  `Solicitante` text NOT NULL,
  `Fecha_Registro` date NOT NULL,
  `Cliente` varchar(45) NOT NULL,
  `Banco` text NOT NULL,
  `XML_FILE` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `capturadepago`
--

INSERT INTO `capturadepago` (`id`, `Id_Pago`, `Numero_Pago`, `Numero_Factura`, `Nombre`, `Tipo`, `Solicitante`, `Fecha_Registro`, `Cliente`, `Banco`, `XML_FILE`) VALUES
(1, 1, 1234, 1234, 'EQUIPOS ELECTRICOS D E SINALOA SA DE CV ', 'PPD', 'Usuario_2', '2024-10-17', 'EQUIPOS ELECTRICOS D E SINALOA SA DE CV ', 'Santander', '../xmlFiles/EPLAN Gui cs-CZ 2.7 (x64).xml');

-- --------------------------------------------------------

--
-- Table structure for table `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `debito` text DEFAULT NULL,
  `credito` text DEFAULT NULL,
  `saldo` text DEFAULT NULL,
  `Fecha_Registro` date DEFAULT NULL,
  `Registrante` text DEFAULT NULL,
  `archivo_procesado` varchar(255) DEFAULT NULL,
  `Estado` varchar(20) NOT NULL,
  `Banco` varchar(50) NOT NULL,
  `Cliente` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movimientos_banamex`
--

CREATE TABLE `movimientos_banamex` (
  `Id` int(11) NOT NULL,
  `Fecha` text NOT NULL,
  `Descripcion` text NOT NULL,
  `Depositos` float NOT NULL,
  `Retiros` float NOT NULL,
  `Saldo` float NOT NULL,
  `Fecha_Registro` date NOT NULL,
  `Responsable` text NOT NULL,
  `Archivo_Procesado` text NOT NULL,
  `Estado` varchar(20) NOT NULL,
  `Banco` text NOT NULL,
  `Cliente` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movimientos_banamex`
--

INSERT INTO `movimientos_banamex` (`Id`, `Fecha`, `Descripcion`, `Depositos`, `Retiros`, `Saldo`, `Fecha_Registro`, `Responsable`, `Archivo_Procesado`, `Estado`, `Banco`, `Cliente`) VALUES
(4, '2024-08-10', 'Abono Interbancario Sucursal: 859 Referencia N�merica: 6092307 Referencia Alfan�merica: TRANSFERENCIA DE FONDOS Autorizaci�n: 00613181', 325286, 0, 335285, '2024-10-17', 'Usuario', 'MDAX101024 1.csv', 'Pendiente', 'Banamex', 'Alen Logistics');

-- --------------------------------------------------------

--
-- Table structure for table `movimientos_base`
--

CREATE TABLE `movimientos_base` (
  `Id` int(11) NOT NULL,
  `Folio` text NOT NULL,
  `Fecha` text NOT NULL,
  `Operacion` text NOT NULL,
  `Destinatario` text NOT NULL,
  `Cargo` float NOT NULL,
  `Abono` float NOT NULL,
  `Saldo` float NOT NULL,
  `Estado` text NOT NULL,
  `Registrante` text NOT NULL,
  `Archivo` text NOT NULL,
  `Banco` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movimientos_santander`
--

CREATE TABLE `movimientos_santander` (
  `Id` int(11) NOT NULL,
  `Fecha` varchar(20) NOT NULL,
  `Descripcion` text NOT NULL,
  `Referencia` int(11) NOT NULL,
  `Concepto` text NOT NULL,
  `Abono` int(11) NOT NULL,
  `Cargo` int(11) NOT NULL,
  `Saldo` int(11) NOT NULL,
  `Fecha_Registro` text NOT NULL,
  `Registrante` text NOT NULL,
  `Estado` varchar(20) NOT NULL,
  `Cliente` text NOT NULL,
  `Archivo_procesado` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movimientos_santander`
--

INSERT INTO `movimientos_santander` (`Id`, `Fecha`, `Descripcion`, `Referencia`, `Concepto`, `Abono`, `Cargo`, `Saldo`, `Fecha_Registro`, `Registrante`, `Estado`, `Cliente`, `Archivo_procesado`) VALUES
(1, '1970-01-01', 'AB TRANSF SPEI                          ', 8321102, 'Pago 2                                   012730001373743720                               ', 0, 8855, 987079, '2024-10-17 19:03:51', 'Usuario', 'Capturado', 'EQUIPOS ELECTRICOS D E SINALOA SA DE CV ', '20241010_MovimientosCheque 1.csv'),
(2, '1970-01-01', 'AB TRANSF SPEI                          ', 8348199, 'Embotelladora del Nayar SA de CV         002560056900352531                               ', 2095, 0, 989174, '2024-10-17 19:03:51', 'Usuario', 'Pendiente', 'EMBOTELLADORA DEL NAYAR SA DE CV        ', '20241010_MovimientosCheque 1.csv'),
(3, '1970-01-01', 'AB TRANSF SPEI                          ', 9229490, 'AMESA PAGO F 510001211                   012180001921646120                               ', 170685, 0, 1159860, '2024-10-17 19:03:51', 'Usuario', 'Pendiente', 'CONSORCIO AMESA SA D E CV               ', '20241010_MovimientosCheque 1.csv'),
(4, '1970-01-01', 'AB TRANSF SPEI                          ', 9854741, 'ID2025007070                             021320040205904442                               ', 0, 33607, 1193467, '2024-10-17 19:03:51', 'Usuario', 'Pendiente', 'PATRON SPIRITS MEXICO                   ', '20241010_MovimientosCheque 1.csv');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archivo_xml`
--
ALTER TABLE `archivo_xml`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `capturadepago`
--
ALTER TABLE `capturadepago`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movimientos_banamex`
--
ALTER TABLE `movimientos_banamex`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `movimientos_base`
--
ALTER TABLE `movimientos_base`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `movimientos_santander`
--
ALTER TABLE `movimientos_santander`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `archivo_xml`
--
ALTER TABLE `archivo_xml`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `capturadepago`
--
ALTER TABLE `capturadepago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `movimientos_banamex`
--
ALTER TABLE `movimientos_banamex`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `movimientos_base`
--
ALTER TABLE `movimientos_base`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `movimientos_santander`
--
ALTER TABLE `movimientos_santander`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
