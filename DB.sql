/*
Navicat MySQL Data Transfer

Source Server         : 192.168.20.2_mariadb@root
Source Server Version : 50505
Source Host           : 192.168.20.2:3306
Source Database       : scraper

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-10-17 10:22:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for search_content
-- ----------------------------
DROP TABLE IF EXISTS `search_content`;
CREATE TABLE `search_content` (
  `id` bigint(15) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `pcontent` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24100 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of search_content
-- ----------------------------

-- ----------------------------
-- Table structure for search_count
-- ----------------------------
DROP TABLE IF EXISTS `search_count`;
CREATE TABLE `search_count` (
  `hrefcount` bigint(15) DEFAULT NULL,
  `contentcount` bigint(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of search_count
-- ----------------------------
INSERT INTO `search_count` VALUES ('0', '0');

-- ----------------------------
-- Table structure for search_filter
-- ----------------------------
DROP TABLE IF EXISTS `search_filter`;
CREATE TABLE `search_filter` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `strategy_id` int(10) DEFAULT NULL,
  `string` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of search_filter
-- ----------------------------
INSERT INTO `search_filter` VALUES ('3', '2', 'http://news.qq.com');
INSERT INTO `search_filter` VALUES ('5', '1', 'http://news.xinhuanet.com');
INSERT INTO `search_filter` VALUES ('7', '3', 'http://news.163.com');
INSERT INTO `search_filter` VALUES ('8', '4', 'http://news.sohu.com/');
INSERT INTO `search_filter` VALUES ('9', '5', '.ifeng.com');
INSERT INTO `search_filter` VALUES ('10', '6', '.sina.com.cn');
INSERT INTO `search_filter` VALUES ('11', '0', 'http://');

-- ----------------------------
-- Table structure for search_filter_abandon
-- ----------------------------
DROP TABLE IF EXISTS `search_filter_abandon`;
CREATE TABLE `search_filter_abandon` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `strategy_id` int(10) DEFAULT NULL,
  `string` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of search_filter_abandon
-- ----------------------------
INSERT INTO `search_filter_abandon` VALUES ('1', '1', 'photo');
INSERT INTO `search_filter_abandon` VALUES ('2', '1', 'forum');

-- ----------------------------
-- Table structure for search_href
-- ----------------------------
DROP TABLE IF EXISTS `search_href`;
CREATE TABLE `search_href` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `contentid` bigint(20) DEFAULT NULL,
  `href` varchar(190) NOT NULL,
  `from_href` varchar(190) NOT NULL COMMENT '来源的href+',
  `strategy_id` int(10) NOT NULL,
  `num` int(10) NOT NULL DEFAULT '1' COMMENT '出现次数',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0代表的是未抓取过的url（现已作废）',
  PRIMARY KEY (`id`),
  KEY `href` (`href`) USING BTREE,
  KEY `contentid` (`contentid`)
) ENGINE=InnoDB AUTO_INCREMENT=607939 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of search_href
-- ----------------------------

-- ----------------------------
-- Table structure for search_orgin
-- ----------------------------
DROP TABLE IF EXISTS `search_orgin`;
CREATE TABLE `search_orgin` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `strategy_id` int(10) DEFAULT NULL,
  `href` varchar(190) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `strategy_id` (`strategy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of search_orgin
-- ----------------------------
INSERT INTO `search_orgin` VALUES ('1', '1', 'http://www.xinhuanet.com/');
INSERT INTO `search_orgin` VALUES ('2', '2', 'http://news.qq.com/');
INSERT INTO `search_orgin` VALUES ('5', '3', 'http://news.163.com/');
INSERT INTO `search_orgin` VALUES ('6', '4', 'http://www.sohu.com/');
INSERT INTO `search_orgin` VALUES ('7', '5', 'http://www.ifeng.com/');
INSERT INTO `search_orgin` VALUES ('8', '6', 'http://news.sina.com.cn/');
INSERT INTO `search_orgin` VALUES ('9', '0', 'http://www.sohu.com/');

-- ----------------------------
-- Table structure for search_strategy
-- ----------------------------
DROP TABLE IF EXISTS `search_strategy`;
CREATE TABLE `search_strategy` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `strategy_id` int(10) DEFAULT NULL,
  `strategy_name` varchar(255) DEFAULT NULL,
  `strategy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of search_strategy
-- ----------------------------
INSERT INTO `search_strategy` VALUES ('1', '1', '新华网', 'XinHua');
INSERT INTO `search_strategy` VALUES ('2', '2', '腾讯网', 'TengXun');
INSERT INTO `search_strategy` VALUES ('4', '3', '网易新闻', 'WangYi');
INSERT INTO `search_strategy` VALUES ('5', '4', '搜狐新闻', 'SouHu');
INSERT INTO `search_strategy` VALUES ('6', '5', '凤凰网', 'FengHuang');
INSERT INTO `search_strategy` VALUES ('7', '6', '新浪网', 'XinLang');
INSERT INTO `search_strategy` VALUES ('8', '0', '无条件全拉', 'All');
