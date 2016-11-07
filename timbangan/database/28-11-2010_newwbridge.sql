-- MySQL dump 10.11
--
-- Host: localhost    Database: newwbridge
-- ------------------------------------------------------
-- Server version	5.0.67-community-nt

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
-- Table structure for table `access_type`
--

DROP TABLE IF EXISTS `access_type`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `access_type` (
  `access_name` varchar(12) NOT NULL default '',
  `status` tinyint(1) NOT NULL default '0',
  `remarks` text,
  PRIMARY KEY  (`access_name`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `access_type`
--

LOCK TABLES `access_type` WRITE;
/*!40000 ALTER TABLE `access_type` DISABLE KEYS */;
INSERT INTO `access_type` VALUES ('detail',1,'Each user can only access the menu items that have been attached to users. This type of access in terms of better security and requires a more complex settings'),('level',0,'This uses the levelization access to menu item. Each user can access all the menu items that have a level less than or equal to user\'s access level');
/*!40000 ALTER TABLE `access_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth`
--

DROP TABLE IF EXISTS `auth`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `auth` (
  `uname` varchar(45) NOT NULL default '',
  `menuid` int(10) unsigned NOT NULL default '0',
  `status` tinyint(3) unsigned NOT NULL default '0',
  `lastuser` varchar(30) default NULL,
  PRIMARY KEY  (`uname`,`menuid`),
  UNIQUE KEY `unique` (`uname`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 11264 kB';
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `auth`
--

LOCK TABLES `auth` WRITE;
/*!40000 ALTER TABLE `auth` DISABLE KEYS */;
INSERT INTO `auth` VALUES ('ITD_DEV',1,1,'Nangkoel'),('ITD_DEV',13,1,'Nangkoel'),('ITD_DEV',14,1,'Nangkoel'),('ITD_DEV',16,1,'Nangkoel'),('ITD_DEV',18,1,'Nangkoel'),('ITD_DEV',19,1,'Nangkoel'),('ITD_DEV',20,1,'Nangkoel'),('ITD_DEV',21,1,'Nangkoel'),('ITD_DEV',22,1,'Nangkoel'),('ITD_DEV',49,1,'ITD_DEV'),('ITD_DEV',50,1,'ITD_DEV'),('ITD_DEV',51,1,'ITD_DEV'),('ITD_DEV',52,1,'ITD_DEV'),('ITD_DEV',53,1,'ITD_DEV'),('barcode',166,0,'ITD_DEV'),('ITD_DEV',166,1,'ITD_DEV'),('barcode',167,1,'ITD_DEV'),('ITD_DEV',167,1,'ITD_DEV'),('barcode',168,1,'ITD_DEV'),('ITD_DEV',168,1,'ITD_DEV'),('barcode',169,1,'ITD_DEV'),('ITD_DEV',169,1,'ITD_DEV'),('barcode',170,1,'ITD_DEV'),('ITD_DEV',170,1,'ITD_DEV'),('ITD_DEV',171,1,'ITD_DEV'),('barcode',172,0,'ITD_DEV'),('ITD_DEV',172,1,'ITD_DEV'),('barcode',174,0,'ITD_DEV'),('ITD_DEV',174,1,'ITD_DEV'),('ITD_DEV',175,1,'ITD_DEV'),('ITD_DEV',176,1,'ITD_DEV'),('ITD_DEV',185,1,'ITD_DEV'),('ITD_DEV',186,1,'ITD_DEV'),('ITD_DEV',187,1,'ITD_DEV'),('ITD_DEV',188,1,'ITD_DEV'),('ITD_DEV',189,1,'ITD_DEV'),('ITD_DEV',190,1,'ITD_DEV'),('ITD_DEV',191,1,'ITD_DEV'),('ITD_DEV',192,1,'ITD_DEV'),('ITD_DEV',193,1,'ITD_DEV'),('ITD_DEV',194,1,'ITD_DEV'),('ITD_DEV',195,1,'ITD_DEV'),('ITD_DEV',196,1,'ITD_DEV'),('admin',1,1,'test123'),('admin',13,1,'test123'),('admin',18,1,'test123'),('admin',19,1,'test123'),('admin',20,1,'test123'),('admin',21,1,'test123'),('admin',22,1,'ITD_DEV'),('admin',14,1,'ITD_DEV'),('admin',49,1,'ITD_DEV'),('admin',50,1,'ITD_DEV'),('admin',51,1,'ITD_DEV'),('admin',52,1,'ITD_DEV'),('admin',53,1,'ITD_DEV'),('admin',16,1,'ITD_DEV'),('admin',197,1,'visione'),('admin',198,1,'visione'),('admin',199,1,'visione'),('admin',200,1,'visione'),('admin',201,1,'visione'),('admin',203,1,'visione'),('admin',204,1,'visione'),('admin',205,1,'visione'),('admin',206,1,'visione'),('admin',207,1,'visione'),('admin',208,1,'admin'),('admin',209,1,'visione'),('admin',210,1,'visione'),('admin',211,1,'visione'),('admin',212,1,'visione'),('admin',213,1,'visione'),('admin',214,1,'visione'),('admin',215,1,'visione'),('admin',216,1,'visione'),('admin',217,1,'visione'),('admin',218,1,'admin'),('admin',219,1,'visione'),('admin',220,1,'visione'),('admin',221,1,'visione'),('admin',222,1,'visione'),('admin',223,1,'visione'),('admin',224,1,'visione'),('admin',225,1,'admin'),('admin',226,1,'admin'),('admin',227,1,'visione'),('admin',228,1,'visione'),('admin',229,1,'visione'),('admin',230,1,'visione'),('admin',232,1,'visione'),('admin',233,1,'visione'),('admin',231,1,'visione'),('admin',234,1,'visione'),('admin',235,1,'visione'),('admin',236,1,'visione'),('admin',237,1,'visione'),('admin',239,1,'visione'),('admin',240,1,'visione'),('admin',241,1,'visione'),('admin',242,1,'visione'),('admin',243,1,'visione'),('admin',244,1,'visione'),('admin',245,1,'visione'),('admin',246,1,'visione'),('admin',238,1,'visione'),('admin',247,1,'visione'),('admin',248,1,'visione'),('admin',249,1,'visione'),('admin',250,1,'visione'),('admin',251,1,'visione'),('admin',252,1,'visione'),('admin',253,1,'visione'),('admin',254,1,'visione'),('admin',255,1,'visione'),('admin',256,1,'visione'),('admin',257,1,'visione'),('admin',258,1,'visione'),('admin',259,1,'visione'),('admin',260,1,'visione'),('demo_beon',197,1,'visione'),('demo_beon',203,1,'visione'),('demo_beon',204,1,'visione'),('demo_beon',205,1,'visione'),('demo_beon',207,1,'visione'),('demo_beon',206,1,'visione'),('demo_beon',208,1,'visione'),('demo_beon',209,1,'visione'),('demo_beon',210,1,'visione'),('demo_beon',211,1,'visione'),('demo_beon',212,1,'visione'),('demo_beon',213,1,'visione'),('demo_beon',214,1,'visione'),('demo_beon',215,1,'visione'),('demo_beon',216,1,'visione'),('demo_beon',217,1,'visione'),('demo_beon',218,1,'visione'),('demo_beon',219,1,'visione'),('demo_beon',220,1,'visione'),('demo_beon',221,1,'visione'),('demo_beon',222,1,'visione'),('demo_beon',223,1,'visione'),('demo_beon',224,1,'visione'),('demo_beon',225,1,'visione'),('demo_beon',226,1,'visione'),('demo_beon',227,1,'visione'),('demo_beon',228,1,'visione'),('demo_beon',229,1,'visione'),('demo_beon',230,1,'visione'),('demo_beon',232,1,'visione'),('demo_beon',233,1,'visione'),('demo_beon',231,1,'visione'),('demo_beon',234,1,'visione'),('demo_beon',235,1,'visione'),('demo_beon',236,1,'visione'),('demo_beon',237,1,'visione'),('demo_beon',239,1,'visione'),('demo_beon',240,1,'visione'),('demo_beon',241,1,'visione'),('demo_beon',242,1,'visione'),('demo_beon',243,1,'visione'),('demo_beon',244,1,'visione'),('demo_beon',245,1,'visione'),('demo_beon',246,1,'visione'),('demo_beon',238,1,'visione'),('demo_beon',247,1,'visione'),('demo_beon',248,1,'visione'),('demo_beon',249,1,'visione'),('demo_beon',250,1,'visione'),('demo_beon',251,1,'visione'),('demo_beon',252,1,'visione'),('demo_beon',253,1,'visione'),('demo_beon',254,1,'visione'),('demo_beon',255,1,'visione'),('demo_beon',256,1,'visione'),('demo_beon',257,1,'visione'),('demo_beon',258,1,'visione'),('demo_beon',259,1,'visione'),('demo_beon',260,1,'visione'),('admin',261,1,'visione'),('admin',262,1,'visione'),('demo_beon',51,1,'visione'),('demo_beon',53,1,'visione'),('demo_beon',1,0,'visione'),('demo_beon',13,0,'visione'),('demo_beon',18,0,'visione'),('demo_beon',19,0,'visione'),('demo_beon',20,0,'visione'),('demo_beon',21,0,'visione'),('demo_beon',22,0,'visione'),('admin',263,1,'admin'),('admin',264,1,'admin'),('admin',265,1,'admin'),('admin',266,1,'admin'),('admin',267,1,'admin'),('admin',268,1,'admin'),('admin',269,1,'admin'),('admin',270,1,'admin'),('admin',271,1,'admin'),('admin',272,1,'admin'),('admin',273,1,'admin'),('admin',274,1,'admin'),('operator',215,1,'admin'),('operator',216,1,'admin'),('operator',217,1,'admin'),('operator',270,1,'admin'),('operator',271,1,'admin'),('operator',218,1,'admin'),('operator',225,1,'admin'),('operator',226,1,'admin'),('operator',272,1,'admin'),('operator',273,1,'admin'),('operator',236,1,'admin'),('operator',237,1,'admin'),('operator',249,1,'admin'),('operator',248,1,'admin'),('operator',247,1,'admin'),('operator',238,1,'admin'),('operator',240,1,'admin'),('operator',239,1,'admin'),('operator',256,0,'admin'),('admin',276,1,'admin'),('admin',278,1,'admin'),('admin',284,1,'admin'),('admin',283,1,'admin'),('admin',277,1,'admin'),('admin',282,1,'admin'),('admin',281,1,'admin'),('admin',280,1,'admin'),('admin',279,1,'admin'),('admin',285,1,'test123'),('admin',287,1,'admin'),('operator',279,1,'admin'),('operator',280,1,'admin'),('operator',281,1,'admin'),('operator',282,1,'admin'),('operator',285,1,'admin'),('operator',277,1,'admin'),('operator',283,1,'admin'),('operator',284,1,'admin'),('operator',278,1,'admin'),('operator',287,1,'admin'),('admin',288,1,'admin'),('operator',288,1,'admin'),('admin',289,1,'admin'),('operator',289,1,'admin'),('admin',290,1,'admin'),('admin',291,1,'admin'),('operator',290,1,'admin'),('operator',291,1,'admin'),('adminpusat',239,1,'admin'),('adminpusat',218,1,'admin'),('adminpusat',225,1,'admin'),('adminpusat',226,1,'admin'),('adminpusat',273,1,'admin'),('adminpusat',236,1,'admin'),('adminpusat',237,1,'admin'),('adminpusat',271,1,'admin'),('adminpusat',270,1,'admin'),('adminpusat',269,1,'admin'),('adminpusat',215,1,'admin'),('adminpusat',216,1,'admin'),('adminpusat',217,1,'admin'),('adminpusat',267,1,'admin'),('adminpusat',21,1,'admin'),('adminpusat',14,1,'admin'),('adminpusat',49,1,'admin'),('adminpusat',50,1,'admin'),('adminpusat',51,1,'admin'),('adminpusat',52,1,'admin'),('adminpusat',53,1,'admin'),('adminpusat',16,1,'admin'),('adminpusat',197,1,'admin'),('adminpusat',203,1,'admin'),('adminpusat',261,1,'admin'),('adminpusat',262,1,'admin'),('adminpusat',204,1,'admin'),('adminpusat',205,1,'admin'),('adminpusat',208,1,'admin'),('adminpusat',209,1,'admin'),('adminpusat',210,1,'admin'),('adminpusat',263,1,'admin'),('adminpusat',264,1,'admin'),('adminpusat',265,1,'admin'),('adminpusat',266,1,'admin'),('adminpusat',1,1,'admin'),('adminpusat',13,1,'admin'),('adminpusat',20,0,'admin'),('admin',292,1,'admin'),('adminpusat',279,1,'admin'),('adminpusat',268,1,'admin'),('adminpusat',280,1,'admin'),('adminpusat',281,1,'admin'),('adminpusat',282,1,'admin'),('adminpusat',285,1,'admin'),('adminpusat',277,1,'admin'),('adminpusat',283,1,'admin'),('adminpusat',284,1,'admin'),('adminpusat',278,1,'admin'),('adminpusat',238,1,'admin'),('adminpusat',247,1,'admin'),('adminpusat',287,1,'admin'),('adminpusat',249,1,'admin'),('adminpusat',256,1,'admin'),('adminpusat',257,1,'admin'),('adminpusat',258,1,'admin'),('adminpusat',259,1,'admin'),('adminpusat',274,1,'admin'),('adminpusat',288,1,'admin'),('adminpusat',289,1,'admin'),('adminpusat',292,1,'admin'),('adminpusat',290,1,'admin'),('adminpusat',291,1,'admin');
/*!40000 ALTER TABLE `auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bpssvr`
--

DROP TABLE IF EXISTS `bpssvr`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `bpssvr` (
  `wilayah` varchar(4) NOT NULL,
  `addr` varchar(15) NOT NULL,
  `name` varchar(25) default 'BPS SVR',
  `port` int(4) unsigned default '3306',
  PRIMARY KEY  (`wilayah`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `bpssvr`
--

LOCK TABLES `bpssvr` WRITE;
/*!40000 ALTER TABLE `bpssvr` DISABLE KEYS */;
/*!40000 ALTER TABLE `bpssvr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `menu` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(20) NOT NULL default 'list' COMMENT 'master,parent,list',
  `class` varchar(20) default NULL COMMENT 'title,devider,click',
  `caption` varchar(40) default NULL COMMENT 'tampilan kecuali devider',
  `action` varchar(45) default NULL COMMENT 'tujuan  aksi dari menu',
  `access_level` int(10) unsigned NOT NULL default '0' COMMENT 'optional untuk grouping access',
  `parent` int(10) unsigned NOT NULL default '0' COMMENT 'jika child 1 maka harus di isi dengan id parent',
  `urut` int(10) unsigned NOT NULL default '0' COMMENT 'urutan dari kiri ke kana untuk master',
  `hide` tinyint(1) default '0',
  `lastupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `lastuser` varchar(30) default 'system' COMMENT 'username',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=293 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'master','click','Administrator','',17,0,1,0,'2009-07-17 21:27:46','Nangkoel'),(13,'parent','click','Menu Manager','',17,1,1,0,'2009-10-08 03:49:22','ITD_DEV'),(14,'parent','click','Users Settings','Action...',17,1,2,0,'2009-10-08 03:49:22','ITD_DEV'),(16,'list','click','Org.Chart','main_orgChart',17,1,4,0,'2009-10-06 09:42:56','Nangkoel'),(18,'list','title','Menu Manager','',0,13,1,0,'2009-05-18 19:06:35','Nangkoel'),(19,'list','devider','','',0,13,2,0,'2009-05-18 19:07:30','Nangkoel'),(20,'list','click','Menu Settings','main_menuSettings',17,13,3,0,'2009-10-06 09:42:43','Nangkoel'),(21,'list','click','User Privillage','main_userPrivillages',17,13,4,0,'2009-10-06 09:42:45','Nangkoel'),(22,'list','click','Parent-Child Menu Arranger','main_parentChild',17,13,6,0,'2009-10-06 09:42:47','Nangkoel'),(49,'list','title','User Settings:','',0,14,1,0,'0000-00-00 00:00:00','0'),(50,'list','devider','','',0,14,2,0,'0000-00-00 00:00:00','0'),(51,'list','click','Add New User','main_newUser',17,14,3,0,'2009-10-06 09:42:50','0'),(52,'list','click','Active/Deactive/Delete User','main_activeUser',17,14,4,0,'2009-10-06 09:42:52','0'),(53,'list','click','Reset Password','main_resetPassword',17,14,6,0,'2009-10-06 09:42:55','0'),(197,'master','click','MASTER','null',0,0,2,0,'2010-08-02 06:18:24','visione'),(203,'parent','click','Pembagian Area','',0,197,1,0,'2010-08-28 14:08:28','visione'),(204,'list','click','Unit','master_unit',0,203,3,0,'2010-08-28 14:16:10','visione'),(205,'list','click','Afdeling','master_divisi',0,203,4,0,'2010-09-03 15:33:12','visione'),(208,'parent','click','Pihak Ketiga','',0,197,2,0,'2010-08-29 19:17:24','visione'),(209,'list','click','Supplier TBS Eksternal / Selain TBS','master_vendor',0,208,1,0,'2010-11-26 07:47:27','visione'),(210,'list','click','Pembeli CPO/PK/KERNEL','master_customer',0,208,2,0,'2010-08-28 14:31:48','visione'),(265,'list','click','Detail Kendaraan','master_kendaraan',0,263,2,0,'2010-09-05 05:08:53','admin'),(264,'list','click','Tipe Kendaraan','master_tipe_kendaraan',0,263,1,0,'2010-08-29 19:26:43','admin'),(263,'parent','click','Kendaraan',' ',0,197,3,0,'2010-08-29 19:26:00','admin'),(215,'master','click','TRANSAKSI','null',0,0,3,0,'2010-09-01 07:34:37','visione'),(216,'parent','click','Penerimaan Barang',' ',0,215,1,0,'2010-09-01 07:38:08','visione'),(217,'parent','click','Penerimaan TBS',' ',0,216,1,0,'2010-09-01 07:40:04','visione'),(218,'list','click','Lain-Lain','lpt_lain_form',0,216,2,0,'2010-11-25 05:50:59','visione'),(272,'list','click','Composting','trx_composting',0,225,2,1,'2010-11-25 06:46:31','admin'),(225,'parent','click','Pengiriman Barang',' ',0,215,2,0,'2010-09-01 16:23:56','visione'),(226,'list','click','Penjualan CPO,PK,KERNEL','trx_cpo_pk',0,225,1,0,'2010-09-01 16:00:33','visione'),(273,'list','click','Selain CPO,PK,KERNEL','trx_pengiriman_barang',0,225,3,0,'2010-09-01 16:25:52','admin'),(287,'list','click','Realisasi SIPB','lpt_realisasi_sipb_form',0,247,1,0,'2010-11-25 06:57:12','admin'),(288,'master','click','MY ACCOUNT','null',0,0,8,0,'2010-11-26 08:18:34','admin'),(236,'master','click','LAPORAN','null',0,0,5,0,'2010-08-03 03:40:12','visione'),(237,'parent','click','Penerimaan Barang',' ',0,236,1,0,'2010-09-01 16:56:48','visione'),(238,'parent','click','Pengiriman Barang','  ',0,236,2,0,'2010-09-01 17:19:51','visione'),(239,'parent','click','Penerimaan TBS Internal',' ',0,237,1,0,'2010-11-24 11:02:03','visione'),(281,'list','click','Per Tanggal','lpt_int_per_tgl_form',0,239,3,0,'2010-11-24 11:19:14','admin'),(282,'list','click','Monitoring Per Jam','lpt_monitor_int_jam_form',0,239,4,0,'2010-11-24 11:17:58','admin'),(283,'list','click','Per Tanggal','lpt_eks_per_tgl_form',0,277,1,0,'2010-11-24 11:19:03','admin'),(285,'list','click','Rekap Per Bulan','lpt_per_bln_form',0,239,5,0,'2010-11-24 17:25:02','test123'),(284,'list','click','Monitoring Per Jam','lpt_monitor_eks_jam_form',0,277,2,0,'2010-11-24 11:19:49','admin'),(247,'parent','click','CPO,PK,KERNEL',' ',0,238,1,0,'2010-11-25 06:53:06','visione'),(248,'list','click','Composting','lap_composting',0,238,2,1,'2010-11-25 06:46:56','admin'),(249,'list','click','Selain CPO,PK,KERNEL','lpt_non_cpo_form',0,238,3,0,'2010-11-25 08:15:08','visione'),(277,'parent','click','Penerimaan TBS Eksternal','lpt_eks_per_tgl_form',0,237,2,0,'2010-11-24 11:19:00','admin'),(278,'list','click','Lain-Lain','lpt_lain_form',0,237,3,0,'2010-11-24 11:10:19','admin'),(279,'list','click','Unit/Tanggal','lpt_per_unit_form',0,239,1,0,'2010-11-24 11:18:04','admin'),(280,'list','click','Afdeling/Tanggal','lpt_per_div_form',0,239,2,0,'2010-11-24 11:18:01','admin'),(256,'master','click','UTILITY','null',0,0,7,0,'2010-08-03 04:00:47','visione'),(257,'list','click','BackUp Data','dump',0,256,1,0,'2010-11-26 09:38:52','visione'),(258,'list','click','Restore Data','restore',0,256,2,0,'2010-08-03 04:00:53','visione'),(259,'list','click','Parameter Dan Konfigurasi System','master_system',0,256,3,0,'2010-11-25 15:22:48','visione'),(274,'list','click','Hapus No. Tiket','master_hapus_tiket',0,256,4,0,'2010-11-26 04:45:11','admin'),(261,'list','click','Wilayah','master_wilayah',0,203,1,0,'2010-08-28 14:12:57','visione'),(262,'list','click','Perusahaan','master_company',0,203,2,0,'2010-08-28 14:15:42','visione'),(266,'list','click','Product','master_product',0,197,4,0,'2010-09-01 06:41:16','admin'),(267,'parent','click','Dokumen Penjualan',' ',0,197,5,0,'2010-09-01 07:28:45','admin'),(268,'list','click','Kontrak Penjualan','master_kontrak',0,267,1,0,'2010-09-01 07:33:03','admin'),(269,'list','click','SIPB','master_sipb',0,267,2,0,'2010-09-01 07:33:05','admin'),(270,'list','click','Internal','trx_tbs_int',0,217,1,0,'2010-09-01 07:41:16','admin'),(271,'list','click','Eksternal/Buah Luar','trx_tbs_ext',0,217,2,0,'2010-09-01 07:41:20','admin'),(289,'list','click','Rubah Password','main_changePassword',0,288,1,0,'2010-11-26 12:23:04','Nangkoel'),(290,'master','click','HOME','null',0,0,9,0,'2010-11-26 12:59:10','admin'),(291,'list','click','Show Home','master',0,290,1,0,'2010-11-26 13:01:25','admin');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msbulking`
--

DROP TABLE IF EXISTS `msbulking`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msbulking` (
  `DOCNO` char(30) NOT NULL,
  `DOCDATE` date NOT NULL,
  `DOCQTY` int(11) default NULL,
  `DESCRIPTION` char(50) default NULL,
  `DOCSTATUS` char(15) default NULL,
  `USERID` char(15) default NULL,
  `CREATEDATE` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`DOCNO`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='master table u/ transaksi transfer antar gudang';
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msbulking`
--

LOCK TABLES `msbulking` WRITE;
/*!40000 ALTER TABLE `msbulking` DISABLE KEYS */;
INSERT INTO `msbulking` VALUES ('007/HOCD/SIPB/IV','2009-04-22',0,'Koreksi dari salah timbang','Aktif','admin','2009-05-06 09:43:07'),('008/HO-CD/SIPB/X','2009-10-14',0,'Sesuai email comercial tgl 14/10/09','Aktif','nurjayanto','2009-10-23 07:43:32');
/*!40000 ALTER TABLE `msbulking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mscompany`
--

DROP TABLE IF EXISTS `mscompany`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mscompany` (
  `COMPCODE` char(4) NOT NULL,
  `COMPNAME` char(40) default NULL,
  `WILCODE` char(4) NOT NULL,
  `COMPADDR` char(40) default NULL,
  `COMPCITY` char(35) default NULL,
  `COMPSTATUS` char(15) default NULL,
  `USERID` char(10) default NULL,
  `CREATEDATE` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`COMPCODE`,`WILCODE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mscompany`
--

LOCK TABLES `mscompany` WRITE;
/*!40000 ALTER TABLE `mscompany` DISABLE KEYS */;
INSERT INTO `mscompany` VALUES ('PMO','PERKEBUNAN MINANGA OGAN, PT','SSRO','DS LUBUK BATANG OKU SUMATERA SELATAN','BATURAJA',NULL,'admin','2010-11-23 06:12:06'),('KMT','KARTIKA MANGESTITAMA, PT','SSRO','WAY KANAN','LAMPUNG',NULL,'admin','2010-11-23 06:14:15'),('GMJ','GUNUNG MERAKSA JAYA, PT','SSRO','DS LUBUK BATANG OKU SUMATERA SELATAN','BATURAJA',NULL,'admin','2010-11-23 06:15:16');
/*!40000 ALTER TABLE `mscompany` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mscontract`
--

DROP TABLE IF EXISTS `mscontract`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mscontract` (
  `CTRNO` char(30) NOT NULL,
  `CTRDATE` date NOT NULL,
  `BUYERCODE` char(10) NOT NULL,
  `CTRQTY` int(11) NOT NULL,
  `DESCRIPTION` char(50) default NULL,
  `CTRSTATUS` char(15) NOT NULL,
  `USERID` char(10) default NULL,
  `CREATEDATE` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `MILLCODE` varchar(6) NOT NULL default 'WNLL',
  PRIMARY KEY  (`CTRNO`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mscontract`
--

LOCK TABLES `mscontract` WRITE;
/*!40000 ALTER TABLE `mscontract` DISABLE KEYS */;
/*!40000 ALTER TABLE `mscontract` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mscontractoil`
--

DROP TABLE IF EXISTS `mscontractoil`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mscontractoil` (
  `CTRNO` char(50) NOT NULL,
  `CTRDATE` date NOT NULL,
  `BUYERCODE` char(10) NOT NULL,
  `CTRQTY` int(11) NOT NULL,
  `DESCRIPTION` char(50) default NULL,
  `CTRSTATUS` char(15) NOT NULL,
  `USERID` char(10) default NULL,
  `CREATEDATE` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`CTRNO`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mscontractoil`
--

LOCK TABLES `mscontractoil` WRITE;
/*!40000 ALTER TABLE `mscontractoil` DISABLE KEYS */;
/*!40000 ALTER TABLE `mscontractoil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msdivisi`
--

DROP TABLE IF EXISTS `msdivisi`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msdivisi` (
  `COMPCODE` char(4) NOT NULL,
  `UNITCODE` char(4) NOT NULL,
  `DIVCODE` char(6) NOT NULL,
  `DIVNAME` char(40) default NULL,
  `DIVSTATUS` char(15) default NULL,
  `USERID` char(10) default NULL,
  `CREATEDATE` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `TIPEDIVISI` varchar(15) default NULL,
  PRIMARY KEY  (`COMPCODE`,`UNITCODE`,`DIVCODE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msdivisi`
--

LOCK TABLES `msdivisi` WRITE;
/*!40000 ALTER TABLE `msdivisi` DISABLE KEYS */;
INSERT INTO `msdivisi` VALUES ('PMO','SENE','SENE05','AFDELING 5 SEI ENAI',NULL,'admin','2010-11-23 06:30:31',NULL),('PMO','SENE','SENE06','AFDELING 6 SEI ENAI',NULL,'admin','2010-11-23 07:46:49',NULL),('PMO','SENE','SENE07','AFDELING 7 SEI ENAI',NULL,'admin','2010-11-23 07:47:42',NULL),('PMO','SENE','SENE08','AFDELING 8 SEI ENAI',NULL,'admin','2010-11-23 07:48:26',NULL),('PMO','SENE','SENE09','AFDELING 9 SEI ENAI',NULL,'admin','2010-11-23 07:49:06',NULL),('PMO','SOGE','SOGE01','AFDELING 1 SEI OGAN',NULL,'admin','2010-11-23 07:50:24',NULL),('PMO','SOGE','SOGE02','AFDELING 2 SEI OGAN',NULL,'admin','2010-11-23 07:51:17',NULL),('PMO','SOGE','SOGE03','AFDELING 3 SEI OGAN',NULL,'admin','2010-11-23 07:51:40',NULL),('PMO','SOGE','SOGE04','AFDELING 4 SEI OGAN',NULL,'admin','2010-11-23 07:52:23',NULL),('KMT','WKNE','WKNE01','AFDELING 1 WAY KANAN',NULL,'admin','2010-11-23 07:53:36',NULL),('KMT','WKNE','WKNE02','AFDELING 2 WAY KANAN',NULL,'admin','2010-11-23 07:54:09',NULL),('KMT','WKNE','WKNE03','AFDELING 3 WAY KANAN',NULL,'admin','2010-11-23 07:54:32',NULL),('KMT','WKNE','WKNE04','AFDELING 4 WAY KANAN',NULL,'admin','2010-11-23 07:54:58',NULL),('GMJ','MRKE','MRKE01','AFDELING 1 MRKE',NULL,'admin','2010-11-23 07:55:41',NULL);
/*!40000 ALTER TABLE `msdivisi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msemail`
--

DROP TABLE IF EXISTS `msemail`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msemail` (
  `id` int(11) NOT NULL auto_increment,
  `EMAIL` varchar(45) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Pengiriman Email Error Log';
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msemail`
--

LOCK TABLES `msemail` WRITE;
/*!40000 ALTER TABLE `msemail` DISABLE KEYS */;
/*!40000 ALTER TABLE `msemail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msgrading`
--

DROP TABLE IF EXISTS `msgrading`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msgrading` (
  `TICKETNO` int(8) NOT NULL,
  `SPBNO` char(20) NOT NULL,
  `TBSDATETRX` datetime NOT NULL,
  `BRUTO` int(11) default NULL,
  `TARRA` int(11) default NULL,
  `TBSMTH` int(11) default NULL,
  `TBSSTHMTG` int(11) default NULL,
  `TBSMTG` int(11) default NULL,
  `TBSTLLMTG` int(11) default NULL,
  `TBSJJGKSG` int(11) default NULL,
  `TBSKERAS` int(11) default NULL,
  `PARTHENOCARPLE` int(11) default NULL,
  `TANGKAIPANJANG` int(11) default NULL,
  `BRDLPS` int(11) default NULL,
  `BRDBUSUK` int(11) default NULL,
  `USERID` char(15) default NULL,
  `CREATEDATE` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`TICKETNO`,`SPBNO`,`TBSDATETRX`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msgrading`
--

LOCK TABLES `msgrading` WRITE;
/*!40000 ALTER TABLE `msgrading` DISABLE KEYS */;
/*!40000 ALTER TABLE `msgrading` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msproduct`
--

DROP TABLE IF EXISTS `msproduct`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msproduct` (
  `PRODUCTCODE` char(8) NOT NULL,
  `PRODUCTNAME` char(30) default NULL,
  `PRODUCTSTATUS` char(15) default NULL,
  `USERID` char(10) default NULL,
  `CREATEDATE` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `GI` tinyint(4) default '0' COMMENT 'GOODS ISSUE',
  `GR` tinyint(4) default '0' COMMENT 'GOODS RECEIVE',
  `TF` tinyint(4) default '0' COMMENT 'TRANSFER BULKING',
  `BPS` varchar(45) default '0',
  PRIMARY KEY  (`PRODUCTCODE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msproduct`
--

LOCK TABLES `msproduct` WRITE;
/*!40000 ALTER TABLE `msproduct` DISABLE KEYS */;
INSERT INTO `msproduct` VALUES ('31100001','UREA','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100002','KCL MOP','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100003','KIESERITE','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100004','ROCK PHOSPATE','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100005','HGF BORATE','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100006','NPK 12.12.17.2','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100007','SP 36/TSP','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100008','NPK 15.15.6.4','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100009','CUSO4','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100010','ZNSO4','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100011','PAMAFERT PN','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100012','FE SO4','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100013','GUANO','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100014','HI-KAY','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100015','BAYFOLAN','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100016','OSTINDO','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100017','MGSO4','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100018','PAMAFERT MN','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100019','MITRA FLORA','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100020','MEISTER','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100021','NPK TABLET MN (15)','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100022','NPK TABLET PN (12)','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100023','NPK TABLET 18.12.10.3.2','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100024','AGROBLEN 17.8.9+3 MGO','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100026','NPK TABLET PAMAFERT','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100027','BIOZYM','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100028','MYCO GOLD','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100029','DOLOMIT','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100030','ATONIK @ 500 ML','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100031','ROOTONE F','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100032','HUMEGA CRUMBLES','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100033','ZINCOPER','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100034','FRUIT STIMULANT','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100035','ZWAVELZUUR AMMONIA (ZA)','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100036','SUPER DOLOMITE','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100037','RP NOS','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100038','MESTER','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100039','PERTIBOR','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('31100040','PALMO 14:8:21:4:2:2:1','Aktif','ksi001','2010-11-26 15:14:54',0,0,0,'0'),('31100041','PALMO 16:10:18:6:2:2:2','Aktif','ksi001','2010-11-26 15:14:54',0,0,0,'0'),('31100042','AGROBLEN','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('35100001','SOLAR','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('35100002','BENSIN','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('35100003','MINYAK TANAH/GAS','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('35100004','KAYU BAKAR','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('37800001','BERAS @ 25 KG','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('37800003','BERAS JAGUNG','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('37800086','MINYAK GORENG @ 5 LTR','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('37800105','GULA PASIR','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('40000001','Crude Palm Oil (CPO)','Aktif','usr002','2010-11-26 15:14:54',0,0,0,'0'),('40000002','Palm Kernel (PK)','Aktif','usr002','2010-11-26 15:14:54',1,0,0,'0'),('40000003','Fresh Fruit Bunch (TBS)','Aktif','usr002','2010-11-26 15:14:54',1,0,1,'0'),('40000004','Janjang Kosong','Aktif','usr001','2010-11-26 15:14:54',0,1,0,'1'),('40000005','CANGKANG SAWIT','Aktif','ksi001','2010-11-26 15:14:54',0,0,0,'0');
/*!40000 ALTER TABLE `msproduct` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mssipb`
--

DROP TABLE IF EXISTS `mssipb`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mssipb` (
  `CTRNO` char(30) NOT NULL,
  `SIPBNO` char(30) NOT NULL,
  `SIPBDATE` date NOT NULL,
  `PRODUCTCODE` char(8) default NULL,
  `TRPCODE` char(10) NOT NULL,
  `SIPBQTY` int(11) NOT NULL,
  `DESCRIPTION` char(50) default NULL,
  `SIPBSTATUS` char(15) default NULL,
  `USERID` char(15) default NULL,
  `CREATEDATE` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`CTRNO`,`SIPBNO`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mssipb`
--

LOCK TABLES `mssipb` WRITE;
/*!40000 ALTER TABLE `mssipb` DISABLE KEYS */;
/*!40000 ALTER TABLE `mssipb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mssipboil`
--

DROP TABLE IF EXISTS `mssipboil`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mssipboil` (
  `CTRNO` char(50) NOT NULL,
  `SIPBNO` char(50) NOT NULL,
  `SIPBDATE` date NOT NULL,
  `PRODUCTCODE` char(8) default NULL,
  `TRPCODE` char(6) NOT NULL,
  `SIPBQTY` int(11) NOT NULL,
  `DESCRIPTION` char(50) default NULL,
  `SIPBSTATUS` char(15) default NULL,
  `USERID` char(15) default NULL,
  `CREATEDATE` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`CTRNO`,`SIPBNO`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mssipboil`
--

LOCK TABLES `mssipboil` WRITE;
/*!40000 ALTER TABLE `mssipboil` DISABLE KEYS */;
INSERT INTO `mssipboil` VALUES ('001/SC-DMA/II/09','001/DMA/SIPB/II/09','2009-03-18','40000001','500070',57830,'','Tidak Aktif','ksi001','2010-01-14 09:40:16'),('026/SC-DMA/VII/09','005/HD-CD/SIPB/','2009-07-11','40000000','901136',-6750,'sesuai email tgl 13/7/09','Aktif','ksi001','2009-10-17 02:50:55'),('028/SC-DMA/VII/09','006/HO-CD/SIPB/CK/','2009-07-27','40000000','901137',4330,'Sesuai email tgl 27 Juli 2009','Aktif','ksi001','2009-07-29 12:34:47'),('043/SC-GSM/IX/09','011/HO-CD/SIPB/','2009-09-28','40000000','901139',972600,'sesuia email tgl 01/10/2009','Aktif','adm003','2009-10-21 01:52:05'),('003/SC-WNA/XI/2009','266/HO-CD/SIPB/XI/2009','2009-11-06','40000001','500070',7410,'sesuai email dept commercial tgl 18/11/09','Aktif','mgr001','2009-12-03 06:39:43'),('053/SC-GSM/XII','016/HO-CD/','2009-12-03','40000000','901140',41520,'Sesuai email commercial tgl.03/12/2009','Aktif','ksi001','2010-01-24 05:15:28'),('004/SC-WNA/XI','309/HO-CD/SIPB/XII','2009-11-19','40000001','500070',0,'Sesuai email Commercial Tgl.15/12/2009','Aktif','ksi001','2010-01-15 10:01:41'),('054/SC-GSM/XII/09','017/HO-CD/SIPB/XII','2009-12-11','40000000','100028',0,'Sesuai email Commercial tgl 21/12/2009','Aktif','ksi001','2010-01-08 14:17:17'),('056/SC-GSM/XII/2009','019/HO/SIPB/XII/09','2009-12-28','40000000','100648',0,'Sesuai email dept comercial','Aktif','adm003','2010-01-13 11:06:18'),('003/SC-GSM/I/2010','001/CD/SIPB/CK/I/10','2010-01-08','40000000','100028',0,'sesuai email dept comercial tgl 8/1/10','Aktif','mgr001','2010-01-08 23:22:44'),('002/SC-GSM/I/10','002/HO-CD/SIPB/CK/I/','2010-01-11','40000000','100100',6,'Sesuai email Commercial tgl.12/01/2010','Aktif','ksi001','2010-01-23 08:55:20');
/*!40000 ALTER TABLE `mssipboil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mssuratpengantar`
--

DROP TABLE IF EXISTS `mssuratpengantar`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mssuratpengantar` (
  `TICKETNO` int(8) NOT NULL,
  `SPNO` char(20) NOT NULL,
  `SPTRXDATE` datetime NOT NULL,
  `PRODUCTCODE` char(8) default NULL,
  `BRUTO` int(11) default NULL,
  `TARRA` int(11) default NULL,
  `FFA` decimal(10,0) default NULL,
  `DIRT` decimal(10,0) default NULL,
  `MOIST` decimal(10,0) default NULL,
  `DOBI` decimal(10,0) default NULL,
  `YELLOW` decimal(10,0) default NULL,
  `RED` decimal(10,0) default NULL,
  `SHLNU` decimal(10,0) default NULL,
  `SHLNP` decimal(10,0) default NULL,
  `SHELL` decimal(10,0) default NULL,
  `BROCK` decimal(10,0) default NULL,
  `DATEOUT` date default NULL,
  `TIMEOUT` time default NULL,
  `NAMAKRANI` char(30) default NULL,
  `SEAL1` char(15) default NULL,
  `SEAL2` char(15) default NULL,
  `TEMP` decimal(10,0) default NULL,
  `USERID` char(15) default NULL,
  `CREATEDATE` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`TICKETNO`,`SPNO`,`SPTRXDATE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mssuratpengantar`
--

LOCK TABLES `mssuratpengantar` WRITE;
/*!40000 ALTER TABLE `mssuratpengantar` DISABLE KEYS */;
/*!40000 ALTER TABLE `mssuratpengantar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mssystem`
--

DROP TABLE IF EXISTS `mssystem`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mssystem` (
  `MILLCODE` char(4) NOT NULL,
  `MILLNAME` char(30) default NULL,
  `COMPCODE` char(30) default NULL,
  `COMPNAME` char(30) default NULL,
  `MNGRNAME` char(30) default NULL,
  `KTUNAME` char(30) default NULL,
  `KRANINAME` char(30) default NULL,
  `TIMEVEH` int(11) default NULL,
  `TIMESTOP` int(11) default NULL,
  `USERID` char(15) NOT NULL,
  `CREATEDATE` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `IDWB` char(2) default NULL,
  PRIMARY KEY  (`MILLCODE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mssystem`
--

LOCK TABLES `mssystem` WRITE;
/*!40000 ALTER TABLE `mssystem` DISABLE KEYS */;
INSERT INTO `mssystem` VALUES ('SOGM','SEI OGAN MILL 2','PMO','PERKEBUNAN MINANGA OGAN, PT','Jonathan','Kasie','Krani',10,NULL,'admin','2010-11-26 04:39:41','A');
/*!40000 ALTER TABLE `mssystem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msterimacpo`
--

DROP TABLE IF EXISTS `msterimacpo`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msterimacpo` (
  `DOCNO` char(15) NOT NULL,
  `DOCDATE` date NOT NULL,
  `DOCQTY` int(11) default NULL,
  `DESCRIPTION` char(50) default NULL,
  `DOCSTATUS` char(15) default NULL,
  `USERID` char(15) default NULL,
  `CREATEDATE` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`DOCNO`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='table master u/ penerimaan CPO';
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msterimacpo`
--

LOCK TABLES `msterimacpo` WRITE;
/*!40000 ALTER TABLE `msterimacpo` DISABLE KEYS */;
/*!40000 ALTER TABLE `msterimacpo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mstrxlain`
--

DROP TABLE IF EXISTS `mstrxlain`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mstrxlain` (
  `TICKETNO` char(8) NOT NULL,
  `DATEIN` datetime NOT NULL,
  `DATETRXIN` datetime NOT NULL,
  `DATEOUT` datetime NOT NULL,
  `DATETRXOUT` datetime NOT NULL,
  `TIMEIN` time default NULL,
  `TIMEOUT` time default NULL,
  `WEI1ST` int(11) default NULL,
  `WEI2ND` int(11) default NULL,
  `TRPCODE` char(6) NOT NULL,
  `UNITCODE` char(4) default NULL,
  `DIVCODE` char(4) default NULL,
  `BULAN` date default NULL,
  `TAHUN` date default NULL,
  `VEHNO` char(12) NOT NULL,
  `DRIVER` char(30) default NULL,
  `UNITGOL` char(1) default NULL,
  `TRXTYPE` char(1) default NULL,
  `WBCODE1ST` char(1) default NULL,
  `WBCODE2ND` char(1) default NULL,
  `FLAG` char(2) default NULL,
  `P1FLAG` int(11) default NULL,
  `P2FLAG` int(11) default NULL,
  `USERID1ST` char(10) default NULL,
  `USERID2ND` char(10) default NULL,
  `NETTO` int(11) default NULL,
  `SNOIN` char(1) default NULL,
  `SNOOUT` char(1) default NULL,
  PRIMARY KEY  (`TICKETNO`,`DATEIN`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mstrxlain`
--

LOCK TABLES `mstrxlain` WRITE;
/*!40000 ALTER TABLE `mstrxlain` DISABLE KEYS */;
/*!40000 ALTER TABLE `mstrxlain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mstrxoil`
--

DROP TABLE IF EXISTS `mstrxoil`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mstrxoil` (
  `id` int(11) NOT NULL auto_increment,
  `TICKETNO` int(6) unsigned zerofill NOT NULL,
  `OUTIN` tinyint(1) NOT NULL COMMENT '1=masuk;0=keluar',
  `SPBNO` char(20) default NULL,
  `CTRNO` char(50) default NULL,
  `SIPBNO` char(50) default NULL,
  `SIPBQTY` int(11) default NULL,
  `SPNO` char(50) default NULL,
  `PRODUCTCODE` char(8) default NULL,
  `DATEIN` datetime default NULL,
  `DATEOUT` datetime default NULL,
  `WEI1ST` int(11) default NULL,
  `WEI2ND` int(11) default NULL,
  `MILLCODE` char(4) default NULL,
  `SLOC` char(4) default NULL,
  `VEHNOCODE` char(12) default NULL,
  `TRPCODE` char(8) default NULL,
  `UNITCODE` char(4) default NULL,
  `DIVCODE` char(4) default NULL,
  `TAHUNTANAM` char(30) default NULL,
  `JMLHJJG` int(11) default NULL,
  `BRONDOLAN` int(11) default NULL,
  `DRIVER` char(30) default NULL,
  `NETTO` int(11) default NULL,
  `BERATKIRIM` char(6) default NULL,
  `USERID` char(20) default NULL,
  `NODOTRP` char(15) default NULL,
  `SATUANBERAT` char(2) default 'KG',
  `PENERIMA` char(35) default NULL,
  `APPROVTARRA` char(15) default NULL,
  `GI` varchar(45) default '0' COMMENT 'GOODS ISSUE',
  `GR` varchar(45) default '0' COMMENT 'GOODS RECEIVE',
  `TF` varchar(45) default '0' COMMENT 'TRANSFER BULKING',
  `TRANSACTIONTYPE` tinyint(1) default '0' COMMENT '1=penjualan;2=TF POSTING (BULKING)',
  `JENISSPB` char(10) default NULL COMMENT 'SPB INTERNAL:PLASMA/INTI',
  `SPNOBULKING` char(30) default NULL,
  `SLOCBULKING` char(4) default NULL,
  `DOCNO` char(50) default NULL COMMENT 'DOC U/ BULKING',
  `DOCQTY` int(11) default NULL,
  `BPS` varchar(45) default '0',
  `PENGIRIM` char(50) default NULL,
  `KETERANGAN` char(50) default NULL,
  `TAHUNTANAM2` char(4) default NULL,
  `JMLHJJG2` int(11) default NULL,
  `BRONDOLAN2` int(11) default NULL,
  `TAHUNTANAM3` char(4) default NULL,
  `JMLHJJG3` int(11) default NULL,
  `BRONDOLAN3` int(11) default NULL,
  `IDWB` char(2) default NULL,
  `TICKETNO2` char(7) default NULL,
  `NOSEGEL` char(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mstrxoil`
--

LOCK TABLES `mstrxoil` WRITE;
/*!40000 ALTER TABLE `mstrxoil` DISABLE KEYS */;
/*!40000 ALTER TABLE `mstrxoil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mstrxtbs`
--

DROP TABLE IF EXISTS `mstrxtbs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mstrxtbs` (
  `IDWB` char(2) NOT NULL,
  `id` int(11) NOT NULL auto_increment,
  `TICKETNO` int(6) unsigned zerofill NOT NULL,
  `OUTIN` tinyint(1) NOT NULL COMMENT '1=masuk;0=keluar',
  `SPBNO` char(35) default NULL,
  `CTRNO` char(30) default NULL,
  `SIPBNO` char(30) default NULL,
  `SIPBQTY` int(11) default NULL,
  `SPNO` char(30) default NULL,
  `PRODUCTCODE` char(8) default NULL,
  `DATEIN` datetime default NULL,
  `DATEOUT` datetime default '0000-00-00 00:00:00',
  `WEI1ST` int(11) default NULL,
  `WEI2ND` int(11) default '0',
  `MILLCODE` char(4) default NULL,
  `SLOC` char(4) default NULL,
  `VEHNOCODE` char(12) default NULL,
  `TRPCODE` char(10) default NULL,
  `UNITCODE` char(10) default NULL,
  `DIVCODE` char(10) default NULL,
  `TAHUNTANAM` char(30) default NULL,
  `JMLHJJG` int(11) default '0',
  `BRONDOLAN` int(11) default '0',
  `DRIVER` char(30) default NULL,
  `NETTO` int(11) default '0',
  `BERATKIRIM` char(6) default NULL,
  `USERID` char(20) default NULL,
  `NODOTRP` char(15) default NULL,
  `SATUANBERAT` char(2) default 'KG',
  `PENERIMA` char(35) default NULL,
  `APPROVTARRA` char(15) default NULL,
  `GI` varchar(45) default '0' COMMENT 'GOODS ISSUE',
  `GR` varchar(45) default '0' COMMENT 'GOODS RECEIVE',
  `TF` varchar(45) default '0' COMMENT 'TRANSFER BULKING',
  `TRANSACTIONTYPE` tinyint(1) default '0' COMMENT '1=penjualan;2=TF POSTING (BULKING)',
  `JENISSPB` char(10) default NULL COMMENT 'SPB INTERNAL:PLASMA/INTI',
  `SPNOBULKING` char(30) default NULL,
  `SLOCBULKING` char(4) default NULL,
  `DOCNO` char(50) default NULL COMMENT 'DOC U/ BULKING',
  `DOCQTY` int(11) default NULL,
  `BPS` varchar(45) default '0',
  `PENGIRIM` char(50) default NULL,
  `KETERANGAN` char(50) default NULL,
  `TAHUNTANAM2` char(4) default NULL,
  `JMLHJJG2` int(11) default '0',
  `BRONDOLAN2` int(11) default '0',
  `TAHUNTANAM3` char(4) default NULL,
  `JMLHJJG3` int(11) default '0',
  `BRONDOLAN3` int(11) default '0',
  `TICKETNO2` char(22) default NULL,
  `NOSEGEL` char(30) default NULL,
  PRIMARY KEY  (`IDWB`,`id`),
  UNIQUE KEY `unique` (`TICKETNO`,`OUTIN`,`TICKETNO2`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mstrxtbs`
--

LOCK TABLES `mstrxtbs` WRITE;
/*!40000 ALTER TABLE `mstrxtbs` DISABLE KEYS */;
/*!40000 ALTER TABLE `mstrxtbs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mstrxtbslogdel`
--

DROP TABLE IF EXISTS `mstrxtbslogdel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mstrxtbslogdel` (
  `IDWB` char(2) NOT NULL,
  `id` int(11) NOT NULL auto_increment,
  `TICKETNO` int(6) unsigned zerofill NOT NULL,
  `OUTIN` tinyint(1) NOT NULL COMMENT '1=masuk;0=keluar',
  `SPBNO` char(20) default NULL,
  `CTRNO` char(30) default NULL,
  `SIPBNO` char(30) default NULL,
  `SIPBQTY` int(11) default NULL,
  `SPNO` char(30) default NULL,
  `PRODUCTCODE` char(8) default NULL,
  `DATEIN` datetime default NULL,
  `DATEOUT` datetime default NULL,
  `WEI1ST` int(11) default NULL,
  `WEI2ND` int(11) default NULL,
  `MILLCODE` char(4) default NULL,
  `SLOC` char(4) default NULL,
  `VEHNOCODE` char(12) default NULL,
  `TRPCODE` char(8) default NULL,
  `UNITCODE` char(4) default NULL,
  `DIVCODE` char(4) default NULL,
  `TAHUNTANAM` char(30) default NULL,
  `JMLHJJG` int(11) default NULL,
  `BRONDOLAN` int(11) default NULL,
  `DRIVER` char(30) default NULL,
  `NETTO` int(11) default NULL,
  `BERATKIRIM` char(6) default NULL,
  `USERID` char(20) default NULL,
  `NODOTRP` char(15) default NULL,
  `SATUANBERAT` char(2) default 'KG',
  `PENERIMA` char(35) default NULL,
  `APPROVTARRA` char(15) default NULL,
  `GI` varchar(45) default '0' COMMENT 'GOODS ISSUE',
  `GR` varchar(45) default '0' COMMENT 'GOODS RECEIVE',
  `TF` varchar(45) default '0' COMMENT 'TRANSFER BULKING',
  `TRANSACTIONTYPE` tinyint(1) default '0' COMMENT '1=penjualan;2=TF POSTING (BULKING)',
  `JENISSPB` char(10) default NULL COMMENT 'SPB INTERNAL:PLASMA/INTI',
  `SPNOBULKING` char(30) default NULL,
  `SLOCBULKING` char(4) default NULL,
  `DOCNO` char(30) default NULL COMMENT 'DOC U/ BULKING',
  `DOCQTY` int(11) default NULL,
  `BPS` varchar(45) default '0',
  `PENGIRIM` char(50) default NULL,
  `KETERANGAN` char(50) default NULL,
  `TAHUNTANAM2` char(4) default NULL,
  `JMLHJJG2` int(11) default NULL,
  `BRONDOLAN2` int(11) default NULL,
  `TAHUNTANAM3` char(4) default NULL,
  `JMLHJJG3` int(11) default NULL,
  `BRONDOLAN3` int(11) default NULL,
  `TICKETNO2` char(7) default NULL,
  `NOSEGEL` char(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mstrxtbslogdel`
--

LOCK TABLES `mstrxtbslogdel` WRITE;
/*!40000 ALTER TABLE `mstrxtbslogdel` DISABLE KEYS */;
/*!40000 ALTER TABLE `mstrxtbslogdel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msunit`
--

DROP TABLE IF EXISTS `msunit`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msunit` (
  `UNITCODE` char(4) NOT NULL,
  `UNITNAME` char(50) default NULL,
  `WILCODE` char(4) NOT NULL,
  `COMPCODE` char(4) NOT NULL,
  `UNITSTATUS` char(15) default NULL,
  `USERID` char(10) default NULL,
  `CREATEDATE` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`UNITCODE`,`WILCODE`,`COMPCODE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msunit`
--

LOCK TABLES `msunit` WRITE;
/*!40000 ALTER TABLE `msunit` DISABLE KEYS */;
INSERT INTO `msunit` VALUES ('SENE','SEI ENAI ESTATE','SSRO','PMO',NULL,'admin','2010-11-23 06:20:08'),('SKSE','SEI KISAM ESTATE','SSRO','PMO',NULL,'admin','2010-11-23 06:21:07'),('SKUE','SEI KUANG ESTATE','SSRO','PMO',NULL,'admin','2010-11-23 06:22:21'),('SOGE','SEI OGAN ESTATE','SSRO','PMO',NULL,'admin','2010-11-23 06:22:59'),('WKNE','WAY KANAN ESTATE','SSRO','KMT',NULL,'admin','2010-11-23 06:23:51'),('MRKE','MERAKSA ESTATE','SSRO','GMJ',NULL,'admin','2010-11-23 06:24:24');
/*!40000 ALTER TABLE `msunit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msuserlevel`
--

DROP TABLE IF EXISTS `msuserlevel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msuserlevel` (
  `LEVELID` smallint(6) NOT NULL,
  `LEVELNAME` char(30) default NULL,
  PRIMARY KEY  (`LEVELID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msuserlevel`
--

LOCK TABLES `msuserlevel` WRITE;
/*!40000 ALTER TABLE `msuserlevel` DISABLE KEYS */;
INSERT INTO `msuserlevel` VALUES (1,'administrator'),(2,'manager'),(3,'kasie'),(4,'operator'),(5,'others');
/*!40000 ALTER TABLE `msuserlevel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msvehicle`
--

DROP TABLE IF EXISTS `msvehicle`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msvehicle` (
  `VEHNOCODE` char(15) NOT NULL,
  `TRPCODE` char(10) default NULL,
  `VEHTYPECODE` char(2) default NULL,
  `VEHTARMIN` int(11) default NULL,
  `VEHTARMAX` int(11) default NULL,
  `VEHDRIVER` char(30) default NULL,
  `VEHDRVSIM` char(25) default NULL,
  `VEHSTATUS` char(15) default NULL,
  `USERID` char(15) default NULL,
  `CREATEDATE` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `FLAG` char(1) default 'T',
  PRIMARY KEY  (`VEHNOCODE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msvehicle`
--

LOCK TABLES `msvehicle` WRITE;
/*!40000 ALTER TABLE `msvehicle` DISABLE KEYS */;
INSERT INTO `msvehicle` VALUES ('3854','5000000001','03',5010,5110,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('A8362AC','5000000001','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9601OH','5000000001','03',3920,4020,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BARU1','5000000001','03',3770,3870,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4331DL','5000000001','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9097WA1','5000000001','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9151JB','5000000001','03',2900,3000,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4568LJ','5000000001','03',3700,3800,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4860FB1','5000000001','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4867FB1','5000000001','03',3360,3460,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4881MA','5000000001','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8068LJ','5000000001','03',3680,3780,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8118FS1','5000000001','03',3480,3580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8138UA','5000000001','03',3930,4030,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8144FM','5000000001','03',4000,4100,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8148MQ','5000000001','03',3840,3940,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8163V','5000000001','03',3040,3140,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8163V1','5000000001','03',3080,3180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8180F','5000000001','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8225LM','5000000001','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8271JA','5000000001','03',3790,3890,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8271UA','5000000001','03',3800,3900,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8291YA','5000000001','03',3710,3810,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8345LN','5000000001','03',3230,3330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8350UE','5000000001','03',3950,4050,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8369RP','5000000001','03',2750,2850,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8396AI','5000000001','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8410FM','5000000001','03',1800,1900,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8500F','5000000001','03',3780,3880,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8501FM','5000000001','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8503T','5000000001','03',3860,3960,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8505UA','5000000001','03',3980,4080,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8521MM','5000000001','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8667D1','5000000001','03',3810,3910,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8709F','5000000001','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8746MJ','5000000001','03',3930,4030,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8782RK','5000000001','03',2990,3090,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8850UA','5000000001','03',3920,4020,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8850UB','5000000001','03',3850,3950,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8895F','5000000001','03',3540,3640,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9202K1','5000000001','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9651F','5000000001','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D8322CW','5000000001','03',2940,3040,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D8708VC','5000000001','03',3120,3220,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D8780NC','5000000001','03',3090,3190,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('E9011V','5000000001','03',3680,3780,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('E9918B','5000000001','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('Z8627DB1','5000000001','03',3120,3220,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4261N','5000000002','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8694F','5000000007','03',2900,3000,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4889FB','5000000008','03',3780,3880,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9999FV.','5000000009','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('7003-II','5000000010','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B2048G','5000000010','03',1740,1840,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9104IF','5000000010','03',3870,3970,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9650AW','5000000010','03',3220,3320,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4331OL','5000000010','03',3440,3540,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9000MK','5000000010','03',3590,3690,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9047L','5000000010','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9079W','5000000010','03',3220,3320,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9098F','5000000010','03',3040,3140,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9104N','5000000010','03',3310,3410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9164TB','5000000010','03',2990,3090,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9208W','5000000010','03',3560,3660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9335BM','5000000010','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9770BH','5000000010','03',3840,3940,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9847AC','5000000010','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9925DP','5000000010','03',3460,3560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9930BK','5000000010','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9975DP','5000000010','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4156MF1','5000000010','03',3280,3380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4326MK.','5000000010','03',3290,3390,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4492KA','5000000010','03',3220,3320,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4496MK','5000000010','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4545ML','5000000010','03',3490,3590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4640MF','5000000010','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4657Y','5000000010','03',2970,3070,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4668MD','5000000010','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4726MG','5000000010','03',3040,3140,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4760MK','5000000010','03',3490,3590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4777FB','5000000010','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4816FB','5000000010','03',3490,3590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4856MH','5000000010','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4865MH','5000000010','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4971MD','5000000010','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8008Y.','5000000010','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8041YA','5000000010','03',3290,3390,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8068LS','5000000010','03',3710,3810,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8068LY','5000000010','03',3660,3760,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8106JA','5000000010','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8107V','5000000010','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8136F','5000000010','03',3060,3160,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8147D','5000000010','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8197FN','5000000010','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8197YA','5000000010','03',3660,3760,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8199F','5000000010','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8262RT','5000000010','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8290YA','5000000010','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8310YA','5000000010','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8366UA','5000000010','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8369RL','5000000010','03',2740,2840,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8398AI','5000000010','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8406F1','5000000010','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8462K','5000000010','03',3560,3660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8474F','5000000010','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8612F','5000000010','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8620F','5000000010','03',3230,3330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8624UB','5000000010','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8798MU','5000000010','03',3530,3630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8808F.','5000000010','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8818UB.','5000000010','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8894MI','5000000010','03',3830,3930,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8896MI','5000000010','03',3760,3860,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8898MI','5000000010','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8938MO','5000000010','03',3330,3430,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9047L','5000000010','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9231AL','5000000010','03',1280,1380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9846LF','5000000010','03',3060,3160,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9930LF','5000000010','03',3050,3150,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9999F','5000000010','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('E8231KB','5000000010','03',3760,3860,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('F8127FK','5000000010','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('Z4935HA','5000000010','03',3340,3440,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE47963AU','5000000011','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9358JT','5000000011','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4061ML','5000000011','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8911RV','5000000011','03',3310,3410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('2003-II','5000000012','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('3851-II','5000000012','03',2990,3090,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('7003II','5000000012','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B4294NB','5000000012','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9650VW','5000000012','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BD8087B','5000000012','03',3130,3230,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4294MD','5000000012','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4351NB','5000000012','03',3260,3360,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4566GC','5000000012','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4618AV','5000000012','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4755N','5000000012','03',3090,3190,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE8171W','5000000012','03',3050,3150,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9136W','5000000012','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9264WA','5000000012','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9294NB','5000000012','03',3130,3230,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9306GE','5000000012','03',3440,3540,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9632GF','5000000012','03',3730,3830,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9889JA','5000000012','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9999GD','5000000012','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4003MM','5000000012','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4237DA1','5000000012','03',2740,2840,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4361FD','5000000012','03',3110,3210,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4744FB','5000000012','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG48689FB','5000000012','03',3790,3890,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4868FB','5000000012','03',3740,3840,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8001FY','5000000012','03',4570,4670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8058MJ','5000000012','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8087B','5000000012','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8144MO','5000000012','03',3040,3140,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8168UA','5000000012','03',3540,3640,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8173YA','5000000012','03',3610,3710,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8216AI','5000000012','03',3210,3310,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8234F','5000000012','03',2940,3040,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8369RZ','5000000012','03',2730,2830,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8427YA','5000000012','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8476UB','5000000012','03',3990,4090,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8586UA','5000000012','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8594AJ','5000000012','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8595AC1','5000000012','03',3230,3330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8634UB','5000000012','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8642UB','5000000012','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8643UB','5000000012','03',3660,3760,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8644UB','5000000012','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8666D','5000000012','03',3310,3410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8685UA','5000000012','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8746F','5000000012','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8749UB','5000000012','03',3760,3860,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8750UB','5000000012','03',3590,3690,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8763AS','5000000012','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8798JB','5000000012','03',3530,3630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8959UA','5000000012','03',3670,3770,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8987AC','5000000012','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9011FL','5000000012','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9632C','5000000012','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9975DP','5000000012','03',3460,3560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9841QV','5000000013','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BD8664LE','5000000013','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4120BB','5000000013','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4143W','5000000013','03',3140,3240,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4240W','5000000013','03',3120,3220,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4276W','5000000013','03',3080,3180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4352AW','5000000013','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4365AK','5000000013','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4442W','5000000013','03',3210,3310,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4448W','5000000013','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4457VA','5000000013','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4763AU','5000000013','03',3230,3330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4886AU','5000000013','03',3360,3460,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9039WA','5000000013','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9058GE','5000000013','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9150WA','5000000013','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9151JE','5000000013','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9162W','5000000013','03',2890,2990,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9190W','5000000013','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9221JC','5000000013','03',3750,3850,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9254TA','5000000013','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9308NA','5000000013','03',840,940,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9359F','5000000013','03',3100,3200,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9529BM','5000000013','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9536BK','5000000013','03',2940,3040,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9715BO','5000000013','03',3550,3650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9743AN','5000000013','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9754MD','5000000013','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9754ND','5000000013','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9783AH','5000000013','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9847NC','5000000013','03',3330,3430,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9932BF','5000000013','03',3460,3560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4045EB','5000000013','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4151MM','5000000013','03',3810,3910,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4202FD','5000000013','03',3360,3460,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4235FB','5000000013','03',2980,3080,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4326MK','5000000013','03',3460,3560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4348AL','5000000013','03',3070,3170,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4359FB','5000000013','03',2890,2990,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4479FB','5000000013','03',3990,4090,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4565FB','5000000013','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4604FB','5000000013','03',3340,3440,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4629FB','5000000013','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4658AB','5000000013','03',2930,3030,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4724MH','5000000013','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4734FB','5000000013','03',3340,3440,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4876AN','5000000013','03',3120,3220,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4895MH','5000000013','03',3210,3310,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4910MF','5000000013','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8001Y','5000000013','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8008Y','5000000013','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8016D','5000000013','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8035LY','5000000013','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8045V','5000000013','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8047F','5000000013','03',3160,3260,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8075LY','5000000013','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8085MJ','5000000013','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8094YA','5000000013','03',3070,3170,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8100AI','5000000013','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8101Y','5000000013','03',3530,3630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8126RN','5000000013','03',2940,3040,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8149KA','5000000013','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8149YA','5000000013','03',3330,3430,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8201FM','5000000013','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8251LM','5000000013','03',3130,3230,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8263YA','5000000013','03',3290,3390,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8268F','5000000013','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8328AQ','5000000013','03',3280,3380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8375F','5000000013','03',3310,3410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8405V','5000000013','03',3160,3260,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8408RL','5000000013','03',3100,3200,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8424F','5000000013','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8426YA','5000000013','03',3290,3390,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8428RL','5000000013','03',3110,3210,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8436MJ','5000000013','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8442F','5000000013','03',3920,4020,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8465UB','5000000013','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8508YA','5000000013','03',3990,4090,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8540AJ','5000000013','03',3290,3390,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8597YB','5000000013','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8614RP','5000000013','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8644LM','5000000013','03',3980,4080,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8680MQ','5000000013','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8685AO','5000000013','03',3330,3430,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8705AJ','5000000013','03',3210,3310,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8729YA','5000000013','03',3580,3680,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8736F','5000000013','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8804Y','5000000013','03',3110,3210,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8808F','5000000013','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8811MY','5000000013','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8820YA','5000000013','03',3980,4080,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8888FN','5000000013','03',4020,4120,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8888FW','5000000013','03',3910,4010,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8910FM','5000000013','03',3140,3240,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8961MO','5000000013','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8972RR','5000000013','03',3140,3240,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9083Y','5000000013','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9254YA','5000000013','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9335BM','5000000013','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9770LF','5000000013','03',3000,3100,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9814LF','5000000013','03',3070,3170,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9901DF','5000000013','03',1260,1360,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9978LF','5000000013','03',3050,3150,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9999FV','5000000013','03',3790,3890,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BM9065LD','5000000013','03',1980,2080,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BM9065LP','5000000013','03',1980,2080,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BM9085LP','5000000013','03',1990,2090,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D8590YM','5000000013','03',3120,3220,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D8614VI','5000000013','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('H8938YH','5000000013','03',3360,3460,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('Z8395HA','5000000013','03',3340,3440,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('Z8784DC','5000000013','03',3080,3180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('Z8935HA','5000000013','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4708DB','5000000015','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8216AT','5000000015','03',3100,3200,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8394UB','5000000015','03',3280,3380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8680AI','5000000015','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8774MO','5000000015','03',3330,3430,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8798UB','5000000015','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8966AI','5000000015','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4658MH','5000000018','03',3440,3540,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8109UB','5000000018','03',3460,3560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8987AJ','5000000018','03',3330,3430,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9011LL','5000000018','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9650YW','5000000020','03',3310,3410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4303W','5000000020','03',3360,3460,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4548W','5000000020','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9040WA','5000000020','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9151W','5000000020','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9153W','5000000020','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9509TB','5000000020','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9533WA','5000000020','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9861BI','5000000020','03',3830,3930,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9938BK','5000000020','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4286MH','5000000020','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4474AR','5000000020','03',3000,3100,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4851MA','5000000020','03',3100,3200,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4998F','5000000020','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8339UC','5000000020','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8738UC','5000000020','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8739UC','5000000020','03',3690,3790,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8895MI','5000000020','03',3690,3790,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8897MI','5000000020','03',3870,3970,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('A8630KC','5000000021','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9001UDA','5000000021','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4329M','5000000021','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4573DL','5000000021','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4637BE','5000000021','03',3550,3650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9002WA','5000000021','03',3580,3680,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9038W','5000000021','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9046W','5000000021','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9047WA','5000000021','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9057AG','5000000021','03',3220,3320,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9058WA','5000000021','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9144GD','5000000021','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9171W','5000000021','03',3220,3320,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9271JB','5000000021','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9414BN','5000000021','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9461WA','5000000021','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9611NC','5000000021','03',3340,3440,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9816BR','5000000021','03',3490,3590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9912NC','5000000021','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4012Y','5000000021','03',3700,3800,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4315MF','5000000021','03',3230,3330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4367EB','5000000021','03',3060,3160,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4500ME','5000000021','03',3080,3180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4732DA','5000000021','03',2830,2930,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4760DB','5000000021','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4760FB','5000000021','03',2990,3090,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8026Y','5000000021','03',3010,3110,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8068Y','5000000021','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8114MO','5000000021','03',3020,3120,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8121LY','5000000021','03',3490,3590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8143AQ','5000000021','03',3010,3110,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8146AO','5000000021','03',3130,3230,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8146AQ','5000000021','03',3220,3320,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8171W','5000000021','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8247Y','5000000021','03',3130,3230,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8364YA','5000000021','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8404YA','5000000021','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8489AC','5000000021','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8544YA','5000000021','03',3480,3580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8624D','5000000021','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8911RP','5000000021','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9127Y','5000000021','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('E9268U','5000000021','03',3230,3330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('Z8935AH','5000000021','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('AG8684R','5000000022','03',3780,3880,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4320UA','5000000022','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4391AU','5000000022','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4489W','5000000022','03',3220,3320,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4550FB','5000000022','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE8522FM','5000000022','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9000WA','5000000022','03',3130,3230,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9060WA','5000000022','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9064AF','5000000022','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9069WA','5000000022','03',3340,3440,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9227WA','5000000022','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9277K','5000000022','03',3610,3710,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9849BQ','5000000022','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4492FB','5000000022','03',3210,3310,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4492WA','5000000022','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4673FB','5000000022','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8025LY','5000000022','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8374F','5000000022','03',3050,3150,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8406F','5000000022','03',3480,3580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8462K1','5000000022','03',3530,3630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8605FJ1','5000000022','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9251K','5000000022','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BH8349MB','5000000022','03',3330,3430,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D8293VE','5000000022','03',3440,3540,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D8293VG','5000000022','03',3530,3630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D9009VJ','5000000022','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D9009VJ1','5000000022','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D9743VA','5000000022','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('G1807BA','5000000022','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('Z8575DB','5000000022','03',3130,3230,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('04','5000000023','03',4990,5090,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('13461','5000000023','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('A4400','5000000023','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('A5013274','5000000023','03',3660,3760,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('A5013731','5000000023','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('A5014400','5000000023','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('AE920P','5000000023','03',1500,1600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('AG7491AC','5000000023','03',3490,3590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B4570EB','5000000023','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9094IF','5000000023','03',3890,3990,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9251F','5000000023','03',3160,3260,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9294QB','5000000023','03',3020,3120,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9294QB.','5000000023','03',2940,3040,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9294QB1','5000000023','03',2920,3020,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9466F','5000000023','03',2060,2160,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9467F','5000000023','03',2010,2110,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9530XR','5000000023','03',2080,2180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9601QH','5000000023','03',3910,4010,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BARU','5000000023','03',3760,3860,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BD8666AD','5000000023','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BD8666AO','5000000023','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE1410AK','5000000023','03',1810,1910,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4015AD','5000000023','03',2970,3070,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4206AH','5000000023','03',2990,3090,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4237DA','5000000023','03',2800,2900,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4261FB','5000000023','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4261N.','5000000023','03',3080,3180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4261N1','5000000023','03',3130,3230,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4261N2','5000000023','03',3040,3140,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4261N3','5000000023','03',3120,3220,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4261N9','5000000023','03',3120,3220,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4292NB','5000000023','03',3080,3180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4294NB','5000000023','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4294NB1','5000000023','03',3130,3230,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4503WA','5000000023','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4637AW','5000000023','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4645T','5000000023','03',3280,3380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4657V','5000000023','03',2980,3080,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4675B','5000000023','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4696BA','5000000023','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4731BD','5000000023','03',3850,3950,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4731BD1','5000000023','03',3880,3980,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4731DB','5000000023','03',3870,3970,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE8080YA','5000000023','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9010JB','5000000023','03',3120,3220,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9010JB1','5000000023','03',3120,3220,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9016WA','5000000023','03',3260,3360,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9095BQ','5000000023','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9206AK','5000000023','03',2890,2990,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9272WA','5000000023','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9274BQ','5000000023','03',3220,3320,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9274EO','5000000023','03',3210,3310,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9274EQ','5000000023','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9277W','5000000023','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9860BR','5000000023','03',3460,3560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG1046LM','5000000023','03',1560,1660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG1692LX','5000000023','03',1830,1930,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG1763AN','5000000023','03',1380,1480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG2756LF','5000000023','03',1470,1570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG40003FD','5000000023','03',3290,3390,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4003FD','5000000023','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4003MB','5000000023','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4015AD','5000000023','03',2950,3050,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4045FB','5000000023','03',3440,3540,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4067FB','5000000023','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4067FD','5000000023','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4078FB','5000000023','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4117MH','5000000023','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4130AP','5000000023','03',3110,3210,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4149FA','5000000023','03',2980,3080,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4156MF.','5000000023','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG417MH','5000000023','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4195LF','5000000023','03',3110,3210,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4197MA','5000000023','03',3060,3160,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4201AT','5000000023','03',3090,3190,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4206AH','5000000023','03',3050,3150,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4206AN','5000000023','03',2940,3040,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4217LF','5000000023','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4237BA','5000000023','03',2820,2920,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4237DA','5000000023','03',2820,2920,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4261N','5000000023','03',3040,3140,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4264MG','5000000023','03',3680,3780,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4264MG1','5000000023','03',3700,3800,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4292FB','5000000023','03',3110,3210,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4341AS','5000000023','03',3120,3220,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4341AS1','5000000023','03',3080,3180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4342M','5000000023','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4342MB','5000000023','03',3580,3680,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4342MB2','5000000023','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4346AL','5000000023','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4365FB1','5000000023','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4418DB','5000000023','03',2860,2960,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4446AP','5000000023','03',2910,3010,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4474AN','5000000023','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4568','5000000023','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4568EB','5000000023','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4568EB.','5000000023','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4568EB2','5000000023','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4570E','5000000023','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4570EB','5000000023','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4570EB.','5000000023','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4570EB1','5000000023','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4570FB.','5000000023','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4617FA','5000000023','03',2910,3010,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4627M','5000000023','03',3540,3640,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4627ML','5000000023','03',3540,3640,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4653FB','5000000023','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4653FB1','5000000023','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4655MK','5000000023','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4661DB','5000000023','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4708AM','5000000023','03',2930,3030,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4731DB','5000000023','03',3880,3980,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4777AN','5000000023','03',3360,3460,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4785LA','5000000023','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4794FB','5000000023','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4794FB1','5000000023','03',3290,3390,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4794MK','5000000023','03',3290,3390,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4794MK9','5000000023','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4814FB','5000000023','03',3210,3310,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4840FB','5000000023','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4867FB','5000000023','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4867FD','5000000023','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4922FA','5000000023','03',3000,3100,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4948FB','5000000023','03',2930,3030,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4957FB','5000000023','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4972MD','5000000023','03',2940,3040,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4991FB','5000000023','03',3090,3190,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4998FB','5000000023','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8005JA','5000000023','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8026MJ','5000000023','03',3690,3790,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8026MJ2','5000000023','03',3790,3890,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8043FM','5000000023','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8077D','5000000023','03',3030,3130,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8077UD','5000000023','03',3780,3880,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8080YA','5000000023','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8089LN','5000000023','03',3550,3650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8089LN1','5000000023','03',3550,3650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG81081','5000000023','03',3480,3580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8108F','5000000023','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8108F.','5000000023','03',3440,3540,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8108F1','5000000023','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8109UB.','5000000023','03',3460,3560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8109UB1','5000000023','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8118F','5000000023','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8138MA','5000000023','03',3860,3960,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8157RN','5000000023','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8190RS','5000000023','03',2880,2980,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8197FM','5000000023','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8199F1','5000000023','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8200H','5000000023','03',3040,3140,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8284FM','5000000023','03',3230,3330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8288FM','5000000023','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8322CW','5000000023','03',2930,3030,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8338HI','5000000023','03',3360,3460,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8348F','5000000023','03',3360,3460,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8348FT1','5000000023','03',3540,3640,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8362AC','5000000023','03',3560,3660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8369F','5000000023','03',3160,3260,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8369RR','5000000023','03',2750,2850,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8396AJ','5000000023','03',3080,3180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8397F','5000000023','03',3080,3180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8404F','5000000023','03',3550,3650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8404F1','5000000023','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8404UB','5000000023','03',3230,3330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8405F','5000000023','03',3480,3580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8405F9','5000000023','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8437FM','5000000023','03',3140,3240,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8464F','5000000023','03',2960,3060,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8474F1','5000000023','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8480FM','5000000023','03',3040,3140,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8497AJ','5000000023','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8497AJ1','5000000023','03',3230,3330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8500T','5000000023','03',3690,3790,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8502T','5000000023','03',3900,4000,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8506T','5000000023','03',3890,3990,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8507UA','5000000023','03',3840,3940,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8521F','5000000023','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8552LM','5000000023','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8585AC','5000000023','03',3140,3240,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8595AC','5000000023','03',3100,3200,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8605AJ','5000000023','03',3550,3650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8605AJ1','5000000023','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8649UD','5000000023','03',3760,3860,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8651F','5000000023','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8666AO','5000000023','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8667D','5000000023','03',3870,3970,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8672F','5000000023','03',3860,3960,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8672F.','5000000023','03',3860,3960,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8673F','5000000023','03',3900,4000,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8673F.','5000000023','03',3940,4040,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8673F1','5000000023','03',3920,4020,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8689','5000000023','03',3030,3130,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8689D','5000000023','03',3130,3230,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8689LN','5000000023','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8689LN.','5000000023','03',3980,4080,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8763AJ','5000000023','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8781MI','5000000023','03',3880,3980,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8781MI.','5000000023','03',3780,3880,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8781MI2','5000000023','03',3780,3880,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8789F','5000000023','03',3610,3710,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8790F','5000000023','03',3690,3790,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8791F','5000000023','03',3740,3840,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8791F1','5000000023','03',3680,3780,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8792F','5000000023','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8793F','5000000023','03',3550,3650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8793F.','5000000023','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8794F','5000000023','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8794MK1','5000000023','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8795F','5000000023','03',3550,3650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8795F1','5000000023','03',3580,3680,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8796F','5000000023','03',3550,3650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8796F.','5000000023','03',3610,3710,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8804F1','5000000023','03',3560,3660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8822F','5000000023','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8860MO','5000000023','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8860UC','5000000023','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8860UC1','5000000023','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8864UA','5000000023','03',3590,3690,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8878AC','5000000023','03',3280,3380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8881F','5000000023','03',3020,3120,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8916AC','5000000023','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8957J','5000000023','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8975F','5000000023','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8976F','5000000023','03',3260,3360,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8976F1','5000000023','03',3280,3380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8990D','5000000023','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8991D','5000000023','03',3740,3840,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9020K','5000000023','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9068Y','5000000023','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9086Y','5000000023','03',2950,3050,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9288E','5000000023','03',1810,1910,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9288F','5000000023','03',1760,1860,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9836LF','5000000023','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9849QB','5000000023','03',3140,3240,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9933MB','5000000023','03',960,1060,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9950LF','5000000023','03',3330,3430,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9950LF1','5000000023','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BGBARU','5000000023','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BH8349MD','5000000023','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BH8720AI','5000000023','03',3280,3380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BK8338HI','5000000023','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D8322GA','5000000023','03',2940,3040,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D9734VA','5000000023','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('D9999VK','5000000023','03',3460,3560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('E9908B','5000000023','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('H1583VW','5000000023','03',3090,3190,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('R1792AC','5000000023','03',3110,3210,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('TRB','5000000023','03',5050,5150,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('TRB01','5000000023','03',5030,5130,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('TRB02','5000000023','03',4950,5050,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('Z8208DD','5000000023','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('Z8627DB','5000000023','03',3110,3210,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BD8036AN','5000000004','03',7670,7770,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9289FC','5000000004','03',6710,6810,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9840FB','5000000004','03',6890,6990,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4072MM','5000000004','03',7370,7470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4658ML','5000000004','03',7100,7200,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8013AO','5000000004','03',6470,6570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8036AN','5000000004','03',7690,7790,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8352LM','5000000004','03',6890,6990,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8443RL','5000000004','03',6830,6930,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8492LN','5000000004','03',7090,7190,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8662MQ','5000000004','03',3780,3880,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8695LM','5000000004','03',6930,7030,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8717LM','5000000004','03',7230,7330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9210QU','5000000006','03',10740,10840,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9280NN','5000000006','03',11490,11590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9334DS','5000000006','03',11030,11130,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9337FW','5000000006','03',11480,11580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9368LL','5000000006','03',11790,11890,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9809AP','5000000006','03',11180,11280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9852WV','5000000006','03',7620,7720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9853WV','5000000006','03',7520,7620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9856WV','5000000006','03',7550,7650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4661AP','5000000006','03',6090,6190,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8011MJ','5000000006','03',7010,7110,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8117UC','5000000006','03',7150,7250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8176UB','5000000006','03',7600,7700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8190RP','5000000006','03',6810,6910,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8207UA','5000000006','03',10610,10710,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8208UA','5000000006','03',10340,10440,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8371MO','5000000006','03',7580,7680,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8372MO','5000000006','03',7340,7440,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8373MO','5000000006','03',7660,7760,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8374MO','5000000006','03',7530,7630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8375MO','5000000006','03',7770,7870,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8386AO','5000000006','03',11020,11120,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8461AO','5000000006','03',10850,10950,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8477MI','5000000006','03',7000,7100,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8511ME','5000000006','03',6870,6970,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8511MI','5000000006','03',6830,6930,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8513MI','5000000006','03',6740,6840,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8562UE','5000000006','03',10180,10280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8601AU','5000000006','03',11170,11270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8601MJ','5000000006','03',11180,11280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8602MJ','5000000006','03',11680,11780,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8690UA','5000000006','03',10810,10910,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8783UB','5000000006','03',10690,10790,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8821UB','5000000006','03',7040,7140,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8872UF','5000000006','03',10630,10730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8904UB','5000000006','03',10180,10280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8905UB','5000000006','03',10350,10450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8906UB','5000000006','03',11290,11390,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8917HU','5000000006','03',11500,11600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8979MI','5000000006','03',6870,6970,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9791C','5000000006','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BH8917HU','5000000006','03',11520,11620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4352MG','5000000010','03',7550,7650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4441MG','5000000010','03',7050,7150,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9854WV','5000000012','03',7500,7600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9855WV','5000000012','03',7570,7670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8524LN','5000000017','03',6940,7040,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8580LM','5000000017','03',6480,6580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9249PJ','5000000023','03',6150,6250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9534GB','5000000023','03',6280,6380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9743NQ','5000000023','03',6040,6140,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9743NQ1','5000000023','03',6000,6100,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9946OB','5000000023','03',6230,6330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9947OB','5000000023','03',6170,6270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9979SQ','5000000023','03',6080,6180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8069FM','5000000023','03',7070,7170,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8071FM','5000000023','03',6960,7060,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4284MG','5000000024','03',7800,7900,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4285MG','5000000024','03',7780,7880,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4353MG','5000000024','03',7380,7480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4354MG','5000000024','03',7440,7540,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4350AT','5000000011','03',2970,3070,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4350AT','5000000012','03',2980,3080,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4336AT','5000000014','03',2960,3060,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9349ZZ','5000000012','03',1380,1480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4444G','5000000012','03',3700,3800,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4862FB','5000000012','03',3740,3840,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8501Y','5000000012','03',3120,3220,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9056Y','5000000012','03',1260,1360,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9101Y','5000000012','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9241Y','5000000012','03',1300,1400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4714GA','5000000012','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9069AD','5000000012','03',2550,2650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8381MO','5000000005','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9024BW','5000000009','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4142MK','5000000009','03',3160,3260,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9239BO','5000000010','03',6520,6620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4256BD','5000000010','03',6410,6510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4264BD','5000000010','03',6440,6540,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4265BD','5000000010','03',6400,6500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4276BB','5000000010','03',7370,7470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4283BB','5000000010','03',7430,7530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4407BD','5000000010','03',7040,7140,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4621BC','5000000010','03',7250,7350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9095BT','5000000010','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9109BN','5000000010','03',7400,7500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9121AO','5000000010','03',7590,7690,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9127WA','5000000010','03',3590,3690,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9190BK','5000000010','03',7600,7700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9256BF','5000000010','03',7620,7720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9266DO','5000000010','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9275BF','5000000010','03',7080,7180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9346BN','5000000010','03',7710,7810,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9391BN','5000000010','03',7930,8030,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9425BO','5000000010','03',7710,7810,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9430BG','5000000010','03',7570,7670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4060MM','5000000010','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4574MG','5000000010','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8067V','5000000010','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8122AJ','5000000010','03',3210,3310,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8133FM','5000000010','03',3730,3830,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8215MO','5000000010','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8216MO','5000000010','03',3260,3360,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8314UB','5000000010','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8364MQ','5000000010','03',3660,3760,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8534AC','5000000010','03',3310,3410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8585RP','5000000010','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8688F','5000000010','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8778F','5000000010','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8952MQ','5000000010','03',3590,3690,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9117BN','5000000010','03',7530,7630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('T8426F','5000000010','03',3190,3290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4643BF','5000000011','03',3930,4030,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9115BN','5000000011','03',7450,7550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9117BN','5000000011','03',7530,7630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9532BF','5000000011','03',3820,3920,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4123MH','5000000011','03',3610,3710,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8429F','5000000011','03',3920,4020,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9332BN','5000000011','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9499MI','5000000012','03',6450,6550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4403BC','5000000012','03',7410,7510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4426BD','5000000012','03',7030,7130,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4431AT','5000000012','03',6780,6880,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9026BG','5000000012','03',7690,7790,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9067BR','5000000012','03',3280,3380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9109RN','5000000012','03',7490,7590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9125BN','5000000012','03',7560,7660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9127BN','5000000012','03',7670,7770,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9141BC','5000000012','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9194BK','5000000012','03',7580,7680,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9280BF','5000000012','03',7730,7830,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9284BF','5000000012','03',7560,7660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9305BO','5000000012','03',6370,6470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9361DP','5000000012','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9425BB','5000000012','03',7720,7820,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9457BN','5000000012','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9872BQ','5000000012','03',7380,7480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9873BQ','5000000012','03',7430,7530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4922MH','5000000012','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8112AJ','5000000012','03',3230,3330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8266FC','5000000012','03',7500,7600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8598MO','5000000012','03',3280,3380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8730MI','5000000012','03',3560,3660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8858MO','5000000012','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG9293BN','5000000012','03',3340,3440,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9274ED','5000000022','03',7090,7190,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8053LG','5000000009','03',3480,3580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8194UB','5000000009','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8308UA','5000000009','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8309UA','5000000009','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8313UA','5000000009','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8631UA','5000000009','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8896MJ','5000000009','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8897MJ','5000000009','03',3560,3660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9329FO','5000000010','03',3560,3660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9412LJ','5000000010','03',6310,6410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9451ML','5000000010','03',6230,6330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4120GA','5000000010','03',6010,6110,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE4898AT','5000000010','03',6520,6620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9078FA','5000000010','03',6230,6330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9144WA','5000000010','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9271WA','5000000010','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9465BM','5000000010','03',7480,7580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9532BT','5000000010','03',3820,3920,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9663FC','5000000010','03',6630,6730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9767BH','5000000010','03',7440,7540,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BE9983GE','5000000010','03',6570,6670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4643BF','5000000010','03',3870,3970,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4643FB','5000000010','03',3810,3910,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4837FB','5000000010','03',3160,3260,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4901FB','5000000010','03',2970,3070,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG4993FB','5000000010','03',7500,7600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8195UB','5000000010','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8314UA','5000000010','03',3580,3680,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8366FM','5000000010','03',3760,3860,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8627UA','5000000010','03',3610,3710,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8629UA','5000000010','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8670F','5000000010','03',3790,3890,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8788F','5000000010','03',6720,6820,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8895MJ','5000000010','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8989FA','5000000010','03',6810,6910,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('BG8997RT','5000000010','03',3700,3800,NULL,NULL,'Aktif','admin','2010-11-27 17:29:50','T'),('B9153FO','5000000011','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9912BG','5000000011','03',7610,7710,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4045MB','5000000011','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4391DB','5000000011','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4540MB','5000000011','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4627AH','5000000011','03',3310,3410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4639MK','5000000011','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4686ML','5000000011','03',3340,3440,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4985FB','5000000011','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8031AC','5000000011','03',3440,3540,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8044FM','5000000011','03',3730,3830,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8108UA','5000000011','03',3490,3590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8137W','5000000011','03',7490,7590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8208MC','5000000011','03',3790,3890,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8209MC','5000000011','03',3700,3800,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8297MQ','5000000011','03',3220,3320,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8310UA','5000000011','03',3920,4020,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8311UA','5000000011','03',3550,3650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8324F','5000000011','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8419AI','5000000011','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8427RN','5000000011','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8439RP','5000000011','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8459RP','5000000011','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8485AJ','5000000011','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8572MJ','5000000011','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8573MJ','5000000011','03',3550,3650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8624F','5000000011','03',3270,3370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8624MJ','5000000011','03',3720,3820,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8626UA','5000000011','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8670RP','5000000011','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8728UD','5000000011','03',3670,3770,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8767AO','5000000011','03',3540,3640,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8851MO','5000000011','03',3490,3590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8869AC','5000000011','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8878F','5000000011','03',6720,6820,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8892MJ','5000000011','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8987MI','5000000011','03',3770,3870,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8988MI','5000000011','03',3670,3770,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8998RT','5000000011','03',3680,3780,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('B9083JS','5000000012','03',6260,6360,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9272BN','5000000012','03',3320,3420,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9317BN','5000000012','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9816BN','5000000012','03',3440,3540,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4066FB','5000000012','03',3550,3650,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4110MM','5000000012','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4685ML','5000000012','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4690ML','5000000012','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4970ML','5000000012','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8064V','5000000012','03',3560,3660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8218FM','5000000012','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8239UE','5000000012','03',3690,3790,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8319RP','5000000012','03',3290,3390,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8653UC','5000000012','03',3210,3310,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8694D','5000000012','03',3670,3770,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG9918LF','5000000012','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4355AU','5000000017','03',6510,6610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4677AC','5000000017','03',6450,6550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4678LC','5000000017','03',6330,6430,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4685LC','5000000017','03',6350,6450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4732LB','5000000017','03',6410,6510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4976LA','5000000017','03',6430,6530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9014AU','5000000017','03',6190,6290,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9074BH','5000000017','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9199GD','5000000017','03',7080,7180,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9471FC','5000000017','03',6210,6310,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9605BS','5000000017','03',7300,7400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9651DO','5000000017','03',6460,6560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8216AC','5000000017','03',7130,7230,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8266RG','5000000017','03',3340,3440,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8312AJ','5000000016','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8364MO','5000000023','03',3770,3870,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4471AM4','5000000001','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4471AM5','5000000001','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4471AM6','5000000001','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4471AM1','5000000012','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8038FM','5000000021','03',3820,3920,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4471AM','5000000023','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4471AM.','5000000023','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4471AM2','5000000023','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4571MA','5000000023','03',3230,3330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4571MA.','5000000023','03',5800,5900,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4571MA2','5000000023','03',3230,3330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8026MJ.','5000000023','03',3830,3930,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8026MJ1','5000000023','03',3700,3800,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8037FM','5000000023','03',3710,3810,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8038FM1','5000000023','03',3820,3920,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8194UB1','5000000010','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('Z8126HJ','5000000001','03',9570,9670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BD8077PK','5000000009','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BD8635DG','5000000009','03',3360,3460,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4021BC','5000000009','03',3200,3300,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4574MG','5000000009','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4084MK','5000000009','03',3280,3380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4108MH','5000000009','03',3260,3360,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4114MI','5000000009','03',3260,3360,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4117MF','5000000009','03',3330,3430,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4451MK','5000000009','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4547MG','5000000009','03',0,50,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4622MK','5000000009','03',3530,3630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4623MK','5000000009','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4703ME','5000000009','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4711MF','5000000009','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4759MK','5000000009','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4759NK','5000000009','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4809MF','5000000009','03',3150,3250,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8000SP','5000000009','03',3490,3590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8010SP','5000000009','03',3530,3630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8014RT','5000000009','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8016UE','5000000009','03',4010,4110,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8018SP','5000000009','03',3460,3560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8019SP','5000000009','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8025FM','5000000009','03',3360,3460,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8028RT','5000000009','03',2970,3070,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8029RT','5000000009','03',3010,3110,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8055MU','5000000009','03',3890,3990,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8065MI','5000000009','03',3400,3500,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8080SP','5000000009','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8100SP','5000000009','03',3490,3590,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8123MO','5000000009','03',3010,3110,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8124MO','5000000009','03',3110,3210,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8130UA','5000000009','03',3510,3610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8132UE','5000000009','03',3880,3980,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8181AO','5000000009','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8194AJ','5000000009','03',3380,3480,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8196AJ','5000000009','03',3350,3450,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8196UA','5000000009','03',3680,3780,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8198UA','5000000009','03',3840,3940,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8209MI','5000000009','03',3740,3840,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8211MI','5000000009','03',3720,3820,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8221MO','5000000009','03',3530,3630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8233AJ','5000000009','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8234SP','5000000009','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8306UA','5000000009','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8312UA','5000000009','03',3530,3630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8314UA1','5000000009','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8326UE','5000000009','03',3930,4030,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8327UE','5000000009','03',4070,4170,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8338MO','5000000009','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8356MO','5000000009','03',3590,3690,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8361RT','5000000009','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8377LN','5000000009','03',3160,3260,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8388SP','5000000009','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8431UB','5000000009','03',3480,3580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8466AC','5000000009','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8467K','5000000009','03',3250,3350,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8469UF','5000000009','03',3480,3580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8470UF','5000000009','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8492UE','5000000009','03',3460,3560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8504UD','5000000009','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8505UD','5000000009','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8530MQ','5000000009','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8536MO','5000000009','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8536UE','5000000009','03',3960,4060,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8568AC','5000000009','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8576UE','5000000009','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8581AJ','5000000009','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8582AJ','5000000009','03',3260,3360,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8585SP','5000000009','03',0,50,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8586SP','5000000009','03',3670,3770,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8622MQ','5000000009','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8623UA','5000000009','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8630UA1','5000000009','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8667RP','5000000009','03',3310,3410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8706AO','5000000009','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8752MO','5000000009','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8772MJ','5000000009','03',3310,3410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8774SP','5000000009','03',3730,3830,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8775SP','5000000009','03',3690,3790,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8800SP','5000000009','03',3680,3780,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8805MU','5000000009','03',3890,3990,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8838SP','5000000009','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8867RP','5000000009','03',3310,3410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8873SP','5000000009','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8879AI','5000000009','03',3120,3220,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8882SP','5000000009','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8890MJ','5000000009','03',3590,3690,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8892MJ1','5000000009','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8899SP','5000000009','03',3690,3790,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8900SP','5000000009','03',3680,3780,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8919US','5000000009','03',3460,3560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8937UD','5000000009','03',4000,4100,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8958UD','5000000009','03',3950,4050,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8959UD','5000000009','03',3960,4060,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8965AI','5000000009','03',3310,3410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8965UC','5000000009','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8965UL','5000000009','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8966MJ','5000000009','03',3770,3870,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8966UC','5000000009','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8967RF','5000000009','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8967UC','5000000009','03',3290,3390,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8968RF','5000000009','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8969RF','5000000009','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8988RF','5000000009','03',3360,3460,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8998MQ','5000000009','03',3430,3530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4240B','5000000010','03',10030,10130,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9170WA','5000000010','03',9210,9310,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9310BG','5000000010','03',6640,6740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4061AS','5000000010','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4114ML','5000000010','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4156MF','5000000010','03',10280,10380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4232AT','5000000010','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4233AT','5000000010','03',3220,3320,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4260ML','5000000010','03',3300,3400,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4357AN','5000000010','03',3240,3340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4542MF','5000000010','03',3280,3380,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4741DK','5000000010','03',8230,8330,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4860FB','5000000010','03',0,50,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4961MF','5000000010','03',3520,3620,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8001SP','5000000010','03',3530,3630,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8107MQ','5000000010','03',3170,3270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8118FS','5000000010','03',10460,10560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8138UB','5000000010','03',3420,3520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8181PG','5000000010','03',3790,3890,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8193AC','5000000010','03',3580,3680,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8293AC','5000000010','03',3330,3430,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8307UA','5000000010','03',3560,3660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8347UE','5000000010','03',3560,3660,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8521FM','5000000010','03',11270,11370,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8522FM','5000000010','03',11460,11560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8562AC','5000000010','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8570AC','5000000010','03',3360,3460,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8584SP','5000000010','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8604AO','5000000010','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8624UA','5000000010','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8630UA','5000000010','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8661MQ','5000000010','03',3450,3550,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8668MQ','5000000010','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8858LN','5000000010','03',3470,3570,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8883SP','5000000010','03',3610,3710,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8893MJ','5000000010','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8964AI','5000000010','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8625UA','5000000011','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8628UA','5000000011','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8894MJ','5000000011','03',3600,3700,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8901MJ','5000000011','03',3590,3690,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8902MJ','5000000011','03',3610,3710,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9102BK','5000000012','03',9580,9680,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8680AC','5000000012','03',3160,3260,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG878AC','5000000012','03',9810,9910,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8860MQ','5000000012','03',11500,11600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4670AS','','03',3330,3430,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4954ML','','03',3480,3580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BD8438AO','5000000021','03',10570,10670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8438AO','5000000021','03',10500,10600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4320AU1','5000000022','03',0,50,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9097WA','5000000022','03',9420,9520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4320AU','5000000023','03',9240,9340,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4675B1','5000000023','03',10900,11000,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4731B1','5000000023','03',3900,4000,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4741DK','5000000023','03',9260,9360,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4156FM','5000000023','03',10100,10200,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4294NB','5000000023','03',7290,7390,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4365FB','5000000023','03',8170,8270,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4794MK1','5000000023','03',10260,10360,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4891FB','5000000023','03',8050,8150,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4891FB1','5000000023','03',8100,8200,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4908FB','5000000023','03',10830,10930,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8431F','5000000023','03',1800,1900,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8957F','5000000023','03',8430,8530,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG9202K','5000000023','03',0,50,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9170WA1','5000000001','03',3140,3240,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4148FA','5000000001','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8348FT','5000000001','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8673F2','5000000001','03',3940,4040,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8860MQ1','5000000001','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8968F','5000000001','03',3840,3940,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('B9946SD','5000000010','03',6420,6520,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4316BC','5000000010','03',3310,3410,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE4406AN','5000000010','03',6460,6560,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9170WAF1','5000000010','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9204BF','5000000010','03',3800,3900,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8804F','5000000010','03',3540,3640,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8989V','5000000010','03',3630,3730,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BE9015FC','5000000012','03',6510,6610,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8424B','5000000012','03',3440,3540,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('8038FM','5000000023','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('AG4791RC','5000000023','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('AG4891RC','5000000023','03',3670,3770,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('AG7491RC','5000000023','03',3690,3790,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4148FA1','5000000023','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4264FG','5000000023','03',3650,3750,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4342MB.','5000000023','03',0,50,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4342MB1','5000000023','03',3660,3760,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4342MB9','5000000023','03',3640,3740,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4568EB1','5000000023','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4571M','5000000023','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4571MA1','5000000023','03',6590,6690,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4571MF1','5000000023','03',3180,3280,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4627ML1','5000000023','03',3620,3720,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4670FB','5000000023','03',3340,3440,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG4771AM','5000000023','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8037F','5000000023','03',3390,3490,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8037F2','5000000023','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8037FM0','5000000023','03',3370,3470,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8037FM1','5000000023','03',3710,3810,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8037FM2','5000000023','03',3410,3510,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8038F','5000000023','03',3500,3600,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8038FM9','5000000023','03',5960,6060,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8404UB1','5000000023','03',5890,5990,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8405F1','5000000023','03',3480,3580,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8672F1','5000000023','03',3970,4070,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8689LN1','5000000023','03',3940,4040,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8781MI1','5000000023','03',3850,3950,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8792F1','5000000023','03',3540,3640,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8793F1','5000000023','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8794F.','5000000023','03',3540,3640,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8794F1','5000000023','03',3580,3680,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('BG8796F1','5000000023','03',3570,3670,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T'),('E8231KB1','5000000023','03',3750,3850,NULL,NULL,'Aktif','admin','2010-11-27 17:29:51','T');
/*!40000 ALTER TABLE `msvehicle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msvehtype`
--

DROP TABLE IF EXISTS `msvehtype`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msvehtype` (
  `VEHTYPECODE` char(2) NOT NULL,
  `VEHTYPENAME` char(30) default NULL,
  `VEHTYPESTATUS` char(15) default NULL,
  `USERID` char(10) default NULL,
  `CREATEDATE` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`VEHTYPECODE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msvehtype`
--

LOCK TABLES `msvehtype` WRITE;
/*!40000 ALTER TABLE `msvehtype` DISABLE KEYS */;
INSERT INTO `msvehtype` VALUES ('01','TRUK','Aktif','usr001','2008-10-13 04:23:44'),('02',' TRUK TANGKI','Aktif','usr002','2009-01-01 17:59:49'),('03','DUMP TRUCK','Aktif','usr001','2008-10-15 23:23:57'),('04','LIGHT TRUCK','Aktif','usr002','2009-01-01 17:59:32'),('05','JONDER','Aktif','ksi001','2009-01-09 11:56:45'),('06','Mobil',NULL,NULL,'2010-09-04 17:11:48');
/*!40000 ALTER TABLE `msvehtype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msvendorbuyer`
--

DROP TABLE IF EXISTS `msvendorbuyer`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msvendorbuyer` (
  `BUYERCODE` char(10) NOT NULL,
  `BUYERNAME` char(35) default NULL,
  `BUYERADDR` char(35) default NULL,
  `BUYERCITY` char(35) default NULL,
  `BUYERSTATUS` char(15) default NULL,
  `USERID` char(10) default NULL,
  `CREATEDATE` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`BUYERCODE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msvendorbuyer`
--

LOCK TABLES `msvendorbuyer` WRITE;
/*!40000 ALTER TABLE `msvendorbuyer` DISABLE KEYS */;
INSERT INTO `msvendorbuyer` VALUES ('1000000001','AFD 1',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000002','AFD 2',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000003','AFD 3',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000004','AFD 4',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000005','AFD 5',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000006','AFD 6',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000007','AFD 7',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000008','AFD 8',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000009','AFD PSR',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000010','PT.A J P',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000011','A.T.S',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000012','BUDI INDRA DARMAWAN',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000013','BUNGA MAYANG',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000014','DIDIK.K',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000015','GALA LESTARINDO',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000016','GM.SELAMET',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000017','H.SOFYAN',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000018','H.MAZANI ILYAS',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000019','H.RUSWAN',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000020','INDRA LAYA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000021','IS.EFENDI HARAHAP',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000022','JAKARTA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000023','KUD MITRA WALL',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000024','KUD BATU KUNING',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000025','KUD I',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000026','KUD II',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000027','KUD TANI SEPAKAT',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000028','LAIN-LAIN',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000029','LOGISTIK',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000030','MUSI MAS',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000031','MA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000032','MUSTOPA KAMAL',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000033','PERTAMINA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000034','PT.TREE KREASI',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000035','PT.EMPAT PILAR SAUDARA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000036','PT.SENTANA A.P',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000037','PT. S A P',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000038','PT.GMJ',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000039','PT.HIKAY',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000040','PT.ALTAN TEGUH SEJATI',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000041','PT.AMAN JAYA PERDANA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000042','PT.BANDAR SAWIT UTAMA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000043','PT.GALATRA LESTARINDO',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000044','PT. KARYA PUTRAKREASAI NUSANTARA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000045','PT.LJU',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000046','PT.LJU',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000047','PT.OKI TANIA PRATAMA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000048','PT.ROLIMEX KIMIA NUSAMAS',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000049','PT.WANAKARYA MK',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000050','PT INDOKARYA INTERNUSA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000051','PT KARISMA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000052','PT.LASKAR',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000053','PTP.MA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000054','PT. R. 6. B',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000055','PTP.WANAKARYA.MK',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000056','PT.S W A',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000057','SIM',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000058','PT SINAR JAYA INTI MULIA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000059','PT.WILMAR NABATI INDONESIA',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28'),('1000000060','YON ARMED',NULL,NULL,'Aktif','admin','2010-11-26 17:01:28');
/*!40000 ALTER TABLE `msvendorbuyer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msvendortbs_eks`
--

DROP TABLE IF EXISTS `msvendortbs_eks`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msvendortbs_eks` (
  `VENDORCODE` char(6) NOT NULL,
  `VENDORNAME` char(35) default NULL,
  `VENDORADDR` char(35) default NULL,
  `VENDORCITY` char(35) default NULL,
  `VENDORSTATUS` char(15) default NULL,
  `USERID` char(10) default NULL,
  `CREATEDATE` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`VENDORCODE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msvendortbs_eks`
--

LOCK TABLES `msvendortbs_eks` WRITE;
/*!40000 ALTER TABLE `msvendortbs_eks` DISABLE KEYS */;
/*!40000 ALTER TABLE `msvendortbs_eks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msvendortrp`
--

DROP TABLE IF EXISTS `msvendortrp`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `msvendortrp` (
  `TRPCODE` char(10) NOT NULL,
  `TRPNAME` char(35) default NULL,
  `TRPADDR` char(40) default NULL,
  `TRPCITY` char(30) default NULL,
  `TRPSTATUS` char(10) default NULL,
  `USERID` char(10) default NULL,
  `CREATEDATE` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`TRPCODE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `msvendortrp`
--

LOCK TABLES `msvendortrp` WRITE;
/*!40000 ALTER TABLE `msvendortrp` DISABLE KEYS */;
INSERT INTO `msvendortrp` VALUES ('5000000001','MINANGA OGAN',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000002','AFD4',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000003','AFD5',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000004','ANGKUTAN MANDIRI JAYA',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000005','BUDI INDRA DARMAWAN',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000006','CV.BUDI USAHA',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000007','DIDIK.K',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000008','H.SOFYAN',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000009','PT.HIKAY',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000010','LAIN2',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000011','LUNG-LUNG',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000012','LAIN-LAIN',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000013','MUSTOPA KAMAL',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000014','PERTAMINA',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000015','PT R6B',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000016','PT. ALTAN TEGUH SEJATI',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000017','PT.AMAN JAYA PERDANA',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000018','PT.BANDAR SAWIT UTAMA',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000019','PT.KARYA PUTRAKREASI NUSANTARA',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000020','PT.OTP',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000021','PT.WANA KARYA',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000022','PT.KHARISMA',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000023','PTP.MINANGA OGAN',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55'),('5000000024','PT.SEIA MAIMA',NULL,NULL,'Aktif','admin','2010-11-26 16:23:55');
/*!40000 ALTER TABLE `msvendortrp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mswilayah`
--

DROP TABLE IF EXISTS `mswilayah`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mswilayah` (
  `WILCODE` char(4) NOT NULL,
  `WILNAME` char(40) default NULL,
  `WILLOCATION` char(20) default NULL,
  `WILSTATUS` char(15) default NULL,
  `USERID` char(10) default NULL,
  `CREATEDATE` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`WILCODE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `mswilayah`
--

LOCK TABLES `mswilayah` WRITE;
/*!40000 ALTER TABLE `mswilayah` DISABLE KEYS */;
INSERT INTO `mswilayah` VALUES ('SSRO','Sumatera Selatan Regional Office',NULL,NULL,NULL,'2010-11-23 06:08:56');
/*!40000 ALTER TABLE `mswilayah` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `number_table`
--

DROP TABLE IF EXISTS `number_table`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `number_table` (
  `number` int(11) NOT NULL,
  PRIMARY KEY  (`number`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `number_table`
--

LOCK TABLES `number_table` WRITE;
/*!40000 ALTER TABLE `number_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `number_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rfcerrorlog`
--

DROP TABLE IF EXISTS `rfcerrorlog`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `rfcerrorlog` (
  `id` int(11) NOT NULL auto_increment,
  `SOURCEID` int(11) default NULL,
  `LOG` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Error Log';
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `rfcerrorlog`
--

LOCK TABLES `rfcerrorlog` WRITE;
/*!40000 ALTER TABLE `rfcerrorlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `rfcerrorlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user` (
  `uname` varchar(45) NOT NULL default '',
  `status` varchar(45) NOT NULL default '0',
  `password` varchar(45) default NULL,
  `access_level` int(10) unsigned default '0',
  `userid` int(7) unsigned zerofill NOT NULL default '0000000',
  `lastuser` varchar(45) NOT NULL default 'System',
  `lastip` varchar(45) default NULL,
  `lastcomp` varchar(45) default NULL,
  `lastupdate` timestamp NOT NULL default '0000-00-00 00:00:00',
  `logged` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('admin','1','b8a9bdc4dbee33be2afa01e715783ec5',16,0000000,'visione','127.0.0.1','louis','2010-11-28 06:11:48',1),('operator','1','4b583376b2767b923c3e1da60d10de59',0,0000000,'operator','127.0.0.1','louis','2010-11-26 12:58:12',0),('adminpusat','1','f9db7b48e78ea581837d2f640d433008',16,0000000,'admin','127.0.0.1','louis','2010-11-26 17:41:50',0);
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

-- Dump completed on 2010-11-28  6:14:17
