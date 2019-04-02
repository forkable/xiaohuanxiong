-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `xwx_admin`;
CREATE TABLE `xwx_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` char(32) NOT NULL,
  `password` char(32) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `last_login_time` int(11) DEFAULT '0',
  `last_login_ip` varchar(100) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  key `username` (`username`) unique
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for xwx_user
-- ----------------------------
DROP TABLE IF EXISTS `xwx_user`;
CREATE TABLE `xwx_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` char(32) NOT NULL,
  `nick_name` varchar(100) DEFAULT '',
  `mobile` char(11) DEFAULT '',
  `password` char(32) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  key `username` (`username`) unique,
  key `mobile` (`mobile`) unique
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for author
-- ----------------------------
DROP TABLE IF EXISTS `xwx_author`;
CREATE TABLE `xwx_author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_name` varchar(100) NOT NULL,
   `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for banner
-- ----------------------------
DROP TABLE IF EXISTS `xwx_banner`;
CREATE TABLE `xwx_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pic_name` varchar(50) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `book_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for book
-- ----------------------------
DROP TABLE IF EXISTS `xwx_book`;
CREATE TABLE `xwx_book` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `book_name` varchar(50) NOT NULL,
  `nick_name` varchar(100),
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `last_time` int(11) DEFAULT '0',
  `tags` varchar(100) DEFAULT '',
  `summary` text,
  `end` tinyint(4) DEFAULT '0',
  `author_id` int(11) NOT NULL,
  `cover_url` varchar(255) DEFAULT '',
  `start_pay` int(10) NOT NULL DEFAULT '99999' COMMENT '第m话开始需要付费',
  `money` decimal(10,2) DEFAULT '0',
  `area_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `tags` (`tags`) USING BTREE,
  KEY `end` (`end`) USING BTREE,
  KEY `author_id` (`author_id`) USING BTREE,
  FULLTEXT KEY `fidx` (`book_name`,`summary`) with parser ngram
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for chapter
-- ----------------------------
DROP TABLE IF EXISTS `xwx_chapter`;
CREATE TABLE `xwx_chapter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chapter_name` varchar(255) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `book_id` int(10) unsigned NOT NULL,
  `order` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `chapter_name` (`chapter_name`) USING BTREE,
  KEY `book_id` (`book_id`) USING BTREE,
  KEY `order` (`order`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for photo
-- ----------------------------
DROP TABLE IF EXISTS `xwx_photo`;
CREATE TABLE `xwx_photo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chapter_id` int(11) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `order` decimal(10,2) NOT NULL,
  `img_url` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `chapter_id` (`chapter_id`) USING BTREE,
  KEY `order` (`order`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for tags
-- ----------------------------
DROP TABLE IF EXISTS `xwx_tags`;
CREATE TABLE `xwx_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(20) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `tag_name` (`tag_name`) unique
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for xwx_friendship_link
-- ----------------------------
DROP TABLE IF EXISTS `xwx_friendship_link`;
CREATE TABLE `xwx_friendship_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for xwx_area
-- ----------------------------
DROP TABLE IF EXISTS `xwx_area`;
CREATE TABLE `xwx_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area_name` varchar(32) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  key `area_name` (`area_name`) unique
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

CREATE TABLE `xwx_user_book` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `book_id` int(10) unsigned NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  key book_id (`book_id`) USING BTREE,
  key user_id (`user_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4;

