-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 09:55 PM
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
-- Database: `motorcyclerental`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `CustNo` tinyint(11) NOT NULL,
  `Surname` varchar(20) NOT NULL,
  `Forename` varchar(20) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `LicenceNo` decimal(9,0) NOT NULL,
  `Status` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`CustNo`, `Surname`, `Forename`, `Email`, `LicenceNo`, `Status`) VALUES
(10, 'Pidr', 'loh', 'dsdsdsdsd@lohpidr', 213312, 'A'),
(27, 'Bodnar', 'Oleh', 'o231lezkabodnar@gmail.com', 1111123, 'A'),
(30, 'Bodnar1`2', 'Oleh`12', 'olezkabodnar@gmail.com12', 312312, 'A'),
(46, 'Barudi', 'Yunis', 'yunis@letsbeam.ie', 1234256, 'A'),
(47, 'Bodnar2', 'Oleh2', 'olezkabodnar@gmail.com1', 3123, 'A');

-- --------------------------------------------------------

--
-- Table structure for table `makemodel`
--

CREATE TABLE `makemodel` (
  `MBcode` varchar(5) NOT NULL,
  `Make` varchar(20) DEFAULT NULL,
  `Model` varchar(20) DEFAULT NULL,
  `Rate` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `makemodel`
--

INSERT INTO `makemodel` (`MBcode`, `Make`, `Model`, `Rate`) VALUES
('bmwr9', 'BMW', 'R nineT', 80.00),
('ducm8', 'Ducati', 'Monster 821', 70.00),
('hd750', 'Harley Davidson', 'Street 750', 65.50),
('kt210', 'KTM', '210', 12.30),
('yam70', 'Yamaha', 'MT-07', 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `motorcycle`
--

CREATE TABLE `motorcycle` (
  `RegNo` varchar(10) NOT NULL,
  `MBcode` varchar(5) DEFAULT NULL,
  `Status` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `motorcycle`
--

INSERT INTO `motorcycle` (`RegNo`, `MBcode`, `Status`) VALUES
('25-C-4321', 'bmwr9', 'A'),
('25-C-4322', 'bmwr9', 'A'),
('25-D-5678', 'hd750', 'A'),
('25-KE-7788', 'yam70', 'A'),
('25-KY-1234', 'kt210', 'A'),
('25-WW-3344', 'ducm8', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `Reservation_Id` tinyint(11) NOT NULL,
  `RegNo` varchar(10) DEFAULT NULL,
  `MBcode` varchar(5) DEFAULT NULL,
  `CustNo` tinyint(11) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Status` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`Reservation_Id`, `RegNo`, `MBcode`, `CustNo`, `StartDate`, `EndDate`, `Price`, `Status`) VALUES
(19, '25-C-4322', 'bmwr9', 46, '2025-04-25', '2025-04-26', 80.00, 'A'),
(20, '25-C-4321', 'bmwr9', 27, '2025-04-25', '2025-04-26', 80.00, 'A'),
(21, '25-D-5678', 'hd750', 46, '2025-04-25', '2025-04-26', 65.50, 'A'),
(22, '25-KY-1234', 'kt210', 10, '2025-04-24', '2025-04-30', 73.80, 'A'),
(23, '25-KE-7788', 'yam70', 47, '2025-05-06', '2025-05-07', 50.00, 'A'),
(24, '25-C-4321', 'bmwr9', 27, '2025-05-06', '2025-05-08', 160.00, 'C'),
(25, '25-C-4321', 'bmwr9', 48, '2025-05-23', '2025-05-30', 560.00, 'A');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`CustNo`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `LicenceNo` (`LicenceNo`);

--
-- Indexes for table `makemodel`
--
ALTER TABLE `makemodel`
  ADD PRIMARY KEY (`MBcode`);

--
-- Indexes for table `motorcycle`
--
ALTER TABLE `motorcycle`
  ADD PRIMARY KEY (`RegNo`),
  ADD KEY `MBcode` (`MBcode`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`Reservation_Id`),
  ADD KEY `RegNo` (`RegNo`),
  ADD KEY `MBcode` (`MBcode`),
  ADD KEY `CustNo` (`CustNo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `CustNo` tinyint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `Reservation_Id` tinyint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `motorcycle`
--
ALTER TABLE `motorcycle`
  ADD CONSTRAINT `motorcycle_ibfk_1` FOREIGN KEY (`MBcode`) REFERENCES `makemodel` (`MBcode`);

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`RegNo`) REFERENCES `motorcycle` (`RegNo`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`MBcode`) REFERENCES `makemodel` (`MBcode`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
