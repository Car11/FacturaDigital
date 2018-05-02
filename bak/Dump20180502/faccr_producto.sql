-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: faccr
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.30-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `producto` (
  `id` char(36) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `nombreAbreviado` varchar(100) DEFAULT NULL,
  `descripcion` varchar(1000) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` int(11) NOT NULL,
  `scancode` varchar(45) DEFAULT NULL,
  `codigoRapido` varchar(45) DEFAULT NULL,
  `fechaExpiracion` varchar(15) DEFAULT NULL COMMENT 'UTC TIMESTAMP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto`
--

LOCK TABLES `producto` WRITE;
/*!40000 ALTER TABLE `producto` DISABLE KEYS */;
INSERT INTO `producto` VALUES ('0091f1c3-4dd1-11e8-8168-54ee75873a76','6','6','66',6,6,'6','',NULL),('32c25220-4dd2-11e8-8168-54ee75873a76','7','7','7',77,7,'','',NULL),('3972b36a-4dd2-11e8-8168-54ee75873a76','2','2','2',22,2,'','',NULL),('50307927-4dd1-11e8-8168-54ee75873a76','6','6','6',6,6,'6','6',NULL),('5b316e37-4dd2-11e8-8168-54ee75873a76','9','9','9',99,9,'','',NULL),('6f9f3a73-4dd1-11e8-8168-54ee75873a76','98','98','98',98,98,'9898','',NULL),('8219b25a-4dd1-11e8-8168-54ee75873a76','8','8','88',8,8,'8','',NULL),('c5ec5e0f-4dd0-11e8-8168-54ee75873a76','sda','sad','',4,4,'','',NULL),('d8cf903b-4dd0-11e8-8168-54ee75873a76','u','','',45,456,'','',NULL),('e0f2cbc9-4dcd-11e8-8168-54ee75873a76','Bernal','Rodriguez','',432,432,'','33126',NULL),('ecc42229-4dd0-11e8-8168-54ee75873a76','5','5','5',5,55,'','',NULL);
/*!40000 ALTER TABLE `producto` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-05-02  1:18:09
