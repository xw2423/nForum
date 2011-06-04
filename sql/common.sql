-- MySQL dump 10.11
--
-- Host: localhost    Database: bbs
-- ------------------------------------------------------
-- Server version	5.0.86-log

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
-- Table structure for table `widget`
--

DROP TABLE IF EXISTS `widget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widget` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `uid` varchar(15) NOT NULL,
  `wid` varchar(40) NOT NULL,
  `title` varchar(30) NOT NULL,
  `color` tinyint(4) NOT NULL,
  `col` tinyint(4) unsigned NOT NULL,
  `row` smallint(6) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`),
  KEY `uid_2` (`uid`,`wid`),
  KEY `uid_3` (`uid`,`col`,`row`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adv`
--

DROP TABLE IF EXISTS `adv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adv` (
  `aid` int(11) unsigned NOT NULL auto_increment,
  `type` tinyint(4) NOT NULL COMMENT '1进站,2banner,3进站图标，4左边banner',
  `file` varchar(30) NOT NULL,
  `url` varchar(100) NOT NULL,
  `sTime` date NOT NULL COMMENT 'banner开始时间',
  `eTime` date NOT NULL COMMENT 'banner结束时间',
  `switch` tinyint(4) NOT NULL default '1' COMMENT '小图标开关',
  `weight` tinyint(4) NOT NULL default '1' COMMENT 'type为3,4的顺序',
  `privilege` tinyint(4) NOT NULL default '0' COMMENT 'banner特权显示',
  `home` tinyint(4) NOT NULL default '0' COMMENT '首页可见',
  `remark` varchar(50) NOT NULL COMMENT '备注',
  PRIMARY KEY  (`aid`),
  KEY `type` (`type`,`switch`),
  KEY `type_2` (`type`,`privilege`,`home`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-12-28 23:07:49
