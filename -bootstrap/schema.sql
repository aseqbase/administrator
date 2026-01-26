-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: %%DATABASE%%:3306
-- Generation Time: Jan 07, 2025 at 03:39 PM
-- Server version: 10.5.27-MariaDB
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `%%DATABASE%%`
--
CREATE DATABASE IF NOT EXISTS `%%DATABASE%%` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `%%DATABASE%%`;

-- --------------------------------------------------------

--
-- Table structure for table `%%PREFIX%%Package`
--

CREATE TABLE IF NOT EXISTS `%%PREFIX%%Package` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ParentId` int(11) DEFAULT NULL,
  `Name` tinytext NOT NULL,
  `Title` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `Image` varchar(1024) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Access` int(11) NOT NULL DEFAULT 0,
  `Status` tinytext DEFAULT NULL,
  `Reference` varchar(1024) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Paths` longtext DEFAULT NULL,
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `MetaData` longtext DEFAULT NULL,
  PRIMARY KEY (`Id` )
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
