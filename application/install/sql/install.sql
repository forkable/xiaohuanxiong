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
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for xwx_user
-- ----------------------------
DROP TABLE IF EXISTS `xwx_user`;
CREATE TABLE `xwx_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` char(32) NOT NULL,
  `nick_name` varchar(100) DEFAULT '',
  `mobile` char(11) DEFAULT '' COMMENT '会员手机号',
  `password` char(32) NOT NULL,
  `level` int default '0' COMMENT '为普通会员，1为vip会员',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT '0',
  `vip_expire_time` int(11) DEFAULT '0' COMMENT '会员到期时间',
  PRIMARY KEY (`id`) USING BTREE,
  unique key `username` (`username`) ,
  key `mobile` (`mobile`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for xhx_user_finance
-- ----------------------------
DROP TABLE IF EXISTS `xhx_user_finance`;
CREATE TABLE `xhx_user_finance`  (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT 0,
  `money` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '充值/消费金额',
  `usage` tinyint(4) NOT NULL COMMENT '用途，1.充值，2.购买vip，3.购买章节',
  `summary` text COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  key `user_id` (`user_id`) USING BTREE
) ENGINE = InnoDB CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for xhx_user_buy
-- ----------------------------
DROP TABLE IF EXISTS `xhx_user_buy`;
CREATE TABLE `xhx_user_buy`  (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT 0 COMMENT '购买用户ID',
  `chapter_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '购买漫画ID',
  `book_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '购买章节ID',
  `money` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '消费金额',
  `summary` text COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

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
  `pic_name` varchar(50) DEFAULT '' COMMENT '轮播图完整路径名',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `book_id` int(11) NOT NULL COMMENT '所属漫画ID',
  `title` varchar(50) NOT NULL COMMENT '轮播图标题',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for book
-- ----------------------------
DROP TABLE IF EXISTS `xwx_book`;
CREATE TABLE `xwx_book` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `book_name` varchar(50) NOT NULL COMMENT '漫画名',
  `nick_name` varchar(100) COMMENT '别名',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `last_time` int(11) DEFAULT '0' COMMENT '最后更新时间',
  `tags` varchar(100) DEFAULT '' COMMENT '分类',
  `summary` text COMMENT '简介',
  `end` tinyint(4) DEFAULT '0' COMMENT '0为连载，1为完结',
  `author_id` int(11) NOT NULL COMMENT '作者ID',
  `cover_url` varchar(255) DEFAULT '' COMMENT '封面图路径',
  `start_pay` int(10) NOT NULL DEFAULT '99999' COMMENT '第m话开始需要付费',
  `money` decimal(10,2) DEFAULT '0' COMMENT '每章所需费用',
  `area_id` int(11) NOT NULL COMMENT '漫画所属地区',
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
  `chapter_name` varchar(255) NOT NULL COMMENT '章节名称',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `book_id` int(10) unsigned NOT NULL COMMENT '章节所属漫画ID',
  `chapter_order` decimal(10,2) NOT NULL COMMENT '章节序',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `chapter_name` (`chapter_name`) USING BTREE,
  KEY `book_id` (`book_id`) USING BTREE,
  KEY `chapter_order` (`chapter_order`) USING BTREE
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
  `pic_order` decimal(10,2) NOT NULL COMMENT '图片序',
  `img_url` varchar(255) DEFAULT '' COMMENT '图片路径',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `chapter_id` (`chapter_id`) USING BTREE,
  KEY `pic_order` (`pic_order`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for tags
-- ----------------------------
DROP TABLE IF EXISTS `xwx_tags`;
CREATE TABLE `xwx_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(20) NOT NULL COMMENT '分类名',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  unique KEY `tag_name` (`tag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for xwx_friendship_link
-- ----------------------------
DROP TABLE IF EXISTS `xwx_friendship_link`;
CREATE TABLE `xwx_friendship_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '友链名',
  `url` varchar(255) NOT NULL COMMENT '友链地址',
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
  `area_name` varchar(32) NOT NULL COMMENT '地区名',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  unique key `area_name` (`area_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for xwx_user_book
-- ----------------------------
DROP TABLE IF EXISTS `xwx_user_book`;
CREATE TABLE `xwx_user_book` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `book_id` int(10) unsigned NOT NULL COMMENT '用户收藏的漫画ID',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  key book_id (`book_id`) USING BTREE,
  key user_id (`user_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for xwx_comments
-- ----------------------------
DROP TABLE IF EXISTS `xwx_comments`;
CREATE TABLE `xwx_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `book_id` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

