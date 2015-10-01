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
