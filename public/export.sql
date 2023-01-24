-- MySQL dump 10.13  Distrib 8.0.31, for Linux (x86_64)
--
-- Host: localhost    Database: MetzConnect
-- ------------------------------------------------------
-- Server version	8.0.31-0ubuntu0.20.04.1

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
-- Table structure for table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorie`
--

LOCK TABLES `categorie` WRITE;
/*!40000 ALTER TABLE `categorie` DISABLE KEYS */;
INSERT INTO `categorie` VALUES (1,'Sécurité'),(2,'Alarme');
/*!40000 ALTER TABLE `categorie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_produit`
--

DROP TABLE IF EXISTS `document_produit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_produit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8DCA4AFBF347EFB` (`produit_id`),
  CONSTRAINT `FK_8DCA4AFBF347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_produit`
--

LOCK TABLES `document_produit` WRITE;
/*!40000 ALTER TABLE `document_produit` DISABLE KEYS */;
INSERT INTO `document_produit` VALUES (5,1,'1-MARBACH-DROIT.pdf');
/*!40000 ALTER TABLE `document_produit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fonction`
--

DROP TABLE IF EXISTS `fonction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fonction` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fonction`
--

LOCK TABLES `fonction` WRITE;
/*!40000 ALTER TABLE `fonction` DISABLE KEYS */;
INSERT INTO `fonction` VALUES (1,'Employé'),(2,'Référant');
/*!40000 ALTER TABLE `fonction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E01FBE6AF347EFB` (`produit_id`),
  CONSTRAINT `FK_E01FBE6AF347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
INSERT INTO `images` VALUES (1,1,'6b89b6b6da361cb40be00e2ee9641509.png'),(2,1,'90e74b7f72162f85c2034f96dd7efbbc.jpg');
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `info_marketing`
--

DROP TABLE IF EXISTS `info_marketing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `info_marketing` (
  `id` int NOT NULL AUTO_INCREMENT,
  `description` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fonctionnalites` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `benefices` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info_marketing`
--

LOCK TABLES `info_marketing` WRITE;
/*!40000 ALTER TABLE `info_marketing` DISABLE KEYS */;
INSERT INTO `info_marketing` VALUES (1,'<p>a</p>','<p>a</p>','<p><strong>B&Eacute;N&Eacute;FICES :&nbsp;</strong></p>\r\n\r\n<p>a</p>');
/*!40000 ALTER TABLE `info_marketing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `info_technique`
--

DROP TABLE IF EXISTS `info_technique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `info_technique` (
  `id` int NOT NULL AUTO_INCREMENT,
  `infoson_id` int DEFAULT NULL,
  `infoalim_id` int DEFAULT NULL,
  `duree_vie` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `compatibilite` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hauteur` double NOT NULL,
  `largeur` double NOT NULL,
  `longueur` double NOT NULL,
  `profondeur` double NOT NULL,
  `poids` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7A4B257CE58E8B2B` (`infoson_id`),
  KEY `IDX_7A4B257C3F1BC91B` (`infoalim_id`),
  CONSTRAINT `FK_7A4B257C3F1BC91B` FOREIGN KEY (`infoalim_id`) REFERENCES `type_alim` (`id`),
  CONSTRAINT `FK_7A4B257CE58E8B2B` FOREIGN KEY (`infoson_id`) REFERENCES `puiss_son` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info_technique`
--

LOCK TABLES `info_technique` WRITE;
/*!40000 ALTER TABLE `info_technique` DISABLE KEYS */;
INSERT INTO `info_technique` VALUES (1,NULL,1,'2','11',100,100,100,100,100);
/*!40000 ALTER TABLE `info_technique` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `last_password`
--

DROP TABLE IF EXISTS `last_password`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `last_password` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `password1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4DA3034BA76ED395` (`user_id`),
  CONSTRAINT `FK_4DA3034BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `last_password`
--

LOCK TABLES `last_password` WRITE;
/*!40000 ALTER TABLE `last_password` DISABLE KEYS */;
INSERT INTO `last_password` VALUES (1,1,'$2y$13$NOoDwuxKYUo97DwtfAZ5E.4wEnduxmHtqJ9gVkometfK7Q1//t/C6',NULL,NULL);
/*!40000 ALTER TABLE `last_password` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_produit`
--

DROP TABLE IF EXISTS `media_produit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_produit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `typemedia_id` int NOT NULL,
  `produit_id` int DEFAULT NULL,
  `lien` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_48726A6EF347EFB` (`produit_id`),
  KEY `IDX_48726A6EB6F653FD` (`typemedia_id`),
  CONSTRAINT `FK_48726A6EB6F653FD` FOREIGN KEY (`typemedia_id`) REFERENCES `type_media` (`id`),
  CONSTRAINT `FK_48726A6EF347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_produit`
--

LOCK TABLES `media_produit` WRITE;
/*!40000 ALTER TABLE `media_produit` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_produit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_user`
--

DROP TABLE IF EXISTS `media_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4ED4099AA76ED395` (`user_id`),
  CONSTRAINT `FK_4ED4099AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_user`
--

LOCK TABLES `media_user` WRITE;
/*!40000 ALTER TABLE `media_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `piece`
--

DROP TABLE IF EXISTS `piece`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `piece` (
  `id` int NOT NULL AUTO_INCREMENT,
  `infotech_id` int DEFAULT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_44CA0B23509CC329` (`infotech_id`),
  CONSTRAINT `FK_44CA0B23509CC329` FOREIGN KEY (`infotech_id`) REFERENCES `info_technique` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `piece`
--

LOCK TABLES `piece` WRITE;
/*!40000 ALTER TABLE `piece` DISABLE KEYS */;
/*!40000 ALTER TABLE `piece` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produit`
--

DROP TABLE IF EXISTS `produit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `infotech_id` int NOT NULL,
  `infomarket_id` int NOT NULL,
  `typeprod_id` int DEFAULT NULL,
  `pu` double NOT NULL,
  `garantie` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_29A5EC27509CC329` (`infotech_id`),
  UNIQUE KEY `UNIQ_29A5EC275BDA013D` (`infomarket_id`),
  KEY `IDX_29A5EC27DB2CB3CE` (`typeprod_id`),
  CONSTRAINT `FK_29A5EC27509CC329` FOREIGN KEY (`infotech_id`) REFERENCES `info_technique` (`id`),
  CONSTRAINT `FK_29A5EC275BDA013D` FOREIGN KEY (`infomarket_id`) REFERENCES `info_marketing` (`id`),
  CONSTRAINT `FK_29A5EC27DB2CB3CE` FOREIGN KEY (`typeprod_id`) REFERENCES `type_prod` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produit`
--

LOCK TABLES `produit` WRITE;
/*!40000 ALTER TABLE `produit` DISABLE KEYS */;
INSERT INTO `produit` VALUES (1,1,1,1,12,'12','11',123);
/*!40000 ALTER TABLE `produit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produit_reglementation`
--

DROP TABLE IF EXISTS `produit_reglementation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produit_reglementation` (
  `produit_id` int NOT NULL,
  `reglementation_id` int NOT NULL,
  PRIMARY KEY (`produit_id`,`reglementation_id`),
  KEY `IDX_A8929AB6F347EFB` (`produit_id`),
  KEY `IDX_A8929AB6D1AD45AF` (`reglementation_id`),
  CONSTRAINT `FK_A8929AB6D1AD45AF` FOREIGN KEY (`reglementation_id`) REFERENCES `reglementation` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_A8929AB6F347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produit_reglementation`
--

LOCK TABLES `produit_reglementation` WRITE;
/*!40000 ALTER TABLE `produit_reglementation` DISABLE KEYS */;
INSERT INTO `produit_reglementation` VALUES (1,1);
/*!40000 ALTER TABLE `produit_reglementation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `puiss_son`
--

DROP TABLE IF EXISTS `puiss_son`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `puiss_son` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mesure` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `puiss_son`
--

LOCK TABLES `puiss_son` WRITE;
/*!40000 ALTER TABLE `puiss_son` DISABLE KEYS */;
/*!40000 ALTER TABLE `puiss_son` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reglementation`
--

DROP TABLE IF EXISTS `reglementation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reglementation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `picto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reglementation`
--

LOCK TABLES `reglementation` WRITE;
/*!40000 ALTER TABLE `reglementation` DISABLE KEYS */;
INSERT INTO `reglementation` VALUES (1,'aa','aa'),(3,'FR','ce-sans-quadrillage-6396f049b9968.jpg');
/*!40000 ALTER TABLE `reglementation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reset_password_request`
--

DROP TABLE IF EXISTS `reset_password_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reset_password_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `selector` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AA76ED395` (`user_id`),
  CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reset_password_request`
--

LOCK TABLES `reset_password_request` WRITE;
/*!40000 ALTER TABLE `reset_password_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `reset_password_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service`
--

LOCK TABLES `service` WRITE;
/*!40000 ALTER TABLE `service` DISABLE KEYS */;
INSERT INTO `service` VALUES (1,'Comptabilité'),(2,'Marketing'),(3,'Commercial'),(4,'Ressources Humaines'),(5,'Service Après Vente'),(6,'Product Owner');
/*!40000 ALTER TABLE `service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `piece_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_97A0ADA3F347EFB` (`produit_id`),
  KEY `IDX_97A0ADA3A76ED395` (`user_id`),
  KEY `IDX_97A0ADA3C40FCFA8` (`piece_id`),
  CONSTRAINT `FK_97A0ADA3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_97A0ADA3C40FCFA8` FOREIGN KEY (`piece_id`) REFERENCES `piece` (`id`),
  CONSTRAINT `FK_97A0ADA3F347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket`
--

LOCK TABLES `ticket` WRITE;
/*!40000 ALTER TABLE `ticket` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_alim`
--

DROP TABLE IF EXISTS `type_alim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `type_alim` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_alim`
--

LOCK TABLES `type_alim` WRITE;
/*!40000 ALTER TABLE `type_alim` DISABLE KEYS */;
INSERT INTO `type_alim` VALUES (1,'1');
/*!40000 ALTER TABLE `type_alim` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_media`
--

DROP TABLE IF EXISTS `type_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `type_media` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_media`
--

LOCK TABLES `type_media` WRITE;
/*!40000 ALTER TABLE `type_media` DISABLE KEYS */;
/*!40000 ALTER TABLE `type_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_prod`
--

DROP TABLE IF EXISTS `type_prod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `type_prod` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_prod`
--

LOCK TABLES `type_prod` WRITE;
/*!40000 ALTER TABLE `type_prod` DISABLE KEYS */;
INSERT INTO `type_prod` VALUES (1,'1\r\n');
/*!40000 ALTER TABLE `type_prod` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `fonction_id` int NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_freeze` tinyint(1) NOT NULL,
  `date_naissance` date NOT NULL,
  `is_archive` tinyint(1) NOT NULL,
  `usurp` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  KEY `IDX_8D93D649ED5CA9E6` (`service_id`),
  KEY `IDX_8D93D64957889920` (`fonction_id`),
  CONSTRAINT `FK_8D93D64957889920` FOREIGN KEY (`fonction_id`) REFERENCES `fonction` (`id`),
  CONSTRAINT `FK_8D93D649ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,1,'amarbach03@gmail.com','[\"ROLE_ADMIN\"]','$2y$13$NOoDwuxKYUo97DwtfAZ5E.4wEnduxmHtqJ9gVkometfK7Q1//t/C6','Andrew','Marbach',0,'2020-10-29',0,0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vente`
--

DROP TABLE IF EXISTS `vente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categorie_id` int NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentaire` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vente`
--

LOCK TABLES `vente` WRITE;
/*!40000 ALTER TABLE `vente` DISABLE KEYS */;
/*!40000 ALTER TABLE `vente` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-12-15 13:28:13
