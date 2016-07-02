--
-- 设置表
--
DROP TABLE IF EXISTS `lyj_setting`;
CREATE TABLE `lyj_setting` (
  `id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `skey` VARCHAR(100)     NOT NULL DEFAULT '要设置的键',
  `sval` VARCHAR(1024)    NOT NULL DEFAULT '要设置的值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `skey` (`skey`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COMMENT = '设置表';

INSERT INTO `lyj_setting` (`id`, `skey`, `sval`) VALUES (1, 'contact', '李玉江，QQ:1032694760');
INSERT INTO `lyj_setting` (`id`, `skey`, `sval`)
VALUES (2, 'seo_title', '标题-PHP_MVC_REST');
INSERT INTO `lyj_setting` (`id`, `skey`, `sval`)
VALUES (3, 'seo_keywords', '关键词,PHP_MVC_REST');
INSERT INTO `lyj_setting` (`id`, `skey`, `sval`) VALUES (4, 'seo_description', '这是PHP_MVC_REST框架示例，结合安卓端开发框架使用');
INSERT INTO `lyj_setting` (`id`, `skey`, `sval`) VALUES (5, 'copyright', 'Copyright © 穿青人');

--
-- 栏目表
--
DROP TABLE IF EXISTS `lyj_category`;
CREATE TABLE `lyj_category` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)     NOT NULL DEFAULT '默认栏目',
  `description` VARCHAR(255)     NOT NULL DEFAULT '栏目简介',
  `sortby`      INT(5)           NOT NULL DEFAULT 0,
  `hidden`      INT(2)           NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COMMENT = '栏目表';

INSERT INTO `lyj_category` (`id`, `name`, `description`, `sortby`, `hidden`) VALUES (1, '安卓软件', '', 1, 0);
INSERT INTO `lyj_category` (`id`, `name`, `description`, `sortby`, `hidden`) VALUES (2, '苹果游戏', '', 2, 0);
INSERT INTO `lyj_category` (`id`, `name`, `description`, `sortby`, `hidden`) VALUES (3, '跨平台应用', '', 3, 0);
INSERT INTO `lyj_category` (`id`, `name`, `description`, `sortby`, `hidden`) VALUES (4, '网站', '', 15, 1);
INSERT INTO `lyj_category` (`id`, `name`, `description`, `sortby`, `hidden`) VALUES (5, '其他', '', 20, 0);

--
-- 文章表
--
DROP TABLE IF EXISTS `lyj_article`;
CREATE TABLE `lyj_article` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT(10) UNSIGNED NOT NULL,
  `title`       VARCHAR(255)     NOT NULL DEFAULT '无题',
  `content`     TEXT             NOT NULL,
  `visit_count` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `timeline`    INT(10)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COMMENT = '文章表';

INSERT INTO `lyj_article` (`id`, `category_id`, `title`, `content`, `timeline`) VALUES (1, 1, '测试标题', '测试内容', 0);


--
-- 登录令牌表
--
DROP TABLE IF EXISTS `lyj_token`;
CREATE TABLE IF NOT EXISTS `lyj_token` (
  `id`              INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         INT(10) UNSIGNED NOT NULL,
  `token`           VARCHAR(32)      NOT NULL,
  `expire_timeline` INT(10)          NOT NULL,
  `update_timeline` INT(10)          NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COMMENT = '登录令牌表';

--
-- 用户表
--
DROP TABLE IF EXISTS `lyj_user`;
CREATE TABLE IF NOT EXISTS `lyj_user` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account`   VARCHAR(15)               DEFAULT NULL,
  `password`  VARCHAR(32)               DEFAULT NULL,
  `nick`      VARCHAR(15)      NOT NULL,
  `face`      VARCHAR(255)              DEFAULT NULL,
  `sex`       INT(2)                    DEFAULT 0,
  `device_id` VARCHAR(20)      NOT NULL,
  `is_forbidden`    INT(10)          NOT NULL DEFAULT 0,
  `is_app`    INT(2)                    DEFAULT 0,
  `timeline`  INT(10)          NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COMMENT = '用户表';

INSERT INTO `lyj_user` (`id`, `account`, `password`, `nick`, `face`, `sex`, `device_id`, `is_forbidden`, `is_app`, `timeline`)
VALUES (1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', '穿青人', '', 1, '15244545455', 0, 0, 0);
INSERT INTO `lyj_user` (`id`, `account`, `password`, `nick`, `face`, `sex`, `device_id`, `is_forbidden`, `is_app`, `timeline`)
VALUES (2, 'lyj', 'e10adc3949ba59abbe56e057f20f883e', '穿青人', '', 1, '15244545455', 0, 0, 0);

--
-- 友链表
--
DROP TABLE IF EXISTS `lyj_link`;
CREATE TABLE `lyj_link` (
  `id`          INT(8) UNSIGNED  NOT NULL AUTO_INCREMENT,
  `category_id` INT(10) UNSIGNED NOT NULL,
  `name`        VARCHAR(200)     NOT NULL DEFAULT '链接名称',
  `description` VARCHAR(255)     NOT NULL DEFAULT '链接简介',
  `url`         VARCHAR(255)     NOT NULL DEFAULT 'http://',
  `icon`        VARCHAR(255)     NOT NULL DEFAULT 'http://',
  `sortby`      INT(5)           NOT NULL DEFAULT 0,
  `hidden`      INT(2)           NOT NULL DEFAULT 0,
  `timeline`    INT(8)           NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COMMENT = '友链表';

INSERT INTO `lyj_link` (`id`, `category_id`, `name`, `description`, `url`, `icon`, `sortby`, `hidden`, `timeline`)
VALUES (1, 1, '输入法皮肤控', '我的键盘我做主，手机端输入法皮肤编辑器——输入法皮肤控', 'http://ime.qqtheme.cn',
        'http://ime.qqtheme.cn/views/assets/img/icon.png', 0, 0, 0);
