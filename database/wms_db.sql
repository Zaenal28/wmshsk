-- MySQL dump 10.13  Distrib 8.4.9, for Win64 (x86_64)
--
-- Host: localhost    Database: wms_db
-- ------------------------------------------------------
-- Server version	8.4.9

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
-- Table structure for table `barang_keluar_items`
--

DROP TABLE IF EXISTS `barang_keluar_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barang_keluar_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `barang_keluar_id` bigint unsigned NOT NULL,
  `barang_id` bigint unsigned DEFAULT NULL,
  `no_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(200) NOT NULL,
  `tanggal_expired` date DEFAULT NULL,
  `jumlah` int NOT NULL DEFAULT '0',
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `barang_keluar_items_barang_id_foreign` (`barang_id`),
  KEY `barang_keluar_items_barang_keluar_id_no_barang_index` (`barang_keluar_id`,`no_barang`),
  KEY `barang_keluar_items_no_barang_index` (`no_barang`),
  CONSTRAINT `barang_keluar_items_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `barang_keluar_items_barang_keluar_id_foreign` FOREIGN KEY (`barang_keluar_id`) REFERENCES `barang_keluars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barang_keluar_items`
--

LOCK TABLES `barang_keluar_items` WRITE;
/*!40000 ALTER TABLE `barang_keluar_items` DISABLE KEYS */;
INSERT INTO `barang_keluar_items` VALUES (16,1,42,'042','Shine & Bright','2028-01-22',1,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(17,1,22,'022','Glowing Gel','2028-01-02',1,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(18,1,35,'035','Brightening Complex','2028-01-15',1,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(19,1,9,'009','AA Sunscreen','2027-12-20',1,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(20,1,39,'039','P-0 Night Cream','2028-01-19',1,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(21,1,36,'036','LG-1 Night Cream','2028-01-16',3,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(22,1,20,'020','AH-1','2027-12-31',1,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(23,1,14,'014','SS Sunscreen','2027-12-25',2,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(24,1,44,'044','Toner For Oily Skin','2028-01-24',1,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(25,1,7,'007','Fw. Oily','2027-12-18',1,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(26,1,6,'006','Fw. Dry & sensitive','2027-12-17',1,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(27,1,29,'029','MS 2','2028-01-09',2,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(28,1,8,'008','Fw. Foam','2027-12-19',1,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(29,1,45,'045','Masker Acne Oles','2028-01-25',1,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(30,1,17,'017','WN Sunscreen','2027-12-28',2,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31'),(31,1,33,'033','Acne Night Cream','2028-01-13',1,NULL,'2026-11-10 07:45:31','2026-11-10 07:45:31');
/*!40000 ALTER TABLE `barang_keluar_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `barang_keluars`
--

DROP TABLE IF EXISTS `barang_keluars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barang_keluars` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_surat` varchar(100) NOT NULL,
  `tanggal_keluar` date NOT NULL,
  `pic` varchar(100) NOT NULL,
  `tujuan` varchar(150) NOT NULL,
  `nama_penerima` varchar(100) DEFAULT NULL,
  `total_jumlah` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `barang_keluars_tanggal_keluar_tujuan_index` (`tanggal_keluar`,`tujuan`),
  KEY `barang_keluars_no_surat_index` (`no_surat`),
  KEY `barang_keluars_tanggal_keluar_index` (`tanggal_keluar`),
  KEY `barang_keluars_pic_index` (`pic`),
  KEY `barang_keluars_tujuan_index` (`tujuan`),
  KEY `barang_keluars_total_jumlah_index` (`total_jumlah`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barang_keluars`
--

