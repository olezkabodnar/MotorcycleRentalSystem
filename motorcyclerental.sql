-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: motorcyclerental
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer` (
  `CustNo` int NOT NULL AUTO_INCREMENT,
  `Surname` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `Forename` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `LicenceNo` decimal(9,0) NOT NULL,
  `Status` char(1) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`CustNo`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `LicenceNo` (`LicenceNo`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer`
--

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
INSERT INTO `customer` VALUES (10,'Customer1','Test','test1@example.com',213312,'I'),(27,'Customer2','Test','test2@example.com',1111123,'A'),(30,'Customer3','Test','test3@example.com',312312,'A'),(46,'Customer4','Test','test4@example.com',1234256,'A'),(47,'Customer5','Test','test5@example.com',3123,'A');
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extra`
--

DROP TABLE IF EXISTS `extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `extra` (
  `ExtraId` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `ChargeType` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`ExtraId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extra`
--

LOCK TABLES `extra` WRITE;
/*!40000 ALTER TABLE `extra` DISABLE KEYS */;
INSERT INTO `extra` VALUES (1,'Helmet',5.00,'per_day'),(2,'Gloves',3.00,'per_day'),(3,'GPS',4.00,'per_day'),(4,'Luggage',10.00,'once'),(5,'Insurance',25.00,'once');
/*!40000 ALTER TABLE `extra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `makemodel`
--

DROP TABLE IF EXISTS `makemodel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `makemodel` (
  `MBcode` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `Make` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Model` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Rate` decimal(10,2) NOT NULL,
  PRIMARY KEY (`MBcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `makemodel`
--

LOCK TABLES `makemodel` WRITE;
/*!40000 ALTER TABLE `makemodel` DISABLE KEYS */;
INSERT INTO `makemodel` VALUES ('bmwr9','BMW','R nineT',80.00),('ducm8','Ducati','Monster 821',70.00),('hd750','Harley Davidson','Street 750',65.50),('kt210','KTM','210',12.30),('yam70','Yamaha','MT-07',50.00);
/*!40000 ALTER TABLE `makemodel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `motorcycle`
--

DROP TABLE IF EXISTS `motorcycle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `motorcycle` (
  `RegNo` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `MBcode` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Status` char(1) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`RegNo`),
  KEY `MBcode` (`MBcode`),
  CONSTRAINT `motorcycle_ibfk_1` FOREIGN KEY (`MBcode`) REFERENCES `makemodel` (`MBcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `motorcycle`
--

LOCK TABLES `motorcycle` WRITE;
/*!40000 ALTER TABLE `motorcycle` DISABLE KEYS */;
INSERT INTO `motorcycle` VALUES ('25-C-4321','bmwr9','A'),('25-C-4322','bmwr9','A'),('25-D-5678','hd750','A'),('25-KE-7788','yam70','A'),('25-KY-1234','kt210','A'),('25-WW-3344','ducm8','A');
/*!40000 ALTER TABLE `motorcycle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservation` (
  `Reservation_Id` int NOT NULL AUTO_INCREMENT,
  `RegNo` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `MBcode` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CustNo` int DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Status` char(1) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`Reservation_Id`),
  KEY `RegNo` (`RegNo`),
  KEY `MBcode` (`MBcode`),
  KEY `CustNo` (`CustNo`),
  CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`RegNo`) REFERENCES `motorcycle` (`RegNo`),
  CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`MBcode`) REFERENCES `makemodel` (`MBcode`),
  CONSTRAINT `reservation_ibfk_3` FOREIGN KEY (`CustNo`) REFERENCES `customer` (`CustNo`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation`
--

LOCK TABLES `reservation` WRITE;
/*!40000 ALTER TABLE `reservation` DISABLE KEYS */;
INSERT INTO `reservation` VALUES (19,'25-C-4322','bmwr9',46,'2025-04-25','2025-04-26',80.00,'A'),(20,'25-C-4321','bmwr9',27,'2025-04-25','2025-04-26',80.00,'A'),(21,'25-D-5678','hd750',46,'2025-04-25','2025-04-26',65.50,'A'),(22,'25-KY-1234','kt210',10,'2025-04-24','2025-04-30',73.80,'C'),(23,'25-KE-7788','yam70',47,'2025-05-06','2025-05-07',50.00,'A'),(24,'25-C-4321','bmwr9',27,'2025-05-06','2025-05-08',160.00,'C'),(25,'25-C-4321','bmwr9',47,'2025-05-23','2025-05-30',560.00,'A'),(26,'25-C-4321','bmwr9',46,'2026-09-24','2026-09-25',80.00,'A'),(27,'25-C-4322','bmwr9',27,'2026-09-24','2026-09-30',480.00,'A');
/*!40000 ALTER TABLE `reservation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation_extra`
--

DROP TABLE IF EXISTS `reservation_extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservation_extra` (
  `Reservation_Id` int NOT NULL,
  `ExtraId` int NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`Reservation_Id`,`ExtraId`),
  KEY `ExtraId` (`ExtraId`),
  CONSTRAINT `reservation_extra_ibfk_1` FOREIGN KEY (`Reservation_Id`) REFERENCES `reservation` (`Reservation_Id`),
  CONSTRAINT `reservation_extra_ibfk_2` FOREIGN KEY (`ExtraId`) REFERENCES `extra` (`ExtraId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation_extra`
--

LOCK TABLES `reservation_extra` WRITE;
/*!40000 ALTER TABLE `reservation_extra` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservation_extra` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
