-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.1.72-community - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for rogertiongdev
CREATE DATABASE IF NOT EXISTS `rogertiongdev` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `rogertiongdev`;

-- Dumping structure for table rogertiongdev.test_tbl_001
CREATE TABLE IF NOT EXISTS `test_tbl_001` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fld_title` varchar(255) DEFAULT NULL,
  `fld_body` text,
  `fld_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fld_deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table rogertiongdev.test_tbl_001: 0 rows
/*!40000 ALTER TABLE `test_tbl_001` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_tbl_001` ENABLE KEYS */;

-- Dumping structure for table rogertiongdev.test_tbl_002
CREATE TABLE IF NOT EXISTS `test_tbl_002` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tbl_001_id` int(11) NOT NULL DEFAULT '0',
  `fld_title` varchar(255) DEFAULT NULL,
  `fld_body` text,
  `fld_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fld_deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Dumping data for table rogertiongdev.test_tbl_002: 0 rows
/*!40000 ALTER TABLE `test_tbl_002` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_tbl_002` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
