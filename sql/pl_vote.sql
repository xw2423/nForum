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
-- Table structure for table `pl_vote`
--

DROP TABLE IF EXISTS `pl_vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pl_vote` (
  `vid` int(11) unsigned NOT NULL auto_increment,
  `uid` varchar(20) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `desc` varchar(200) NOT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `num` int(11) NOT NULL default '0' COMMENT '参与人数',
  `type` tinyint(4) NOT NULL default '0' COMMENT '0单选 1多选',
  `limit` tinyint(4) NOT NULL default '0' COMMENT '个人最大票数 0无限制',
  `aid` int(11) NOT NULL COMMENT '对应帖子id',
  `result_voted` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL default '1' COMMENT '有效1 删除0',
  PRIMARY KEY  (`vid`),
  KEY `status` (`status`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='plugin-vote';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pl_vote_item`
--

DROP TABLE IF EXISTS `pl_vote_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pl_vote_item` (
  `viid` int(11) unsigned NOT NULL auto_increment,
  `vid` int(11) unsigned NOT NULL,
  `label` varchar(50) NOT NULL,
  `num` int(11) NOT NULL default '0',
  PRIMARY KEY  (`viid`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='投票项';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pl_vote_result`
--

DROP TABLE IF EXISTS `pl_vote_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pl_vote_result` (
  `vid` int(11) unsigned NOT NULL,
  `uid` varchar(20) NOT NULL,
  `result` varchar(200) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`vid`,`uid`),
  KEY `vid` (`vid`),
  KEY `uid` (`uid`)
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

-- Dump completed on 2010-12-28 23:11:11
