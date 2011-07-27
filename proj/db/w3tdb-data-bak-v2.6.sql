-- MySQL dump 10.13  Distrib 5.2.6-MariaDB, for Linux (i686)
--
-- Host: localhost    Database: w3tdb
-- ------------------------------------------------------
-- Server version	5.2.6-MariaDB-log

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
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `add1` varchar(100) DEFAULT NULL,
  `add2` varchar(100) DEFAULT NULL,
  `zipcode` varchar(50) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) NOT NULL,
  `store_account` varchar(50) DEFAULT NULL,
  `user_account` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_address_store1` (`store_account`),
  KEY `fk_address_user1` (`user_account`),
  CONSTRAINT `fk_address_store1` FOREIGN KEY (`store_account`) REFERENCES `store` (`store_account`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_user1` FOREIGN KEY (`user_account`) REFERENCES `user` (`user_account`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `address`
--

LOCK TABLES `address` WRITE;
/*!40000 ALTER TABLE `address` DISABLE KEYS */;
/*!40000 ALTER TABLE `address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `age_range`
--

DROP TABLE IF EXISTS `age_range`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `age_range` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `age_range`
--

LOCK TABLES `age_range` WRITE;
/*!40000 ALTER TABLE `age_range` DISABLE KEYS */;
INSERT INTO `age_range` VALUES (1,'N/A'),(2,'20-25'),(3,'25-30'),(4,'30-35'),(5,'99-100'),(6,'99-100'),(7,'99-100'),(8,'99-100'),(9,'99-100'),(10,'99-100'),(11,'99-100'),(12,'99-100'),(13,'99-100'),(14,'99-100'),(15,'99-100'),(16,'99-100'),(17,'99-100'),(18,'99-100'),(19,'99-100'),(20,'99-100'),(21,'99-100'),(22,'99-100'),(23,'99-100'),(24,'99-100'),(25,'99-100'),(26,'99-100'),(27,'99-100'),(28,'99-100'),(29,'99-100'),(30,'99-100'),(31,'99-100'),(32,'99-100'),(33,'99-100'),(34,'99-100'),(35,'99-100'),(36,'99-100'),(37,'99-100'),(38,'99-100'),(39,'99-100'),(40,'99-100'),(41,'99-100'),(42,'99-100'),(43,'99-100'),(44,'99-100'),(45,'99-100'),(46,'99-100'),(47,'99-100'),(48,'99-100'),(49,'99-100'),(50,'99-100'),(51,'99-100'),(52,'99-100'),(53,'99-100'),(54,'99-100'),(55,'99-100'),(56,'99-100'),(57,'99-100'),(58,'99-100'),(59,'99-100'),(60,'99-100'),(61,'99-100'),(62,'99-100'),(63,'99-100'),(64,'99-100'),(65,'99-100'),(66,'99-100'),(67,'99-100'),(68,'99-100'),(69,'99-100'),(70,'99-100'),(71,'99-100'),(72,'99-100'),(73,'99-100'),(74,'99-100'),(75,'99-100'),(76,'99-100'),(77,'99-100'),(78,'99-100'),(79,'99-100'),(80,'99-100'),(81,'99-100'),(82,'99-100'),(83,'99-100'),(84,'99-100'),(85,'25-30');
/*!40000 ALTER TABLE `age_range` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact` (
  `store_account` varchar(50) DEFAULT NULL,
  `user_account` varchar(50) DEFAULT NULL,
  `contact_type` varchar(50) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`value`),
  KEY `fk_contact_contact_type1` (`contact_type`),
  KEY `fk_contact_store1` (`store_account`),
  KEY `fk_contact_user1` (`user_account`),
  KEY `user_account_fk` (`user_account`),
  KEY `store_account_fk` (`store_account`),
  KEY `contact_type_fk` (`contact_type`),
  CONSTRAINT `user_account_fk` FOREIGN KEY (`user_account`) REFERENCES `user` (`user_account`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `store_account_fk` FOREIGN KEY (`store_account`) REFERENCES `store` (`store_account`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `contact_type_fk` FOREIGN KEY (`contact_type`) REFERENCES `contact_type` (`contact_type`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
INSERT INTO `contact` VALUES (NULL,'asasdasd','email','asasdasd@masxaro.com'),(NULL,'asasdasd','email','asdasd'),(NULL,'test123','email','asdasd@tet.com'),(NULL,'test','email','daizenga@gmail.com'),('Mc_NYU',NULL,'email','Mc_NYU@masxaro.com'),(NULL,'new','email','new@masxaro.com'),(NULL,'test123','email','test123@masxaro.com'),(NULL,'test','email','test@masxaro.com'),(NULL,'w3tAcc','email','w3tAcc@masxaro.net'),(NULL,'w3tAcc','email','yangcongknight@gmail.com');
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_type`
--

DROP TABLE IF EXISTS `contact_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_type` (
  `contact_type` varchar(50) NOT NULL,
  PRIMARY KEY (`contact_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_type`
--

LOCK TABLES `contact_type` WRITE;
/*!40000 ALTER TABLE `contact_type` DISABLE KEYS */;
INSERT INTO `contact_type` VALUES ('email'),('phone');
/*!40000 ALTER TABLE `contact_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receipt`
--

DROP TABLE IF EXISTS `receipt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `receipt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_account` varchar(50) NOT NULL,
  `user_account` varchar(50) NOT NULL,
  `receipt_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tax` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `img` blob,
  `deleted` tinyint(1) DEFAULT '0',
  `source` varchar(20) DEFAULT 'default',
  PRIMARY KEY (`id`),
  KEY `fk_receipt_store1` (`store_account`),
  KEY `fk_receipt_user1` (`user_account`),
  CONSTRAINT `fk_receipt_store1` FOREIGN KEY (`store_account`) REFERENCES `store` (`store_account`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_receipt_user1` FOREIGN KEY (`user_account`) REFERENCES `user` (`user_account`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receipt`
--

LOCK TABLES `receipt` WRITE;
/*!40000 ALTER TABLE `receipt` DISABLE KEYS */;
INSERT INTO `receipt` VALUES (1,'Mc_NYU','new','2011-07-19 20:59:21','0.10','14.00',NULL,0,'default'),(2,'Mc_NYU','new','2011-06-10 15:24:01','0.10','0.00',NULL,0,'default'),(3,'Mc_NYU','w3t','2011-07-17 12:32:23','0.09','99.00',NULL,0,'default'),(4,'Mc_NYU','w3t','2011-07-17 12:32:23','0.09','99.00',NULL,0,'default'),(5,'Mc_NYU','new','2011-06-12 09:04:26','0.00','0.00',NULL,0,'default'),(6,'Mc_NYU','w3t','2011-06-14 16:13:28','0.09','99.00',NULL,0,'default'),(7,'Mc_NYU','new','2011-06-15 08:43:46','0.10','4.00',NULL,0,'default'),(8,'Mc_NYU','new','2011-06-15 09:08:36','0.10','4.00',NULL,0,'default'),(9,'Mc_NYU','new','2011-06-15 09:08:37','0.10','0.00',NULL,0,'default'),(10,'Mc_NYU','new','2011-06-15 09:09:50','0.10','4.00',NULL,0,'default'),(11,'Mc_NYU','new','2011-06-15 09:09:51','0.10','0.00',NULL,0,'default'),(12,'Mc_NYU','new','2011-06-18 08:29:48','0.10','4.00',NULL,0,'default'),(13,'Mc_NYU','new','2011-06-18 08:29:49','0.10','0.00',NULL,0,'default'),(14,'Mc_NYU','new','2011-06-18 08:31:48','0.10','4.00',NULL,0,'default'),(15,'Mc_NYU','new','2011-06-18 08:31:50','0.10','0.00',NULL,0,'default'),(16,'Mc_NYU','new','2011-06-18 08:32:19','0.10','4.00',NULL,0,'default'),(17,'Mc_NYU','new','2011-06-18 08:32:20','0.10','0.00',NULL,0,'default'),(18,'Mc_NYU','new','2011-06-18 08:33:15','0.10','4.00',NULL,0,'default'),(19,'Mc_NYU','new','2011-06-18 08:33:17','0.10','0.00',NULL,0,'default'),(20,'Mc_NYU','new','2011-06-18 08:34:05','0.10','4.00',NULL,0,'default'),(21,'Mc_NYU','new','2011-06-18 08:34:06','0.10','0.00',NULL,0,'default'),(22,'Mc_NYU','new','2011-06-18 08:36:45','0.10','4.00',NULL,0,'default'),(24,'Mc_NYU','new','2011-06-20 07:29:19','0.10','4.00',NULL,0,'default'),(26,'Mc_NYU','new','2011-06-26 05:05:19','0.10','4.00',NULL,0,'default'),(28,'Mc_NYU','new','2011-06-26 05:09:54','0.10','4.00',NULL,0,'default'),(29,'Mc_NYU','new','2011-06-26 05:10:48','0.10','4.00',NULL,0,'default'),(31,'Mc_NYU','new','2011-06-27 23:58:35','0.10','4.00',NULL,0,'default'),(32,'Mc_NYU','new','2011-07-14 05:51:26','0.10','1.11',NULL,0,'default'),(33,'Mc_NYU','new','2011-07-07 04:16:42','0.10','0.00',NULL,0,'default'),(34,'Mc_NYU','new','2011-07-17 12:32:23','0.10','4.00',NULL,0,'default'),(36,'Mc_NYU','w3t','2011-07-17 12:32:23','0.09','99.00',NULL,0,'default'),(37,'Mc_NYU','w3t','2011-07-14 04:26:38','0.09','99.00',NULL,0,'default'),(38,'Mc_NYU','w3t','2011-07-14 04:32:55','0.09','99.00',NULL,0,'default'),(39,'Mc_NYU','w3t','2011-07-14 04:34:02','0.09','99.00',NULL,0,'default'),(40,'Mc_NYU','w3t','2011-07-14 04:47:30','0.09','29.00',NULL,0,'default'),(41,'Mc_NYU','w3t','2011-07-14 04:48:34','0.09','10.00',NULL,0,'default'),(42,'Mc_NYU','w3t','2011-07-17 12:32:23','0.09','10.00',NULL,0,'default'),(43,'Mc_NYU','w3t','2011-07-14 06:02:55','0.09','0.00',NULL,0,'default'),(44,'Mc_NYU','w3t','2011-07-14 06:04:08','0.09','0.00',NULL,0,'default'),(45,'Mc_NYU','w3t','2011-07-14 06:04:47','0.09','0.00',NULL,0,'default'),(46,'Mc_NYU','w3t','2011-07-14 06:07:15','0.09','0.00',NULL,0,'default'),(47,'Mc_NYU','w3t','2011-07-14 06:07:59','0.09','0.00',NULL,0,'default'),(48,'Mc_NYU','w3t','2011-07-14 06:08:36','0.09','0.00',NULL,0,'default'),(49,'Mc_NYU','w3t','2011-07-14 06:10:43','0.09','0.00',NULL,0,'default'),(50,'Mc_NYU','w3t','2011-07-14 06:11:08','0.09','0.00',NULL,0,'default'),(51,'Mc_NYU','w3t','2011-07-14 06:13:26','0.09','0.00',NULL,0,'default'),(52,'Mc_NYU','w3t','2011-07-14 06:15:37','0.09','0.00',NULL,0,'default'),(53,'Mc_NYU','w3t','2011-07-14 06:18:55','0.09','0.00',NULL,0,'default'),(54,'Mc_NYU','w3t','2011-07-14 06:20:02','0.09','0.00',NULL,0,'default'),(55,'Mc_NYU','w3t','2011-07-14 06:20:24','0.09','0.00',NULL,0,'default'),(56,'Mc_NYU','w3t','2011-07-14 06:20:56','0.09','0.00',NULL,0,'default'),(57,'Mc_NYU','w3t','2011-07-14 06:21:27','0.09','0.00',NULL,0,'default'),(58,'Mc_NYU','w3t','2011-07-14 06:23:38','0.09','0.00',NULL,0,'default'),(59,'Mc_NYU','w3t','2011-07-14 06:24:04','0.09','0.00',NULL,0,'default'),(60,'Mc_NYU','w3t','2011-07-14 06:25:46','0.09','0.00',NULL,0,'default'),(61,'Mc_NYU','w3t','2011-07-14 06:26:18','0.09','0.00',NULL,0,'default'),(62,'Mc_NYU','w3t','2011-07-14 06:26:45','0.09','0.00',NULL,0,'default'),(63,'Mc_NYU','w3t','2011-07-14 06:32:33','0.09','0.00',NULL,0,'default'),(64,'Mc_NYU','w3t','2011-07-17 12:32:23','0.09','9.75',NULL,0,'default'),(65,'Mc_NYU','w3t','2011-07-17 12:32:23','0.09','9.75',NULL,0,'default'),(66,'Mc_NYU','w3t','2011-07-17 12:32:23','0.09','9.75',NULL,0,'default'),(67,'Mc_NYU','w3t','2011-07-17 12:32:23','0.09','9.75',NULL,0,'default'),(72,'Mc_NYU','new','2011-07-18 18:30:25','15.00','2.64',NULL,0,'default'),(73,'Mc_NYU','new','2011-07-18 21:31:45','15.00','2.64',NULL,0,'default'),(76,'Mc_NYU','new','2011-07-18 21:35:56','10.00','0.00',NULL,0,'default'),(78,'Mc_NYU','new','2011-07-18 21:38:24','10.00','0.00',NULL,0,'default'),(80,'Mc_NYU','w3t','2011-07-17 12:32:23','8.75','87.46',NULL,0,'default'),(81,'Mc_NYU','w3t','2011-07-17 12:32:23','8.75','9.75',NULL,0,'default'),(82,'Mc_NYU','new','2011-07-25 02:13:52','15.00','3.57',NULL,0,'default');
/*!40000 ALTER TABLE `receipt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receipt_item`
--

DROP TABLE IF EXISTS `receipt_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `receipt_item` (
  `receipt_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(45) NOT NULL,
  `item_qty` int(11) NOT NULL,
  `item_discount` decimal(10,2) DEFAULT '0.00',
  `item_price` decimal(10,2) NOT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  KEY `fk_receipt_item_receipt1` (`receipt_id`),
  KEY `receipt_fk` (`receipt_id`),
  CONSTRAINT `receipt_fk` FOREIGN KEY (`receipt_id`) REFERENCES `receipt` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receipt_item`
--

LOCK TABLES `receipt_item` WRITE;
/*!40000 ALTER TABLE `receipt_item` DISABLE KEYS */;
INSERT INTO `receipt_item` VALUES (1,10,'fries-mid',2,'1.00','2.25',0),(1,11,'cheese burg',1,'1.00','2.50',0),(1,12,'coke',2,'1.00','1.25',0),(1,23,'Coffee',1,'1.00','1.00',0),(1,29,'Salad',1,'1.00','3.00',0),(3,3,'Harry-Potter - IIIII123123123123',1,'1.00','10.99',0),(3,4,'Harry-potter - II',2,'1.00','39.99',0),(3,5,'Harry-potter - III',5,'1.00','19.99',0),(4,3,'Harry-Potter - I123',1,'1.00','10.99',0),(4,4,'Harry-potter - II',2,'1.00','39.99',0),(4,5,'Harry-potter - III',5,'1.00','19.99',0),(6,3,'Harry-Potter - I',1,'1.00','10.99',0),(6,4,'Harry-potter - II',2,'1.00','39.99',0),(6,5,'Harry-potter - III',5,'1.00','19.99',0),(7,23,'Coffee',1,'1.00','1.00',0),(7,29,'Salad',1,'1.00','3.00',0),(8,23,'Coffee',1,'1.00','1.00',0),(8,29,'Salad',1,'1.00','3.00',0),(10,23,'Coffee',1,'1.00','1.00',0),(10,29,'Salad',1,'1.00','3.00',0),(12,23,'Coffee',1,'1.00','1.00',0),(12,29,'Salad',1,'1.00','3.00',0),(14,23,'Coffee',1,'1.00','1.00',0),(14,29,'Salad',1,'1.00','3.00',0),(16,23,'Coffee',1,'1.00','1.00',0),(16,29,'Salad',1,'1.00','3.00',0),(18,23,'Coffee',1,'1.00','1.00',0),(18,29,'Salad',1,'1.00','3.00',0),(20,23,'Coffee',1,'1.00','1.00',0),(20,29,'Salad',1,'1.00','3.00',0),(22,23,'Coffee',1,'1.00','1.00',0),(22,29,'Salad',1,'1.00','3.00',0),(24,23,'Coffee',1,'1.00','1.00',0),(24,29,'Salad',1,'1.00','3.00',0),(26,23,'Coffee',1,'1.00','1.00',0),(26,29,'Salad',1,'1.00','3.00',0),(28,23,'Coffee',1,'1.00','1.00',0),(28,29,'Salad',1,'1.00','3.00',0),(29,23,'Coffee',1,'1.00','1.00',0),(29,29,'Salad',1,'1.00','3.00',0),(31,23,'Coffee',1,'1.00','1.00',0),(31,29,'Salad',1,'1.00','3.00',0),(34,23,'Coffee',1,'1.00','1.00',0),(34,29,'Salad',1,'1.00','3.00',0),(36,3,'Harry-Potter - VI',1,'1.00','10.99',0),(36,4,'Harry-potter - II',2,'1.00','39.99',0),(36,5,'Harry-potter - III',5,'1.00','19.99',0),(37,0,'Harry-Potter - I',1,'1.00','10.99',1),(37,0,'Harry-potter - II',2,'1.00','39.99',1),(37,0,'Harry-potter - III',5,'1.00','19.99',1),(38,0,'Harry-Potter - I',1,'1.00','10.99',1),(38,0,'Harry-potter - II',2,'1.00','39.99',1),(38,0,'Harry-potter - III',5,'1.00','19.99',1),(39,0,'Harry-Potter - I',1,'1.00','10.99',1),(39,0,'Harry-potter - II',2,'1.00','39.99',1),(39,0,'Harry-potter - III',5,'1.00','19.99',1),(40,0,'Big Mac',1,'1.00','4.99',1),(40,0,'Salad',1,'1.00','1.99',1),(40,0,'Medium drink',1,'1.00','19.99',1),(41,0,'Big Mac',1,'1.00','4.99',1),(41,0,'Salad',1,'1.00','1.99',1),(41,0,'Medium drink',1,'1.00','1.99',1),(42,1,'Big Mac',1,'1.00','4.99',0),(42,2,'Salad',1,'1.00','1.99',0),(42,3,'Medium drink',1,'1.00','1.99',0),(62,1,'big mac',1,'1.00','4.99',0),(64,1,'Big Mac',1,'1.00','4.99',0),(64,2,'Salad',1,'1.00','1.99',0),(64,3,'Medium drink',1,'1.00','1.99',0),(65,1,'Big Mac',1,'1.00','4.99',0),(65,2,'Salad',1,'1.00','1.99',0),(65,3,'Medium drink',1,'1.00','1.99',0),(66,1,'Big Mac',1,'1.00','4.99',0),(66,2,'Salad',1,'1.00','1.99',0),(66,3,'Medium drink',1,'1.00','1.99',0),(67,1,'Big Mac',1,'1.00','4.99',0),(67,2,'Salad',1,'1.00','1.99',0),(67,3,'Medium drink',1,'1.00','1.99',0),(72,23,'Coffee',1,'0.00','1.00',0),(72,29,'Salad',1,'30.00','3.00',0),(73,23,'Coffee',1,'0.00','1.00',0),(73,29,'Salad',1,'30.00','3.00',0),(80,1,'Big Mac',1,'1.00','4.99',0),(80,2,'Salad',1,'1.00','1.99',0),(80,3,'Medium drink',1,'1.00','1.99',0),(81,1,'Big Mac',1,'1.00','4.99',0),(81,2,'Salad',1,'1.00','1.99',0),(81,3,'Medium drink',1,'1.00','1.99',0),(82,23,'Coffee',1,'0.00','1.00',0),(82,29,'Salad',1,'30.00','3.00',0);
/*!40000 ALTER TABLE `receipt_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receipt_source`
--

DROP TABLE IF EXISTS `receipt_source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `receipt_source` (
  `source` varchar(20) NOT NULL,
  PRIMARY KEY (`source`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receipt_source`
--

LOCK TABLES `receipt_source` WRITE;
/*!40000 ALTER TABLE `receipt_source` DISABLE KEYS */;
INSERT INTO `receipt_source` VALUES ('default'),('email'),('mobile'),('user');
/*!40000 ALTER TABLE `receipt_source` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receipt_tag`
--

DROP TABLE IF EXISTS `receipt_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `receipt_tag` (
  `tag` varchar(20) NOT NULL,
  `user_account` varchar(50) NOT NULL,
  `receipt_id` int(11) NOT NULL,
  PRIMARY KEY (`tag`,`user_account`,`receipt_id`),
  KEY `fk_tag_has_receipt_receipt1` (`receipt_id`),
  KEY `fk_tag_has_receipt_tag1` (`tag`,`user_account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receipt_tag`
--

LOCK TABLES `receipt_tag` WRITE;
/*!40000 ALTER TABLE `receipt_tag` DISABLE KEYS */;
INSERT INTO `receipt_tag` VALUES ('book','w3t',39),('book','w3t',40),('book','w3t',41),('book','w3t',42),('book','w3t',43),('book','w3t',44),('book','w3t',45),('book','w3t',46),('food','w3t',61),('food','w3t',62),('food','w3t',63),('food','w3t',64),('food','w3t',65),('food','w3t',66),('food','w3t',67),('food','w3t',68),('food','w3t',69),('gym','w3t',4),('movie','w3t',65),('movie','w3t',66),('restaurant','w3t',3),('restaurant','w3t',36),('restaurant','w3t',37),('restaurant','w3t',38),('restaurant','w3t',61),('restaurant','w3t',62),('restaurant','w3t',63),('restaurant','w3t',64),('restaurant','w3t',66);
/*!40000 ALTER TABLE `receipt_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(50) DEFAULT NULL,
  `discription` text,
  `price` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service`
--

LOCK TABLES `service` WRITE;
/*!40000 ALTER TABLE `service` DISABLE KEYS */;
/*!40000 ALTER TABLE `service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store`
--

DROP TABLE IF EXISTS `store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store` (
  `store_account` varchar(50) NOT NULL,
  `pwd` varchar(50) NOT NULL,
  `store_name` varchar(50) NOT NULL,
  `parent_store_account` varchar(50) DEFAULT NULL,
  `store_type` varchar(50) DEFAULT 'normal',
  `register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `verified` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`store_account`),
  KEY `fk_store_store` (`parent_store_account`),
  KEY `fk_store_store_type1` (`store_type`),
  CONSTRAINT `fk_store_store` FOREIGN KEY (`parent_store_account`) REFERENCES `store` (`store_account`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_store_store_type1` FOREIGN KEY (`store_type`) REFERENCES `store_type` (`store_type`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store`
--

LOCK TABLES `store` WRITE;
/*!40000 ALTER TABLE `store` DISABLE KEYS */;
INSERT INTO `store` VALUES ('Mc_NYU','202cb962ac59075b964b07152d234b70','McDonalds(NYU)',NULL,'normal','0000-00-00 00:00:00',0);
/*!40000 ALTER TABLE `store` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_service`
--

DROP TABLE IF EXISTS `store_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_service` (
  `store_account` varchar(50) NOT NULL,
  `service_id` int(11) NOT NULL,
  PRIMARY KEY (`store_account`,`service_id`),
  KEY `fk_store_has_service_service1` (`service_id`),
  KEY `fk_store_has_service_store1` (`store_account`),
  KEY `service_fk` (`service_id`),
  CONSTRAINT `service_fk` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_store_has_service_store1` FOREIGN KEY (`store_account`) REFERENCES `store` (`store_account`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_service`
--

LOCK TABLES `store_service` WRITE;
/*!40000 ALTER TABLE `store_service` DISABLE KEYS */;
/*!40000 ALTER TABLE `store_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_type`
--

DROP TABLE IF EXISTS `store_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_type` (
  `store_type` varchar(50) NOT NULL,
  PRIMARY KEY (`store_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_type`
--

LOCK TABLES `store_type` WRITE;
/*!40000 ALTER TABLE `store_type` DISABLE KEYS */;
INSERT INTO `store_type` VALUES ('normal');
/*!40000 ALTER TABLE `store_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `tag` varchar(20) NOT NULL,
  `user_account` varchar(50) NOT NULL,
  PRIMARY KEY (`tag`,`user_account`),
  KEY `fk_tag_user1` (`user_account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
INSERT INTO `tag` VALUES ('food','w3t'),('gym','w3t'),('movie','w3t'),('play','w3t'),('restaurant','w3t');
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_account` varchar(50) NOT NULL,
  `first_name` varchar(45) NOT NULL,
  `age_range_id` int(11) DEFAULT '1',
  `ethnicity` varchar(45) DEFAULT 'N/A',
  `pwd` varchar(50) NOT NULL,
  `register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `opt_in` tinyint(1) DEFAULT '1',
  `deleted` tinyint(1) DEFAULT '0',
  `verified` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_account`),
  KEY `fk_user_age_range1` (`age_range_id`),
  KEY `age_range_fk` (`age_range_id`),
  CONSTRAINT `age_range_fk` FOREIGN KEY (`age_range_id`) REFERENCES `age_range` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('asasdasd','',NULL,NULL,'202cb962ac59075b964b07152d234b70','2011-06-24 04:52:12',NULL,0,0),('contact_test','contact test user',1,NULL,'202cb962ac59075b964b07152d234b70','2011-06-21 04:45:45',NULL,NULL,0),('new','Brian',NULL,NULL,'202cb962ac59075b964b07152d234b70','2011-07-12 00:42:32',NULL,0,1),('test','test',NULL,NULL,'81dc9bdb52d04dc20036dbd8313ed055','2011-06-24 04:46:07',NULL,0,0),('test123','123',NULL,NULL,'202cb962ac59075b964b07152d234b70','2011-06-24 05:00:51',NULL,0,0),('w3t','W3Tester',1,'N/A','3c8effb5baa6024765b885a77e2902b4','2011-07-07 06:18:28',1,0,1),('w3tAcc','NA',NULL,NULL,'3c8effb5baa6024765b885a77e2902b4','2011-07-18 14:25:16',NULL,0,1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-07-25 13:12:36