LOCK TABLES `barang_keluars` WRITE;
/*!40000 ALTER TABLE `barang_keluars` DISABLE KEYS */;
INSERT INTO `barang_keluars` VALUES (1,'2710 /HSKBIO/ XI /2026','2026-11-10','RAHMA','Marketplace','Zaenal',21,'2026-11-10 07:23:30','2026-11-10 07:45:31');
/*!40000 ALTER TABLE `barang_keluars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `barangs`
--

DROP TABLE IF EXISTS `barangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barangs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no` varchar(50) NOT NULL,
  `supplier` varchar(150) DEFAULT NULL,
  `nama_barang` varchar(200) NOT NULL,
  `status_racikan` varchar(50) NOT NULL DEFAULT 'Non Racikan',
  `jumlah` int NOT NULL DEFAULT '0',
  `tanggal_masuk` date NOT NULL,
  `tanggal_expired` date NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `alokasi_penyimpanan` varchar(150) DEFAULT NULL,
  `satuan` varchar(50) NOT NULL DEFAULT 'Pcs',
  `harga_barang` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `barangs_tanggal_expired_jumlah_index` (`tanggal_expired`,`jumlah`),
  KEY `barangs_no_index` (`no`),
  KEY `barangs_supplier_index` (`supplier`),
  KEY `barangs_nama_barang_index` (`nama_barang`),
  KEY `barangs_jumlah_index` (`jumlah`),
  KEY `barangs_tanggal_masuk_index` (`tanggal_masuk`),
  KEY `barangs_tanggal_expired_index` (`tanggal_expired`),
  KEY `barangs_kategori_index` (`kategori`),
  KEY `barangs_alokasi_penyimpanan_index` (`alokasi_penyimpanan`),
  KEY `barangs_status_racikan_index` (`status_racikan`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barangs`
--

LOCK TABLES `barangs` WRITE;
/*!40000 ALTER TABLE `barangs` DISABLE KEYS */;
INSERT INTO `barangs` VALUES (1,'001','PT BIO KUSUMA','Cleasing Oil','Non Racikan',100,'2026-11-09','2027-12-12','Pembersih','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(2,'002','PT BIO KUSUMA','Eye Make Up Remover','Non Racikan',100,'2026-11-09','2027-12-13','Pembersih','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(3,'003','PT BIO KUSUMA','Micellar Milk','Non Racikan',100,'2026-11-09','2027-12-14','Pembersih','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(4,'004','PT BIO KUSUMA','Micellar Water','Non Racikan',100,'2026-11-09','2027-12-15','Pembersih','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(5,'005','PT BIO KUSUMA','Milky Micellar','Non Racikan',100,'2026-11-09','2027-12-16','Pembersih','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(6,'006','PT BIO KUSUMA','Fw. Dry & sensitive','Non Racikan',99,'2026-11-09','2027-12-17','Sabun','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(7,'007','PT BIO KUSUMA','Fw. Oily','Non Racikan',99,'2026-11-09','2027-12-18','Sabun','Gudang Pusat Lt 1','pcs',110000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(8,'008','PT BIO KUSUMA','Fw. Foam','Non Racikan',99,'2026-11-09','2027-12-19','Sabun','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(9,'009','PT BIO KUSUMA','AA Sunscreen','Non Racikan',99,'2026-11-09','2027-12-20','Sunblock','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(10,'010','PT BIO KUSUMA','Acne Sun Protector','Non Racikan',100,'2026-11-09','2027-12-21','Sunblock','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(11,'011','PT BIO KUSUMA','Flawless Skin','Non Racikan',100,'2026-11-09','2027-12-22','Sunblock','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(12,'012','PT BIO KUSUMA','Glow Sunscreen','Non Racikan',100,'2026-11-09','2027-12-23','Sunblock','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(13,'013','PT BIO KUSUMA','Skin MTX','Non Racikan',100,'2026-11-09','2027-12-24','Sunblock','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(14,'014','PT BIO KUSUMA','SS Sunscreen','Non Racikan',98,'2026-11-09','2027-12-25','Sunblock','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(15,'015','PT BIO KUSUMA','Sunshield','Non Racikan',100,'2026-11-09','2027-12-26','Sunblock','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(16,'016','PT BIO KUSUMA','WL Sunscreen','Non Racikan',100,'2026-11-09','2027-12-27','Sunblock','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(17,'017','PT BIO KUSUMA','WN Sunscreen','Non Racikan',98,'2026-11-09','2027-12-28','Sunblock','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(18,'018','PT BIO KUSUMA','AC-2','Non Racikan',100,'2026-11-09','2027-12-29','Pelembab','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(19,'019','PT BIO KUSUMA','ACSB','Non Racikan',100,'2026-11-09','2027-12-30','Pelembab','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(20,'020','PT BIO KUSUMA','AH-1','Non Racikan',99,'2026-11-09','2027-12-31','Pelembab','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(21,'021','PT BIO KUSUMA','Essential Moist','Non Racikan',100,'2026-11-09','2028-01-01','Pelembab','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(22,'022','PT BIO KUSUMA','Glowing Gel','Non Racikan',99,'2026-11-09','2028-01-02','Pelembab','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(23,'023','PT BIO KUSUMA','Intensive Moist','Non Racikan',100,'2026-11-09','2028-01-03','Pelembab','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(24,'024','PT BIO KUSUMA','WP Moist','Non Racikan',100,'2026-11-09','2028-01-04','Pelembab','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(25,'025','PT BIO KUSUMA','AA Skin Booster','Non Racikan',100,'2026-11-09','2028-01-05','Serum','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(26,'026','PT BIO KUSUMA','AC-1','Non Racikan',100,'2026-11-09','2028-01-06','Serum','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(27,'027','PT BIO KUSUMA','AC-3','Non Racikan',100,'2026-11-09','2028-01-07','Serum','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(28,'028','PT BIO KUSUMA','LUMINOUS GOLD','Non Racikan',100,'2026-11-09','2028-01-08','Serum','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(29,'029','PT BIO KUSUMA','MS 2','Non Racikan',98,'2026-11-09','2028-01-09','Serum','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(30,'030','PT BIO KUSUMA','Radiant White','Non Racikan',100,'2026-11-09','2028-01-10','Serum','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(31,'031','PT BIO KUSUMA','SS Serum','Non Racikan',100,'2026-11-09','2028-01-11','Serum','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(32,'032','PT BIO KUSUMA','Wm Serum','Non Racikan',100,'2026-11-09','2028-01-12','Serum','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(33,'033','PT BIO KUSUMA','Acne Night Cream','Non Racikan',99,'2026-11-09','2028-01-13','Cream Malam','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(34,'034','PT BIO KUSUMA','Acne Reducer','Non Racikan',100,'2026-11-09','2028-01-14','Cream Malam','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(35,'035','PT BIO KUSUMA','Brightening Complex','Non Racikan',99,'2026-11-09','2028-01-15','Cream Malam','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(36,'036','PT BIO KUSUMA','LG-1 Night Cream','Sudah Diracik',97,'2026-11-09','2028-01-16','Cream Malam','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(37,'037','PT BIO KUSUMA','LG-2 Night Cream','Non Racikan',100,'2026-11-09','2028-01-17','Cream Malam','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(38,'038','PT BIO KUSUMA','LG-3 Night Cream','Non Racikan',100,'2026-11-09','2028-01-18','Cream Malam','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(39,'039','PT BIO KUSUMA','P-0 Night Cream','Non Racikan',99,'2026-11-09','2028-01-19','Cream Malam','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(40,'040','PT BIO KUSUMA','P-1 Night Cream','Non Racikan',100,'2026-11-09','2028-01-20','Cream Malam','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(41,'041','PT BIO KUSUMA','P-2 Night Cream','Non Racikan',100,'2026-11-09','2028-01-21','Cream Malam','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(42,'042','PT BIO KUSUMA','Shine & Bright','Non Racikan',99,'2026-11-09','2028-01-22','Cream Malam','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(43,'043','PT BIO KUSUMA','Brightening Toner','Non Racikan',100,'2026-11-09','2028-01-23','Toner','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(44,'044','PT BIO KUSUMA','Toner For Oily Skin','Non Racikan',99,'2026-11-09','2028-01-24','Toner','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(45,'045','PT BIO KUSUMA','Masker Acne Oles','Non Racikan',99,'2026-11-09','2028-01-25','Masker','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-10 07:45:31'),(46,'046','PT BIO KUSUMA','masker oksigen bening','Non Racikan',100,'2026-11-09','2028-01-26','Masker','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(47,'047','PT BIO KUSUMA','Masker Oksigen biru','Non Racikan',100,'2026-11-09','2028-01-27','Masker','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(48,'048','PT BIO KUSUMA','masker peel off huesday gold/gram','Non Racikan',100,'2026-11-09','2028-01-28','Masker','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(49,'049','PT BIO KUSUMA','masker peel off huesday white/gram','Non Racikan',100,'2026-11-09','2028-01-29','Masker','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(50,'050','PT BIO KUSUMA','Masker skin mtx aloevera hydro shooting','Non Racikan',100,'2026-11-09','2028-01-30','Masker','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(51,'051','PT BIO KUSUMA','Masker skin mtx milky whiteness','Non Racikan',100,'2026-11-09','2028-01-31','Masker','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(52,'052','PT BIO KUSUMA','Masker skin mtx sensitive calming','Non Racikan',100,'2026-11-09','2028-02-01','Masker','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(53,'053','PT BIO KUSUMA','Masker Vit C','Non Racikan',100,'2026-11-09','2028-02-02','Masker','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(54,'054','PT BIO KUSUMA','power oxygen (masker f acne)','Non Racikan',100,'2026-11-09','2028-02-03','Masker','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(55,'055','PT BIO KUSUMA','GL 01 Padat','Non Racikan',100,'2026-11-09','2028-02-04','Bedak','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(56,'056','PT BIO KUSUMA','GL 02 Padat','Non Racikan',100,'2026-11-09','2028-02-05','Bedak','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(57,'057','PT BIO KUSUMA','Powder GL 01','Non Racikan',100,'2026-11-09','2028-02-06','Bedak','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(58,'058','PT BIO KUSUMA','Powder GL 02','Non Racikan',100,'2026-11-09','2028-02-07','Bedak','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(59,'059','PT BIO KUSUMA','Ac Expert','Non Racikan',100,'2026-11-09','2028-02-08','Lainnya','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(60,'060','PT BIO KUSUMA','Bio Gel (BTMO)','Non Racikan',100,'2026-11-09','2028-02-09','Lainnya','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(61,'061','PT BIO KUSUMA','BMO Lotion','Non Racikan',100,'2026-11-09','2028-02-10','Lainnya','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(62,'062','PT BIO KUSUMA','BNS','Non Racikan',100,'2026-11-09','2028-02-11','Lainnya','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(63,'063','PT BIO KUSUMA','Cymi','Non Racikan',100,'2026-11-09','2028-02-12','Lainnya','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(64,'064','PT BIO KUSUMA','Demi Lotion','Non Racikan',100,'2026-11-09','2028-02-13','Lainnya','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(65,'065','PT BIO KUSUMA','HAGE','Non Racikan',100,'2026-11-09','2028-02-14','Lainnya','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(66,'066','PT BIO KUSUMA','Hand Body Lotion','Non Racikan',100,'2026-11-09','2028-02-15','Lainnya','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(67,'067','PT BIO KUSUMA','Shampoo','Non Racikan',100,'2026-11-09','2028-02-16','Lainnya','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(68,'068','PT BIO KUSUMA','Topicare Cream','Non Racikan',100,'2026-11-09','2028-02-17','Lainnya','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(69,'069','PT BIO KUSUMA','Under arm','Non Racikan',100,'2026-11-09','2028-02-18','Lainnya','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(70,'070','PT BIO KUSUMA','Veno c','Non Racikan',100,'2026-11-09','2028-02-19','Lainnya','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(71,'071','PT BIO KUSUMA','Axtan @30','Non Racikan',100,'2026-11-09','2028-02-20','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(72,'072','PT BIO KUSUMA','CUR','Non Racikan',100,'2026-11-09','2028-02-21','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(73,'073','PT BIO KUSUMA','EFC 1 @28','Non Racikan',100,'2026-11-09','2028-02-22','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(74,'074','PT BIO KUSUMA','FC9 @50','Non Racikan',100,'2026-11-09','2028-02-23','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(75,'075','PT BIO KUSUMA','lanadexon','Non Racikan',100,'2026-11-09','2028-02-24','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(76,'076','PT BIO KUSUMA','Levothyroxine @30','Non Racikan',100,'2026-11-09','2028-02-25','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(77,'077','PT BIO KUSUMA','Olibes @30','Non Racikan',100,'2026-11-09','2028-02-26','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(78,'078','PT BIO KUSUMA','Pan Hair @100','Non Racikan',100,'2026-11-09','2028-02-27','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(79,'079','PT BIO KUSUMA','Lanamol','Non Racikan',100,'2026-11-09','2028-02-28','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(80,'080','PT BIO KUSUMA','Glutasol','Non Racikan',100,'2026-11-09','2028-02-29','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(81,'081','PT BIO KUSUMA','Truxantine','Non Racikan',100,'2026-11-09','2028-03-01','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(82,'082','PT BIO KUSUMA','S-thione \'30','Non Racikan',100,'2026-11-09','2028-03-02','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(83,'083','PT BIO KUSUMA','Zinc @30','Non Racikan',100,'2026-11-09','2028-03-03','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(84,'084','PT BIO KUSUMA','flutamol','Non Racikan',100,'2026-11-09','2028-03-04','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(85,'085','PT BIO KUSUMA','oshi sk','Non Racikan',100,'2026-11-09','2028-03-05','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(86,'086','PT BIO KUSUMA','Dohixat','Non Racikan',100,'2026-11-09','2028-03-06','Obat','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(87,'087','PT BIO KUSUMA','concor 2,5 @100','Non Racikan',100,'2026-11-09','2028-03-07','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(88,'088','PT BIO KUSUMA','enzyme + probiotic','Non Racikan',100,'2026-11-09','2028-03-08','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(89,'089','PT BIO KUSUMA','Ester @30','Non Racikan',100,'2026-11-09','2028-03-09','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(90,'090','PT BIO KUSUMA','FOLATEN @30','Non Racikan',100,'2026-11-09','2028-03-10','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(91,'091','PT BIO KUSUMA','joint formula wellness @30','Non Racikan',100,'2026-11-09','2028-03-11','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(92,'092','PT BIO KUSUMA','MONECTO \'20','Non Racikan',100,'2026-11-09','2028-03-12','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(93,'093','PT BIO KUSUMA','NH nano Calcium @60','Non Racikan',100,'2026-11-09','2028-03-13','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(94,'094','PT BIO KUSUMA','NP melantonin lozenges @60','Non Racikan',100,'2026-11-09','2028-03-14','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(95,'095','PT BIO KUSUMA','np VIT D3 1000IU','Non Racikan',100,'2026-11-09','2028-03-15','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(96,'096','PT BIO KUSUMA','omega 3 fish oil @75','Non Racikan',100,'2026-11-09','2028-03-16','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(97,'097','PT BIO KUSUMA','peptisol coklat','Non Racikan',100,'2026-11-09','2028-03-17','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(98,'098','PT BIO KUSUMA','peptisol vanilla','Non Racikan',100,'2026-11-09','2028-03-18','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(99,'099','PT BIO KUSUMA','Pharmaton @60','Non Racikan',100,'2026-11-09','2028-03-19','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(100,'100','PT BIO KUSUMA','plavix @28','Non Racikan',100,'2026-11-09','2028-03-20','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(101,'101','PT BIO KUSUMA','polysilane @40','Non Racikan',100,'2026-11-09','2028-03-21','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(102,'102','PT BIO KUSUMA','pulmicort 0,25 mg','Non Racikan',100,'2026-11-09','2028-03-22','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(103,'103','PT BIO KUSUMA','rozet 10/20mg @30','Non Racikan',100,'2026-11-09','2028-03-23','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(104,'104','PT BIO KUSUMA','ventolin 2,5mg','Non Racikan',100,'2026-11-09','2028-03-24','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(105,'105','PT BIO KUSUMA','zinc with vitamin B6 wellness @60','Non Racikan',100,'2026-11-09','2028-03-25','VIT','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(106,'106','PT BIO KUSUMA','ADS (cairan detox)','Non Racikan',100,'2026-11-09','2028-03-26','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(107,'107','PT BIO KUSUMA','Anastesi 500gr u/ 100 pasien','Non Racikan',100,'2026-11-09','2028-03-27','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(108,'108','PT BIO KUSUMA','ASTRIGEN (cairan acne)','Non Racikan',100,'2026-11-09','2028-03-28','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(109,'109','PT BIO KUSUMA','Cream Massage','Non Racikan',100,'2026-11-09','2028-03-29','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(110,'110','PT BIO KUSUMA','Gel IPL','Non Racikan',100,'2026-11-09','2028-03-30','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(111,'111','PT BIO KUSUMA','Ion RX 1 untuk 20 Pasien','Non Racikan',100,'2026-11-09','2028-03-31','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(112,'112','PT BIO KUSUMA','Ion RX 2 untuk 20 Pasien','Non Racikan',100,'2026-11-09','2028-04-01','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(113,'113','PT BIO KUSUMA','Ion RX 5 untuk 20 Pasien','Non Racikan',100,'2026-11-09','2028-04-02','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(114,'114','PT BIO KUSUMA','Power C u/12 pasien','Non Racikan',100,'2026-11-09','2028-04-03','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(115,'115','PT BIO KUSUMA','Power Retinol u/15 pasien','Non Racikan',100,'2026-11-09','2028-04-04','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(116,'116','PT BIO KUSUMA','purismart u/3 pasien','Non Racikan',100,'2026-11-09','2028-04-05','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(117,'117','PT BIO KUSUMA','Radical White u/4 Pasien','Non Racikan',100,'2026-11-09','2028-04-06','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(118,'118','PT BIO KUSUMA','redensifying prof prog (facial exosome)','Non Racikan',100,'2026-11-09','2028-04-07','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(119,'119','PT BIO KUSUMA','RF Cream','Non Racikan',100,'2026-11-09','2028-04-08','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(120,'120','PT BIO KUSUMA','Skey enzympeel u/25 pasien','Non Racikan',100,'2026-11-09','2028-04-09','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(121,'121','PT BIO KUSUMA','Telomatrik u/ 8 pasien','Non Racikan',100,'2026-11-09','2028-04-10','Treatment Equiement','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(122,'122','PT BIO KUSUMA','eye complex u/ 80 orang','Non Racikan',100,'2026-11-09','2028-04-11','Korean Lux','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(123,'123','PT BIO KUSUMA','hydra cool serum 15ml u/45 orang','Non Racikan',100,'2026-11-09','2028-04-12','Korean Lux','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(124,'124','PT BIO KUSUMA','LIP POLISH k-lux u/150 org','Non Racikan',100,'2026-11-09','2028-04-13','Korean Lux','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(125,'125','PT BIO KUSUMA','MASKER REJUVE u/ 40 orang','Non Racikan',100,'2026-11-09','2028-04-14','Korean Lux','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(126,'126','PT BIO KUSUMA','PEELING K-lux u/80 org','Non Racikan',100,'2026-11-09','2028-04-15','Korean Lux','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(127,'127','PT BIO KUSUMA','Pro health serum 15ml u/45 org','Non Racikan',100,'2026-11-09','2028-04-16','Korean Lux','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(128,'128','PT BIO KUSUMA','sunblock SBD','Non Racikan',100,'2026-11-09','2028-04-17','Korean Lux','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(129,'129','PT BIO KUSUMA','alas bed','Non Racikan',100,'2026-11-09','2028-04-18','Kebutuhan Treatment','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(130,'130','PT BIO KUSUMA','Carbon','Non Racikan',100,'2026-11-09','2028-04-19','Kebutuhan Treatment','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(131,'131','PT BIO KUSUMA','cutton bud','Non Racikan',100,'2026-11-09','2028-04-20','Kebutuhan Treatment','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(132,'132','PT BIO KUSUMA','Ear candle','Non Racikan',100,'2026-11-09','2028-04-21','Kebutuhan Treatment','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(133,'133','PT BIO KUSUMA','gel waxing','Non Racikan',100,'2026-11-09','2028-04-22','Kebutuhan Treatment','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(134,'134','PT BIO KUSUMA','Hairnet','Non Racikan',100,'2026-11-09','2028-04-23','Kebutuhan Treatment','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(135,'135','PT BIO KUSUMA','kapas 75 gr','Non Racikan',100,'2026-11-09','2028-04-24','Kebutuhan Treatment','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(136,'136','PT BIO KUSUMA','PROBE (VAGINA OZONE)','Non Racikan',100,'2026-11-09','2028-04-25','Kebutuhan Treatment','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(137,'137','PT BIO KUSUMA','Tisu Masker','Non Racikan',100,'2026-11-09','2028-04-26','Kebutuhan Treatment','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(138,'138','PT BIO KUSUMA','AVZ','Bahan Racik',100,'2026-11-09','2028-04-27','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(139,'139','PT BIO KUSUMA','BRU','Bahan Racik',100,'2026-11-09','2028-04-28','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(140,'140','PT BIO KUSUMA','Clindamicin 150','Bahan Racik',100,'2026-11-09','2028-04-29','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(141,'141','PT BIO KUSUMA','Clindamicin 300','Bahan Racik',100,'2026-11-09','2028-04-30','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(142,'142','PT BIO KUSUMA','cincort','Bahan Racik',100,'2026-11-09','2028-05-01','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(143,'143','PT BIO KUSUMA','cls','Bahan Racik',100,'2026-11-09','2028-05-02','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(144,'144','PT BIO KUSUMA','DEMY CREAM','Bahan Racik',100,'2026-11-09','2028-05-03','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(145,'145','PT BIO KUSUMA','DSR30g','Bahan Racik',100,'2026-11-09','2028-05-04','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(146,'146','PT BIO KUSUMA','Glucy','Bahan Racik',100,'2026-11-09','2028-05-05','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(147,'147','PT BIO KUSUMA','INS1','Bahan Racik',100,'2026-11-09','2028-05-06','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(148,'148','PT BIO KUSUMA','INS5','Bahan Racik',100,'2026-11-09','2028-05-07','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(149,'149','PT BIO KUSUMA','KSQ','Bahan Racik',100,'2026-11-09','2028-05-08','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(150,'150','PT BIO KUSUMA','KSA1','Bahan Racik',100,'2026-11-09','2028-05-09','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(151,'151','PT BIO KUSUMA','KSA2','Bahan Racik',100,'2026-11-09','2028-05-10','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(152,'152','PT BIO KUSUMA','KSA3','Bahan Racik',100,'2026-11-09','2028-05-11','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(153,'153','PT BIO KUSUMA','KSB3','Bahan Racik',100,'2026-11-09','2028-05-12','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(154,'154','PT BIO KUSUMA','SP1','Bahan Racik',100,'2026-11-09','2028-05-13','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(155,'155','PT BIO KUSUMA','SP2','Bahan Racik',100,'2026-11-09','2028-05-14','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(156,'156','PT BIO KUSUMA','SP3','Bahan Racik',100,'2026-11-09','2028-05-15','Bahan Racik','Gudang Pusat Lt 1','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(157,'157','PT BIO KUSUMA','Mymask surgical face mask','Bahan Racik',100,'2026-11-09','2028-05-16','Bahan Racik','Gudang Pusat Lt 2','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(158,'158','PT BIO KUSUMA','See-U facial tissue','Bahan Racik',100,'2026-11-09','2028-05-17','Bahan Racik','Gudang Pusat Lt 3','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(159,'159','PT BIO KUSUMA','hand towel','Bahan Racik',100,'2026-11-09','2028-05-18','Bahan Racik','Gudang Pusat Lt 4','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(160,'160','PT BIO KUSUMA','Plenty bathroom rolls','Bahan Racik',100,'2026-11-09','2028-05-19','Bahan Racik','Gudang Pusat Lt 5','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(161,'161','PT BIO KUSUMA','SMO','Bahan Racik',100,'2026-11-09','2028-05-20','Bahan Racik','Gudang Pusat Lt 6','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(162,'162','PT BIO KUSUMA','WELLNESS starch blocker @60','Bahan Racik',100,'2026-11-09','2028-05-21','Bahan Racik','Gudang Pusat Lt 7','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(163,'163','PT BIO KUSUMA','Eqinon forte cream 15gr','Bahan Racik',100,'2026-11-09','2028-05-22','Bahan Racik','Gudang Pusat Lt 8','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(164,'164','PT BIO KUSUMA','Reticor 0,1 15gr','Bahan Racik',100,'2026-11-09','2028-05-23','Bahan Racik','Gudang Pusat Lt 9','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32'),(165,'165','PT BIO KUSUMA','Pology B niacinamide 125 gr','Bahan Racik',100,'2026-11-09','2028-05-24','Bahan Racik','Gudang Pusat Lt 10','pcs',100000.00,'2026-11-09 10:54:37','2026-11-09 10:57:32');
/*!40000 ALTER TABLE `barangs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3','i:1;',1794295007),('livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer','i:1794295007;',1794295007);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `barang_keluar_item_id` bigint unsigned DEFAULT NULL,
  `barang_id` bigint unsigned DEFAULT NULL,
  `no_surat_jalan` varchar(100) DEFAULT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `item_description` varchar(200) NOT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `item_unit` varchar(50) NOT NULL DEFAULT 'pcs',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_barang_keluar_item_id_foreign` (`barang_keluar_item_id`),
  KEY `invoice_items_barang_id_foreign` (`barang_id`),
  KEY `invoice_items_invoice_id_item_description_index` (`invoice_id`,`item_description`),
  CONSTRAINT `invoice_items_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barangs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoice_items_barang_keluar_item_id_foreign` FOREIGN KEY (`barang_keluar_item_id`) REFERENCES `barang_keluar_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_invoice` varchar(100) NOT NULL,
  `tanggal_invoice` date NOT NULL,
  `tujuan` varchar(150) NOT NULL,
  `nama_tempat` varchar(200) DEFAULT NULL,
  `alamat_tempat` text,
  `dari_tanggal` date NOT NULL,
  `ke_tanggal` date NOT NULL,
  `sub_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `diskon_persen` decimal(5,2) NOT NULL DEFAULT '20.00',
  `diskon_nominal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_tagihan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_tujuan_dari_tanggal_ke_tanggal_index` (`tujuan`,`dari_tanggal`,`ke_tanggal`),
  KEY `invoices_no_invoice_index` (`no_invoice`),
  KEY `invoices_tanggal_invoice_index` (`tanggal_invoice`),
  KEY `invoices_tujuan_index` (`tujuan`),
  KEY `invoices_dari_tanggal_index` (`dari_tanggal`),
  KEY `invoices_ke_tanggal_index` (`ke_tanggal`),
  KEY `invoices_total_tagihan_index` (`total_tagihan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_11_07_100642_create_notifications_table',2),(5,'2026_11_07_114000_create_barangs_table',3),(6,'2026_11_07_123000_create_barang_keluars_table',4),(7,'2026_11_07_143000_create_invoices_table',5),(8,'2026_11_07_144100_add_alokasi_penyimpanan_to_barangs_table',6),(9,'2026_11_09_174200_add_status_racikan_and_create_racikans_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `racikan_bahans`
--

DROP TABLE IF EXISTS `racikan_bahans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `racikan_bahans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `racikan_id` bigint unsigned NOT NULL,
  `bahan_barang_id` bigint unsigned DEFAULT NULL,
  `no_bahan` varchar(50) DEFAULT NULL,
  `nama_bahan` varchar(200) NOT NULL,
  `jumlah_pakai` int NOT NULL DEFAULT '0',
  `satuan` varchar(50) NOT NULL DEFAULT 'Pcs',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `racikan_bahans_racikan_id_foreign` (`racikan_id`),
  KEY `racikan_bahans_bahan_barang_id_foreign` (`bahan_barang_id`),
  CONSTRAINT `racikan_bahans_bahan_barang_id_foreign` FOREIGN KEY (`bahan_barang_id`) REFERENCES `barangs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `racikan_bahans_racikan_id_foreign` FOREIGN KEY (`racikan_id`) REFERENCES `racikans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `racikan_bahans`
--

LOCK TABLES `racikan_bahans` WRITE;
/*!40000 ALTER TABLE `racikan_bahans` DISABLE KEYS */;
/*!40000 ALTER TABLE `racikan_bahans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `racikans`
--

DROP TABLE IF EXISTS `racikans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `racikans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_racik` varchar(50) NOT NULL,
  `tanggal_racik` date NOT NULL,
  `barang_asal_id` bigint unsigned DEFAULT NULL,
  `barang_hasil_id` bigint unsigned DEFAULT NULL,
  `nama_barang` varchar(200) NOT NULL,
  `jumlah_racik` int NOT NULL DEFAULT '0',
  `tanggal_expired_hasil` date DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `racikans_barang_asal_id_foreign` (`barang_asal_id`),
  KEY `racikans_barang_hasil_id_foreign` (`barang_hasil_id`),
  KEY `racikans_kode_racik_index` (`kode_racik`),
  KEY `racikans_tanggal_racik_index` (`tanggal_racik`),
  KEY `racikans_nama_barang_index` (`nama_barang`),
  CONSTRAINT `racikans_barang_asal_id_foreign` FOREIGN KEY (`barang_asal_id`) REFERENCES `barangs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `racikans_barang_hasil_id_foreign` FOREIGN KEY (`barang_hasil_id`) REFERENCES `barangs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `racikans`
--

LOCK TABLES `racikans` WRITE;
/*!40000 ALTER TABLE `racikans` DISABLE KEYS */;
/*!40000 ALTER TABLE `racikans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('utFa30KdzR90mgy11wXWiqloy8ONtWDwcwvJghK4',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiSTlvWDVYZDRIZUxFT0FSd0w3ZUNsUXBKOGZIT1l3WHU5c1FnQnV4WCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ3OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYWRtaW4vYmFyYW5nLWtlbHVhci8xL3BkZiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRKUUpTLmdmTk5oWjlDNnJmbjlVSHZ1eDJjdVk0a2NqS2NzTHhaeGJWMEFNNnlXRHRjUzI5eSI7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==',1794297667),('xbJT8qQIcAU0alcepEiKPF94K4Whc5hNgdCidhoB',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiMkZTQXZhaTJQQlhNMmpzYmJwa1FzZDFjUU1BU3VDNldhVmRtM294ViI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4va2Vsb2xhLWJhcmFuZyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRKUUpTLmdmTk5oWjlDNnJmbjlVSHZ1eDJjdVk0a2NqS2NzTHhaeGJWMEFNNnlXRHRjUzI5eSI7fQ==',1794297967);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Gudang Pusat','gudangpusat@wms.com',NULL,'$2y$12$JQJS.gfNNhZ9C6rfn9UHvux2cuY4kcjKcsLxZxbV0AM6yWDtcS29y','SKiCS2cA0oDqH8U9kuTfeG9JWaoWsGkacl4tWezP5S3ZT5If5a4fhGSQK3sb','2026-11-07 02:58:01','2026-11-09 10:54:37');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'wms_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-10 15:15:48
