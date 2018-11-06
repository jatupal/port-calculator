/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : port_calculator

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-10-21 22:24:52
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `future_plan`
-- ----------------------------
DROP TABLE IF EXISTS `future_plan`;
CREATE TABLE `future_plan` (
  `future_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `port_id` int(10) NOT NULL,
  `index_id` int(10) NOT NULL,
  `date` date NOT NULL,
  `symbol` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `amount` int(10) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`future_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of future_plan
-- ----------------------------

-- ----------------------------
-- Table structure for `index`
-- ----------------------------
DROP TABLE IF EXISTS `index`;
CREATE TABLE `index` (
  `index_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `index_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `future_contact` decimal(10,2) NOT NULL,
  `option_contact` decimal(10,2) NOT NULL,
  PRIMARY KEY (`index_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of index
-- ----------------------------

-- ----------------------------
-- Table structure for `option_plan`
-- ----------------------------
DROP TABLE IF EXISTS `option_plan`;
CREATE TABLE `option_plan` (
  `option_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `port_id` int(10) NOT NULL,
  `index_id` int(10) NOT NULL,
  `date` date NOT NULL,
  `type` enum('Call','Put') COLLATE utf8_unicode_ci NOT NULL,
  `symbol` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `premium` decimal(10,2) NOT NULL,
  `amount` int(10) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of option_plan
-- ----------------------------

-- ----------------------------
-- Table structure for `portferio`
-- ----------------------------
DROP TABLE IF EXISTS `portferio`;
CREATE TABLE `portferio` (
  `port_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `port_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`port_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of portferio
-- ----------------------------

-- ----------------------------
-- Table structure for `symbol`
-- ----------------------------
DROP TABLE IF EXISTS `symbol`;
CREATE TABLE `symbol` (
  `symbol_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `index_id` int(10) NOT NULL,
  `symbol_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('Option','Future') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Future',
  PRIMARY KEY (`symbol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of symbol
-- ----------------------------

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `is_admin` int(1) NOT NULL DEFAULT '0',
  `is_active` enum('no','yes') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `code_verify` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'admin', 'bookdee2017', 'Admin', '-', '-', '1', 'yes', '2017-02-16 08:06:12', '0000-00-00 00:00:00', '');
INSERT INTO `user` VALUES ('2', 'bdadmin', 'Bd2017', 'Bookdee Admin', 'ittiya@bookdee.com', '', '1', 'yes', '2017-03-07 22:06:52', '0000-00-00 00:00:00', '');
