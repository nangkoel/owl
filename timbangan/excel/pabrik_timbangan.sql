-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 25, 2010 at 01:27 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `owl`
--

-- --------------------------------------------------------

--
-- Table structure for table `pabrik_timbangan`
--

DROP TABLE IF EXISTS `pabrik_timbangan`;
CREATE TABLE IF NOT EXISTS `pabrik_timbangan` (
  `notransaksi` char(12) NOT NULL,
  `tanggal` datetime NOT NULL,
  `kodeorg` char(10) NOT NULL,
  `kodecustomer` char(10) NOT NULL COMMENT 'kode cust',
  `bjr` double NOT NULL COMMENT 'bjr stup blok thn tnm',
  `jumlahtandan` mediumtext NOT NULL COMMENT 'jumlah tandan',
  `kodebarang` char(11) NOT NULL COMMENT 'kode tbs, cpo, pk, cangkang, jangkos',
  `jammasuk` time NOT NULL COMMENT 'jam masuk',
  `beratmasuk` double NOT NULL COMMENT 'berat masuk',
  `jamkeluar` time NOT NULL,
  `beratkeluar` double NOT NULL COMMENT 'berat keluar',
  `nokendaraan` varchar(8) NOT NULL COMMENT 'nopol',
  `supir` varchar(30) NOT NULL,
  `nospb` varchar(30) NOT NULL COMMENT 'no spb/nodok lain',
  `petugassortasi` varchar(20) DEFAULT NULL COMMENT 'nama petugas sortasi',
  `timbangonoff` bit(1) NOT NULL DEFAULT b'0' COMMENT 'apakah timbangan on atau entry manual',
  `statussortasi` bit(1) DEFAULT b'0',
  `nokontrak` varchar(30) NOT NULL COMMENT 'no kontrak',
  `nodo` varchar(30) NOT NULL COMMENT 'no do',
  `intex` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1 = internal, 2 =afiliasi, 0 external',
  `nosipb` varchar(30) NOT NULL,
  `thntm1` int(4) unsigned NOT NULL,
  `thntm2` int(4) unsigned NOT NULL,
  `thntm3` int(4) unsigned NOT NULL,
  `jumlahtandan2` double NOT NULL,
  `jumlahtandan3` double NOT NULL,
  `brondolan` double NOT NULL,
  `username` varchar(45) NOT NULL,
  PRIMARY KEY (`notransaksi`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pabrik_timbangan`
--

