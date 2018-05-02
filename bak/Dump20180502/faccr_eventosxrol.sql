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
-- Table structure for table `eventosxrol`
--

DROP TABLE IF EXISTS `eventosxrol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eventosxrol` (
  `idevento` char(36) NOT NULL,
  `idrol` char(36) NOT NULL,
  PRIMARY KEY (`idevento`,`idrol`),
  KEY `rol_idx` (`idrol`),
  CONSTRAINT `evento` FOREIGN KEY (`idevento`) REFERENCES `evento` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `rol` FOREIGN KEY (`idrol`) REFERENCES `rol` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventosxrol`
--

LOCK TABLES `eventosxrol` WRITE;
/*!40000 ALTER TABLE `eventosxrol` DISABLE KEYS */;
INSERT INTO `eventosxrol` VALUES ('1ed3a48c-3e44-11e8-9ddb-54ee75873a69','1ed3a48c-3e44-11e8-9ddb-54ee75873a80'),('1ed3a48c-3e44-11e8-9ddb-54ee75873a69','1ed3a48c-3e44-11e8-9ddb-54ee75873a81'),('1ed3a48c-3e44-11e8-9ddb-54ee75873a69','1ed3a48c-3e44-11e8-9ddb-54ee75873a82'),('1ed3a48c-3e44-11e8-9ddb-54ee75873a70','1ed3a48c-3e44-11e8-9ddb-54ee75873a80'),('1ed3a48c-3e44-11e8-9ddb-54ee75873a70','1ed3a48c-3e44-11e8-9ddb-54ee75873a82'),('1ed3a48c-3e44-11e8-9ddb-54ee75873a71','1ed3a48c-3e44-11e8-9ddb-54ee75873a80'),('1ed3a48c-3e44-11e8-9ddb-54ee75873a71','1ed3a48c-3e44-11e8-9ddb-54ee75873a82'),('1ed3a48c-3e44-11e8-9ddb-54ee75873a72','1ed3a48c-3e44-11e8-9ddb-54ee75873a80'),('1ed3a48c-3e44-11e8-9ddb-54ee75873a72','1ed3a48c-3e44-11e8-9ddb-54ee75873a81');
/*!40000 ALTER TABLE `eventosxrol` ENABLE KEYS */;
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
