/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50529
Source Host           : localhost:3306
Source Database       : leaps_simple

Target Server Type    : MYSQL
Target Server Version : 50529
File Encoding         : 65001

Date: 2013-01-21 17:03:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `yun_admin`
-- ----------------------------
DROP TABLE IF EXISTS `yun_admin`;
CREATE TABLE `yun_admin` (
  `userid` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `encrypt` varchar(6) NOT NULL,
  `mobile` varchar(11) DEFAULT '',
  `email` varchar(40) DEFAULT '',
  `realname` varchar(50) DEFAULT '',
  `issuper` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lastloginip` varchar(15) DEFAULT '',
  `lastlogintime` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`userid`),
  KEY `username` (`username`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yun_admin
-- ----------------------------
INSERT INTO `yun_admin` VALUES ('1', 'admin', '5f5f11a10119801bbcbe8d87fd4514b6', 'GLJUWn', '18615271353', '85825770@qq.com', '徐同乐', '1', '127.0.0.1', '1358757495');
