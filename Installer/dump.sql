SET NAMES utf8mb4;

# Dump of table admin_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_menu`;

CREATE TABLE `admin_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `catalog` int(11) NOT NULL DEFAULT '0',
  `section` varchar(32) NOT NULL DEFAULT '',
  `pos` int(11) NOT NULL DEFAULT '0',
  `pop` int(11) NOT NULL DEFAULT '0',
  `link` varchar(128) NOT NULL DEFAULT '',
  `type` varchar(32) NOT NULL DEFAULT '',
  `icon` varchar(128) NOT NULL DEFAULT '',
  `do_hits` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin_menu` (`id`, `name`, `catalog`, `section`, `pos`, `pop`, `link`, `type`, `icon`, `do_hits`, `hidden`, `page_id`)
VALUES
    (3, 'Меню раздела', 0, 'UsersSection', 2, 0, '', 'catalog', '', 0, 0, 0),
    (4, 'Создать пользователя', 3, 'UsersSection', 1, 0, '[tab://]&a=members&i=add', 'normal', '03.png', 0, 0, 0),
    (5, 'Поиск пользователей', 3, 'UsersSection', 2, 0, '[tab://]&a=members&i=search', 'normal', '09.png', 0, 0, 0),
    (6, 'Группы пользователей', 3, 'UsersSection', 3, 0, '[tab://]&a=groups', 'normal', '03.png', 0, 0, 0),
    (7, 'Создать группу', 3, 'UsersSection', 4, 0, '[tab://]&a=groups&i=add', 'normal', '03.png', 0, 0, 0),
    (8, 'Меню раздела', 0, 'SystemSection', 2, 0, '', 'catalog', '', 0, 0, 0),
    (9, 'Кеширование', 8, 'SystemSection', 1, 0, '[tab://]&a=system&i=caching', 'normal', '03.png', 0, 0, 0),
    (10, 'Блокировка', 8, 'SystemSection', 2, 0, '[tab://]&a=system&i=blocking', 'normal', '03.png', 0, 0, 0),
    (11, 'Отладка', 8, 'SystemSection', 3, 0, '[tab://]&a=system&i=debugging', 'normal', '03.png', 0, 0, 0);

# Dump of table admin_sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_sessions`;

CREATE TABLE `admin_sessions` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `session_ip_address` varchar(32) NOT NULL DEFAULT '',
  `session_member_name` varchar(250) NOT NULL DEFAULT '',
  `session_member_id` mediumint(9) NOT NULL DEFAULT '0',
  `session_member_pass_hash` varchar(32) NOT NULL DEFAULT '',
  `session_location` varchar(64) NOT NULL DEFAULT '',
  `session_log_in_time` int(11) NOT NULL DEFAULT '0',
  `session_running_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

# Dump of table conf_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `conf_settings`;

CREATE TABLE `conf_settings` (
  `conf_id` int(11) NOT NULL AUTO_INCREMENT,
  `conf_title` text NOT NULL,
  `conf_desc` text NOT NULL,
  `conf_group` int(11) NOT NULL,
  `conf_type` varchar(255) NOT NULL DEFAULT '',
  `conf_key` varchar(128) NOT NULL DEFAULT '',
  `conf_value` text NOT NULL,
  `conf_default` text NOT NULL,
  `conf_extra` text NOT NULL,
  `conf_position` smallint(6) NOT NULL DEFAULT '0',
  `conf_add_cache` tinyint(1) NOT NULL DEFAULT '1',
  `conf_noshow` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`conf_id`),
  UNIQUE KEY `conf_key` (`conf_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `conf_settings` (`conf_id`, `conf_title`, `conf_desc`, `conf_group`, `conf_type`, `conf_key`, `conf_value`, `conf_default`, `conf_extra`, `conf_position`, `conf_add_cache`, `conf_noshow`)
VALUES
    (1,'Часовой пояс сайта','Указать часовой пояс в котором работает сайт',4,'input','time_offset','','7','',1,1,0),
    (2,'Тонкая настройка времени','Можно указать сколько минуть прибавить или отнять от времени сервера, например <b>-2</b>',4,'input','time_adjust','','0','',2,1,0),
    (3,'Имя сайта','',1,'input','site_name','RAMPAGE CMS','','',1,1,0),
    (5,'Входящий адрес','E-mail на который будут приходить уведомления о различных событиях на сайте.',2,'input','email_in','','admin@localhost','',10,1,0),
    (6,'Исходящий адрес','E-mail который будет указан при отправке сообщений от имени сайта',2,'input','email_out','','admin@localhost','',11,1,0),
    (7,'Ограничивать работу сайта при повышенной нагрузке','0 - без ограничений, 100 - максимальное значение',3,'input','load_limit','','0','',2,1,0),
    (8,'Привязывать сессию к IP-адресу','',3,'yes_no','match_ipaddress','0','1','',3,1,0),
    (9,'Привязывать сессию к браузеру','',3,'yes_no','match_browser','','1','',4,1,0),
    (10,'Ключевые слова','Ключевые слова, встречающиеся на вашем сайте.\nКрайне редко используются роботами, но иногда может быть полезно.',1,'textarea','site_keywords','cистема,управление,сайтами,rampage,cms','','',3,1,0),
    (11,'Описание сайта','Произвольное описание сайта. Используется в основном при автоматической регистрации в веб-каталогах.',1,'textarea','site_desc','Сайт посвящен системе управления сайтами RAMPAGE CMS','','',4,1,0),
    (12,'Заголовок','Показывается в заголовке окна на всех страницах, кроме тех на которых определен собственный',1,'input','site_title','Система управления сайтами RAMPAGE CMS','','',2,1,0),
    (13,'Адрес SMTP-сервера','',2,'input','smtp_server_addr','','localhost','',6,1,0),
    (14,'Порт SMTP-сервера','',2,'input','smtp_server_port','','25','',7,1,0),
    (15,'Имя сайта','Имя, от которого будут отправляться письма с сайта',2,'input','email_site_person','','','',8,1,0),
    (16,'Использовать SMTP для отправки E-mail','Если включено, то отправка писем будет по SMTP, иначе с помощью функции mail()',2,'yes_no','email_use_smtp','','0','',5,1,0);

# Dump of table conf_settings_titles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `conf_settings_titles`;

CREATE TABLE `conf_settings_titles` (
  `conf_title_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `conf_title_title` varchar(255) NOT NULL DEFAULT '',
  `conf_title_desc` text NOT NULL,
  `conf_title_noshow` tinyint(1) NOT NULL DEFAULT '0',
  `conf_title_keyword` varchar(200) NOT NULL DEFAULT '0',
  `conf_title_position` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`conf_title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `conf_settings_titles` (`conf_title_id`, `conf_title_title`, `conf_title_desc`, `conf_title_noshow`, `conf_title_keyword`, `conf_title_position`)
VALUES
    (1,'Информация о сайте','Название сайта, заголовок, описание, ключевые слова',0,'0',2),
    (2,'Электронная почта','Входящие и исходящие адреса, настройки почтового сервера',0,'0',3),
    (3,'Настройки доступа','',0,'0',6),
    (4,'Настройки времени','',0,'0',4);

# Dump of table groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `g_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `g_title` varchar(32) NOT NULL DEFAULT '',
  `suffix` varchar(250) DEFAULT NULL,
  `prefix` varchar(250) DEFAULT NULL,
  `g_access` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `groups` (`g_id`, `g_title`, `suffix`, `prefix`, `g_access`)
VALUES
    (1, 'Ожидающие', '</span>', '<span style=\'color:gray\'>', '{\"adminPanel\":false,\"admin\":{\"desktop\":false,\"tree\":false,\"content\":false,\"groups\":false,\"users\":false,\"settings\":false,\"system\":false,"module":false},\"offline\":false}'),
    (2, 'Гости', '</span>', '<span style=\'color:purple\'>', '{\"adminPanel\":false,\"admin\":{\"desktop\":false,\"tree\":false,\"content\":false,\"groups\":false,\"users\":false,\"settings\":false,\"system\":false,"module":false},\"offline\":false}'),
    (3, 'Пользователи', '</span>', '<span style=\'color:green\'>', '{\"adminPanel\":false,\"admin\":{\"desktop\":false,\"tree\":false,\"content\":false,\"groups\":false,\"users\":false,\"settings\":false,\"system\":false,"module":false},\"offline\":false}'),
    (4, 'Администраторы', '</span>', '<span style=\'color:red; border-bottom: 1px dashed red;\'>', '{\"adminPanel\":true,\"admin\":{\"desktop\":true,\"tree\":true,\"content\":true,\"groups\":true,\"users\":true,\"settings\":true,\"system\":true,"module":true},\"offline\":true}');

# Dump of table members
# ------------------------------------------------------------

DROP TABLE IF EXISTS `members`;

CREATE TABLE `members` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL DEFAULT '',
  `dname` varchar(255) NOT NULL DEFAULT '',
  `pass_hash` varchar(32) NOT NULL DEFAULT '',
  `pass_salt` varchar(5) NOT NULL DEFAULT '',
  `mgroup` smallint(6) NOT NULL DEFAULT '0',
  `email` varchar(150) NOT NULL DEFAULT '',
  `reg_time` int(11) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '',
  `time_offset` varchar(10) DEFAULT NULL,
  `last_visit` int(11) DEFAULT '0',
  `last_activity` int(11) DEFAULT '0',
  `dst_in_use` tinyint(1) NOT NULL DEFAULT '0',
  `login_key` varchar(32) NOT NULL DEFAULT '',
  `login_key_expire` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mgroup` (`mgroup`),
  KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

# Dump of table members_extra
# ------------------------------------------------------------

DROP TABLE IF EXISTS `members_extra`;

CREATE TABLE `members_extra` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `surname` varbinary(255) DEFAULT '',
  `patronymic` varchar(255) DEFAULT '',
  `birthday` varchar(255) DEFAULT '',
  `phone` varchar(255) DEFAULT '',
  `city` varchar(255) DEFAULT '',
  `address` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

# Dump of table modules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `modules`;

CREATE TABLE `modules` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` text NOT NULL,
    `title` text NOT NULL,
    `description` text NOT NULL,
    `main_page_id` int(10) unsigned DEFAULT NULL,
    `item_page_layout` varchar(128) DEFAULT NULL,
    `item_page_placeholder` varchar(128) DEFAULT NULL,
    `item_text_folder_id` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

# Dump of table sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(32) NOT NULL DEFAULT '0',
  `member_name` varchar(64) DEFAULT NULL,
  `member_id` mediumint(9) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) DEFAULT NULL,
  `browser` varchar(200) NOT NULL DEFAULT '',
  `running_time` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `member_group` smallint(6) DEFAULT NULL,
  `in_error` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

# Dump of table site_tree
# ------------------------------------------------------------

DROP TABLE IF EXISTS `site_tree`;

CREATE TABLE `site_tree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pagetitle` varchar(255) NOT NULL DEFAULT '',
  `longtitle` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(100) DEFAULT NULL,
  `published` int(11) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0',
  `isfolder` int(11) NOT NULL DEFAULT '0',
  `templates` mediumtext NOT NULL,
  `layout` varchar(128) NOT NULL DEFAULT '1',
  `searchable` int(11) NOT NULL DEFAULT '1',
  `cacheable` int(11) NOT NULL DEFAULT '1',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `createdon` int(11) NOT NULL DEFAULT '0',
  `editedby` int(11) NOT NULL DEFAULT '0',
  `editedon` int(11) NOT NULL DEFAULT '0',
  `deleted` int(11) NOT NULL DEFAULT '0',
  `deletedon` int(11) NOT NULL DEFAULT '0',
  `deletedby` int(11) NOT NULL DEFAULT '0',
  `publishedon` int(11) NOT NULL DEFAULT '0',
  `publishedby` int(11) NOT NULL DEFAULT '0',
  `donthit` tinyint(1) NOT NULL DEFAULT '0',
  `haskeywords` tinyint(1) NOT NULL DEFAULT '0',
  `hasmetatags` tinyint(1) NOT NULL DEFAULT '0',
  `menu_pos` int(10) unsigned NOT NULL DEFAULT '0',
  `admin_pr` int(10) unsigned NOT NULL DEFAULT '0',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `admin_groups_access` varchar(255) NOT NULL DEFAULT '',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `in_module` tinyint(1) NOT NULL DEFAULT '0',
  `link` int(10) unsigned NOT NULL DEFAULT '0',
  `is_link` tinyint(1) DEFAULT NULL,
  `only_user` tinyint(1) DEFAULT '0',
  `show_in_admin_menu` tinyint(1) DEFAULT '1',
  `show_in_public_menu` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `parent` (`parent`),
  KEY `aliasidx` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `site_tree` (`id`, `pagetitle`, `longtitle`, `alias`, `published`, `parent`, `isfolder`, `templates`, `layout`, `searchable`, `cacheable`, `createdby`, `createdon`, `editedby`, `editedon`, `deleted`, `deletedon`, `deletedby`, `publishedon`, `publishedby`, `donthit`, `haskeywords`, `hasmetatags`, `menu_pos`, `admin_pr`, `keywords`, `admin_groups_access`, `hidden`, `in_module`, `link`, `is_link`, `only_user`, `show_in_admin_menu`, `show_in_public_menu`)
VALUES
    (1,'Информация','','index',1,0,0,'a:1:{s:31:\"Основной_контент\";a:3:{s:4:\"type\";s:7:\"content\";s:2:\"id\";s:1:\"1\";s:7:\"noadmin\";s:1:\"0\";}}','index',1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,185,'','a:0:{}',0,0,0,0,0,1,1),
    (2,'Инсталляция','','installation',1,0,1,'a:1:{s:31:\"Основной_контент\";a:2:{s:4:\"type\";s:7:\"content\";s:2:\"id\";s:2:\"15\";}}','index',1,1,0,0,0,0,0,0,0,0,0,0,0,0,1,0,'','a:0:{}',0,0,0,0,0,1,1),
    (3,'Документация','','documentation',1,0,1,'a:1:{s:31:\"Основной_контент\";a:3:{s:4:\"type\";s:7:\"content\";s:2:\"id\";s:2:\"16\";s:7:\"noadmin\";s:1:\"0\";}}','index',1,1,0,0,0,0,0,0,0,0,0,0,0,0,3,0,'','a:0:{}',0,0,0,0,0,1,1),
    (4,'Конфигурация сервера','','configuration',1,2,0,'a:1:{s:31:\"Основной_контент\";a:2:{s:4:\"type\";s:7:\"content\";s:2:\"id\";s:2:\"17\";}}','index',1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','a:0:{}',0,0,0,0,0,1,1),
    (5,'Начальная установка','','setup',1,2,0,'a:1:{s:31:\"Основной_контент\";a:2:{s:4:\"type\";s:7:\"content\";s:2:\"id\";s:2:\"18\";}}','index',1,1,0,0,0,0,0,0,0,0,0,0,0,0,1,0,'','a:0:{}',0,0,0,0,0,1,1),
    (6,'Начало работы','','beginning',1,3,0,'a:1:{s:31:\"Основной_контент\";a:3:{s:4:\"type\";s:7:\"content\";s:2:\"id\";s:2:\"19\";s:7:\"noadmin\";s:1:\"0\";}}','index',1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','a:0:{}',0,0,0,0,0,1,1),
    (7,'Структура сайта','','structure',1,3,0,'a:1:{s:31:\"Основной_контент\";a:2:{s:4:\"type\";s:7:\"content\";s:2:\"id\";s:2:\"20\";}}','index',1,1,0,0,0,0,0,0,0,0,0,0,0,0,1,0,'','a:0:{}',0,0,0,0,0,1,1),
    (8,'Модули','','modules',1,3,0,'a:1:{s:31:\"Основной_контент\";a:2:{s:4:\"type\";s:7:\"content\";s:2:\"id\";s:2:\"21\";}}','index',1,1,0,0,0,0,0,0,0,0,0,0,0,0,2,0,'','a:0:{}',0,0,0,0,0,1,1),
    (9,'Пользователи','','users',1,3,0,'a:1:{s:31:\"Основной_контент\";a:2:{s:4:\"type\";s:7:\"content\";s:2:\"id\";s:2:\"22\";}}','index',1,1,0,0,0,0,0,0,0,0,0,0,0,0,3,0,'','a:0:{}',0,0,0,0,0,1,1),
    (10,'Настройки','','settings',1,3,0,'a:1:{s:31:\"Основной_контент\";a:2:{s:4:\"type\";s:7:\"content\";s:2:\"id\";s:2:\"23\";}}','index',1,1,0,0,0,0,0,0,0,0,0,0,0,0,4,0,'','a:0:{}',0,0,0,0,0,1,1),
    (11,'Системные функции','','system',1,3,0,'a:1:{s:31:\"Основной_контент\";a:2:{s:4:\"type\";s:7:\"content\";s:2:\"id\";s:2:\"24\";}}','index',1,1,0,0,0,0,0,0,0,0,0,0,0,0,5,0,'','a:0:{}',0,0,0,0,0,1,1);

# Dump of table task_logs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `task_logs`;

CREATE TABLE `task_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_title` varchar(255) NOT NULL DEFAULT '',
  `log_date` int(11) NOT NULL DEFAULT '0',
  `log_ip` varchar(16) NOT NULL DEFAULT '0',
  `log_desc` text NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

# Dump of table task_manager
# ------------------------------------------------------------

DROP TABLE IF EXISTS `task_manager`;

CREATE TABLE `task_manager` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_title` varchar(255) NOT NULL DEFAULT '',
  `task_file` varchar(255) NOT NULL DEFAULT '',
  `task_next_run` int(11) NOT NULL DEFAULT '0',
  `task_week_day` tinyint(1) NOT NULL DEFAULT '-1',
  `task_month_day` smallint(6) NOT NULL DEFAULT '-1',
  `task_hour` smallint(6) NOT NULL DEFAULT '-1',
  `task_minute` smallint(6) NOT NULL DEFAULT '-1',
  `task_cronkey` varchar(32) NOT NULL DEFAULT '',
  `task_log` tinyint(1) NOT NULL DEFAULT '0',
  `task_description` text NOT NULL,
  `task_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `task_key` varchar(30) NOT NULL DEFAULT '',
  `task_safemode` tinyint(1) NOT NULL DEFAULT '0',
  `task_locked` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_id`),
  KEY `task_next_run` (`task_next_run`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

# Dump of table vfs_docs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `vfs_docs`;

CREATE TABLE `vfs_docs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `real_name` varchar(255) NOT NULL DEFAULT '',
  `sys_name` varchar(40) NOT NULL DEFAULT '',
  `time_upload` varchar(10) NOT NULL DEFAULT '',
  `file_ext` varchar(6) NOT NULL DEFAULT '',
  `file_size` int(10) unsigned NOT NULL DEFAULT '0',
  `vfs_folder` int(10) unsigned NOT NULL DEFAULT '0',
  `group_access` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

# Dump of table vfs_folders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `vfs_folders`;

CREATE TABLE `vfs_folders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `parent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `vfs_folders` (`id`, `name`, `parent`)
VALUES
    (1,'Images',0),
    (2,'Docs',0),
    (3,'Texts',0),
    (9,'Инсталляция',1),
    (10,'Документация',3),
    (11,'Конфигурация сервера',9),
    (12,'Начальная установка',9),
    (13,'Начало работы',14),
    (14,'Документация',1),
    (15,'Структура сайта',14),
    (16,'Системные функции',14),
    (17,'Инсталляция',3);

# Dump of table vfs_imgs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `vfs_imgs`;

CREATE TABLE `vfs_imgs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `real_name` varchar(255) NOT NULL DEFAULT '',
  `sys_name` varchar(40) NOT NULL DEFAULT '',
  `thumb_name` varchar(40) NOT NULL DEFAULT '',
  `time_upload` varchar(10) NOT NULL DEFAULT '',
  `file_ext` varchar(6) NOT NULL DEFAULT '',
  `file_size` int(10) unsigned NOT NULL DEFAULT '0',
  `vfs_folder` int(10) unsigned NOT NULL DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `thumb_width` int(11) NOT NULL DEFAULT '0',
  `thumb_height` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `vfs_imgs` (`id`, `real_name`, `sys_name`, `thumb_name`, `time_upload`, `file_ext`, `file_size`, `vfs_folder`, `width`, `height`, `thumb_width`, `thumb_height`)
VALUES
    (10,'Старый но не бесполезный','85a4069436225ae5add4976b5efc091d.jpg','d190cfe5b6794dda5ea5226349604a58.jpg','1673872562','jpg',27438,11,600,256,94,40),
    (12,'Инсталлятор','2c54efe9e7dd8889a319b2bd2ba19939.png','93991ab2db2b913a9888dd7e9efe45c2.png','1673879520','png',31987,12,418,488,81,94),
    (13,'Вход в панель управления','7357b4f141ad060438dbd1786ea4a3b6.png','6b3a4ae6871dbd834060da141f4b7537.png','1673879962','png',12660,13,377,223,94,56),
    (17,'Дерево сайта','58d7bdd610ac9c467d8928483a5edcb9.png','9bcde5a3848298d764c9ca016ddb7d85.png','1673880646','png',21048,15,449,304,94,64),
    (18,'Рабочий стол','5bceb3bdb203a675eb550320c0e06400.png','00460e0c023055be576a302bdb3becb5.png','1674553476','png',70444,13,853,466,94,51),
    (20,'Навигационное меню','21220d30049815860f4436b2f2887ad2.png','2da7882f2b6344f06851894003d02212.png','1674554727','png',35451,13,377,223,94,56),
    (21,'Текстовый контент','ea59356c3ea6cbe5c469fc094eb3da9a.png','a9ad3be490cf964c5ebc6ae3c65395ae.png','1674555500','png',51419,13,377,293,94,73),
    (22,'Бедный кот','c7db3c5103ad591ef038ce44ff4f0424.jpeg','4240f4ff44ec830fe195da3015c3bd7c.jpeg','1674562233','jpeg',100722,16,811,639,94,74);


# Dump of table vfs_texts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `vfs_texts`;

CREATE TABLE `vfs_texts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `vfs_folder` int(10) unsigned NOT NULL DEFAULT '0',
  `text_formatted` text NOT NULL,
  `text_searchable` text NOT NULL,
  `present_at_pages` varchar(128) NOT NULL DEFAULT '',
  `parent_sheet` int(11) NOT NULL DEFAULT '0',
  `root_sheet` int(11) NOT NULL DEFAULT '0',
  `pos` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `vfs_texts` (`id`, `title`, `vfs_folder`, `text_formatted`, `text_searchable`, `present_at_pages`, `parent_sheet`, `root_sheet`, `pos`)
VALUES
    (1,'Информация',3,'<div><strong>RAMPAGE CMS</strong>&nbsp;— это открытая, бесплатная система для создания веб-сайтов, наполнения&nbsp;и управления контентом.</div>\r\n<div>&nbsp;</div>\r\n<div><strong>История создания</strong></div>\r\n<div><span style=\"white-space: pre;\">&nbsp;</span></div>\r\n<div><span style=\"white-space: pre;\">	</span>Подробно про историю создания можно прочитать <a href=\"http://www.therampage.org/rampagecms\">здесь</a>.</div>\r\n<div>&nbsp;</div>\r\n<div><strong>Исходный код</strong>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div><span style=\"white-space: pre;\">	</span>GitHub:&nbsp;&nbsp;<a href=\"https://github.com/rampagecode/rampagecms\">https://github.com/rampagecode/rampagecms</a></div>\r\n<div>&nbsp;</div>\r\n<div><strong>Системные требования</strong>&nbsp;</div>\r\n<div><span style=\"white-space: pre;\">&nbsp;</span></div>\r\n<div><span style=\"white-space: pre;\"><span style=\"white-space: pre;\">	</span>PHP&nbsp;5.4</span></div>\r\n<div><span style=\"white-space: pre;\">	Apache 2&nbsp;&nbsp;</span></div>\r\n<div><span style=\"white-space: pre;\"><span style=\"white-space: pre;\">	</span>MySQL 5&nbsp;</span></div>\r\n<div><span style=\"white-space: pre;\"><span style=\"white-space: pre;\">	</span>Composer 2.2.10&nbsp;</span>&nbsp;</div>\r\n<div><span style=\"white-space: pre;\">	</span>ZendFramework&nbsp;1.12.20</div>\r\n<div><span style=\"white-space: pre;\">	</span>PHP Unit 4.0.0&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div><strong>Инсталляция</strong></div>\r\n<ul>\r\n<li><a href=\"/installation/configuration/\" title=\"Конфигурация сервера\">Конфигурация сервера</a></li>\r\n<li><a href=\"/installation/setup/\" title=\"Начальная установка\">Начальная установка</a><br />\r\n</li></ul>\r\n<div><strong>Документация</strong></div>\r\n<ul>\r\n<li><a href=\"/documentation/beginning/\" title=\"Начало работы\">Начало работы</a></li>\r\n<li><a href=\"/documentation/structure/\" title=\"Структура сайта\">Структура сайта</a></li>\r\n<li><a href=\"/documentation/modules/\" title=\"Модули\">Модули</a></li>\r\n<li><a href=\"/documentation/users/\" title=\"Пользователи\">Пользователи</a></li>\r\n<li><a href=\"/documentation/settings/\" title=\"Настройки\">Настройки</a></li>\r\n<li><a href=\"/documentation/system/\" title=\"Системные функции\">Системные функции</a></li></ul>\r\n<div><strong>Демонстрация </strong><em>(скоро будет)</em></div>\r\n<ul>\r\n<li>Скачивание и установка</li>\r\n<li>Создание структуры</li>\r\n<li>Редактирование макета</li>\r\n<li>Изменение настроек&nbsp;</li></ul>','RAMPAGE CMS&nbsp;— это открытая, бесплатная система для создания веб-сайтов, наполнения&nbsp;и управления контентом.\r\n&nbsp;\r\nИстория создания\r\n&nbsp;\r\n	Подробно про историю создания можно прочитать здесь.\r\n&nbsp;\r\nИсходный код&nbsp;\r\n&nbsp;\r\n	GitHub:&nbsp;&nbsp;https://github.com/rampagecode/rampagecms\r\n&nbsp;\r\nСистемные требования&nbsp;\r\n&nbsp;\r\n	PHP&nbsp;5.4\r\n	Apache 2&nbsp;&nbsp;\r\n	MySQL 5&nbsp;\r\n	Composer 2.2.10&nbsp;&nbsp;\r\n	ZendFramework&nbsp;1.12.20\r\n	PHP Unit 4.0.0&nbsp;\r\n&nbsp;\r\nИнсталляция\r\n\r\nКонфигурация сервера\r\nНачальная установка\r\n\r\nДокументация\r\n\r\nНачало работы\r\nСтруктура сайта\r\nМодули\r\nПользователи\r\nНастройки\r\nСистемные функции\r\nДемонстрация (скоро будет)\r\n\r\nСкачивание и установка\r\nСоздание структуры\r\nРедактирование макета\r\nИзменение настроек&nbsp;','1',0,0,0),
    (15,'Инсталляция',17,'<div><strong>Инсталляция</strong></div>\r\n<ul>\r\n<li><a href=\"/installation/configuration/\" title=\"Конфигурация сервера\">Конфигурация сервера</a></li>\r\n<li><a href=\"/installation/setup/\" title=\"Начальная установка\">Начальная установка</a><br />\r\n</li></ul>','Инсталляция\r\n\r\nКонфигурация сервера\r\nНачальная установка\r\n','2,1',0,0,0),
    (16,'Документация',10,'<div><strong>Документация</strong></div>\r\n<ul>\r\n<li><a href=\"/documentation/beginning/\" title=\"Начало работы\">Начало работы</a></li>\r\n<li><a href=\"/documentation/structure/\" title=\"Структура сайта\">Структура сайта</a></li>\r\n<li><a href=\"/documentation/modules/\" title=\"Модули\">Модули</a></li>\r\n<li><a href=\"/documentation/users/\" title=\"Пользователи\">Пользователи</a></li>\r\n<li><a href=\"/documentation/settings/\" title=\"Настройки\">Настройки</a></li>\r\n<li><a href=\"/documentation/system/\" title=\"Системные функции\">Системные функции</a></li></ul>','Документация\r\n\r\nНачало работы\r\nСтруктура сайта\r\nМодули\r\nПользователи\r\nНастройки\r\nСистемные функции','3',0,0,0),
    (17,'Конфигурация сервера',17,'<img style=\"margin: 0px 0px 0px 20px;\" src=\"/images/85a4069436225ae5add4976b5efc091d.jpg\" width=\"300\" height=\"128\" align=\"right\" border=\"0\" alt=\"\" title=\"\" />\r\n<div>Так как система была разработана, мягко говоря, не вчера, то и конфигурация сервера для ее запуска тоже не блещет новизной. Тем не менее, имеется <em>Dockerfile</em> для сборки всего необходимого окружения и <em>docker-compose.yml</em> для запуска. Обратите внимание, что это конфигурация для разработки и тестирования, не нужно использовать её для продакшена.</div>\r\n<div><strong>&nbsp;</strong></div>\r\n<div><strong>Порядок действий:</strong></div>\r\n<div>В директории <em>docker-setup</em> находятся все конфигурационные файлы для сборки и запуска рабочего окружения.</div>\r\n<div>Для начала нам нужно собрать веб-сервер, для этого перейдите в директорию:</div>\r\n<div><strong>docker-setup</strong> </div>\r\n<div>и выполните команду:</div>\r\n<div><strong>docker build .</strong></div>\r\n<div><strong>&nbsp;</strong></div>\r\n<div><em>Обратите внимание: </em>в процессе работы скрипта на шаге 10 может возникнуть ошибка ERROR [10/24] связанная с GPG. Это обычно связано с недоступностью стороннего сервера. Можно подождать и повторить запуск команды. Если не помогает, поменяйте сервер GPG в Dockerfile<strong>&nbsp;</strong></div>\r\n<div><strong>&nbsp;</strong></div>\r\n<div>Когда все шаги буду и успешно исполнены нужно запустить окружение.&nbsp;&nbsp;Для этого выполните команду:</div>\r\n<div><strong>docker compose up</strong></div>\r\n<div>После успешного завершения предыдущей команды пропишите доменное имя хоста на локальной машине. В Linux/MacOS файл называется:</div>\r\n<div><strong>/etc/hosts</strong></div>\r\n<div><strong></strong>В Windows: </div>\r\n<div><strong>windows/system32/drivers/etc/hosts</strong></div>\r\n<div>Добавьте в него строку: </div>\r\n<div><strong>127.0.0.1 rampagecms</strong><strong>&nbsp;</strong></div>\r\n<div>После чего откройте браузер и перейдите по адресу:&nbsp;</div>\r\n<div><strong>http://rampagecms</strong></div>\r\n<div><strong>&nbsp;</strong></div>\r\n<div>Дальнейшие инструкции написаны в разделе <a href=\"/installation/setup/\" title=\"Начальная установка\">Начальная установка</a>.<strong>&nbsp;</strong><strong>&nbsp;</strong></div>\r\n<div>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>&nbsp;</div>','\r\nТак как система была разработана, мягко говоря, не вчера, то и конфигурация сервера для ее запуска тоже не блещет новизной. Тем не менее, имеется Dockerfile для сборки всего необходимого окружения и docker-compose.yml для запуска. Обратите внимание, что это конфигурация для разработки и тестирования, не нужно использовать её для продакшена.\r\n&nbsp;\r\nПорядок действий:\r\nВ директории docker-setup находятся все конфигурационные файлы для сборки и запуска рабочего окружения.\r\nДля начала нам нужно собрать веб-сервер, для этого перейдите в директорию:\r\ndocker-setup \r\nи выполните команду:\r\ndocker build .\r\n&nbsp;\r\nОбратите внимание: в процессе работы скрипта на шаге 10 может возникнуть ошибка ERROR [10/24] связанная с GPG. Это обычно связано с недоступностью стороннего сервера. Нужно лишь подождать и повторить запуск команды.&nbsp;\r\n&nbsp;\r\nКогда все шаги буду и успешно исполнены нужно запустить окружение.&nbsp;&nbsp;Для этого выполните команду:\r\ndocker compose up\r\nПосле успешного завершения предыдущей команды пропишите доменное имя хоста на локальной машине. В Linux/MacOS файл называется:\r\n/etc/hosts\r\nВ Windows: \r\nwindows/system32/drivers/etc/hosts\r\nДобавьте в него строку: \r\n127.0.0.1 rampagecms&nbsp;\r\nПосле чего откройте браузер и перейдите по адресу:&nbsp;\r\nhttp://rampagecms\r\n&nbsp;\r\nДальнейшие инструкции написаны в разделе Начальная установка.&nbsp;&nbsp;\r\n&nbsp;\r\n&nbsp;\r\n&nbsp;','4',0,0,0),
    (18,'Начальная установка',17,'<div><img style=\"margin: 0px 0px 0px 10px;\" src=\"/images/2c54efe9e7dd8889a319b2bd2ba19939.png\" width=\"418\" height=\"488\" align=\"right\" border=\"0\" alt=\"\" title=\"\" />После того как окружение сконфигурировано, сервер заработал и сайт открылся, поначалу будет выполняться редирект на скрипт установки. </div>\r\n<div>&nbsp;</div>\r\n<div>Данный скрипт нужен для первоначального подключения сайта к серверу БД, создания таблиц и генерации конфигурационных файлов. </div>\r\n<div>&nbsp;</div>\r\n<div>Вам нужно ввести в поля формы всю требуемую информацию и запустить процесс инсталляции. </div>\r\n<div>&nbsp;</div>\r\n<div>По окончанию установки вы сможете перейти на готовый сайт и зайти в панель управления.</div>\r\n<div>&nbsp;</div>\r\n<div>Если вы установили систему используя конфигурацию докер-контейнер из дистрибутива, то единственное поле которое вам нужно заполнить, это пароль администратора. Этот пароль будет требоваться для входа в панель управления сайтом.</div>\r\n<div>&nbsp;</div>\r\n<div>Дальнейшие действия описаны в разделе <a href=\"/documentation/beginning/\" title=\"Начало работы\">Начало работы</a>&nbsp;</div>','После того как окружение сконфигурировано, сервер заработал и сайт открылся, поначалу будет выполняться редирект на скрипт установки. \r\n&nbsp;\r\nДанный скрипт нужен для первоначального подключения сайта к серверу БД, создания таблиц и генерации конфигурационных файлов. \r\n&nbsp;\r\nВам нужно ввести в поля формы всю требуемую информацию и запустить процесс инсталляции. \r\n&nbsp;\r\nПо окончанию установки вы сможете перейти на готовый сайт и зайти в панель управления.\r\n&nbsp;\r\nЕсли вы установили систему используя конфигурацию докер-контейнер из дистрибутива, то единственное поле которое вам нужно заполнить, это пароль администратора. Этот пароль будет требоваться для входа в панель управления сайтом.\r\n&nbsp;\r\nДальнейшие действия описаны в разделе Начало работы&nbsp;','5',0,0,0),
    (19,'Начало работы',10,'<table border=\"0\" bordercolor=\"#000000\" cellpadding=\"3\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td valign=\"top\">\r\n<div><strong>Вход в панель управления</strong></div>\r\n<div><strong>&nbsp;</strong></div>\r\n<div>Чтобы открыть панель управления сайтом<strong>&nbsp;</strong><em>(сокр.&nbsp;ПУПС)</em>&nbsp;нужно набрать в адресной строке браузера адрес сайта и добавить в к нему <strong>/admin</strong><strong>&nbsp;</strong></div>\r\n<div><em>Например:</em> <a href=\"http://rampagecms/admin\">http://rampagecms/admin</a></div>\r\n<div>&nbsp;</div>\r\n<div>Вы увидите форму входа в которую нужно ввести логин и пароль которые вы указали при установки системы. После успешной авторизации вы попадете на рабочий стол панели управления.</div>\r\n<div><strong>&nbsp;</strong></div>\r\n<div><strong>Рабочий стол</strong>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>На рабочем столе, для быстрого доступа, автоматически закрепляются страницы сайта, с которым недавно велась работа. Если нужно отредактировать страницу иконки которой нет на рабочем столе, её можно найти в Навигационном меню. </div>\r\n<div>&nbsp;</div>\r\n<div><strong>Навигационное меню</strong>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>Чтобы открыть меню, нажмите на большую яркую кнопку в левом верхнем углу. В выпадающем окне будет отображено дерево сайта, со всеми страницами у которых включена опция \"Показывать в меню на сайте\" (об этом подробнее читайте в разделе <a href=\"/documentation/structure/\" title=\"Структура сайта\">Структура сайта</a>). Навигационное меню доступно не только на рабочем столе, но и&nbsp;в любом разделе панели управления.</div>\r\n<div>&nbsp;</div>\r\n<div><strong>Редактирование страницы</strong>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>Чтобы отредактировать страницу нажмите на ее название или иконку на Рабочем столе или в Навигационном меню. При этом откроется страница показывающая весь доступный для редактирования контент на выбранной странице. Это может быть как текст, так и управляемые модули (подробнее в разделе <a href=\"/documentation/modules/\" title=\"Модули\">Модули</a>).&nbsp;</div>\r\n<div><strong>&nbsp;</strong></div>\r\n<div><strong>Текстовый контент</strong>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>Для редактирования текста на страницах используется визуальный текстовый редактор. Он позволяет форматировать текст, форму и размеры шрифта, создавать и редактировать списки, таблицы, ссылки на внутренние и внешние страницы и файлы, загружать и внедрять в текст страницы изображения и много другое.</div></td><td valign=\"top\" align=\"right\" width=\"390\"><br />\r\n<img style=\"margin: 0px;\" src=\"/images/7357b4f141ad060438dbd1786ea4a3b6.png\" width=\"377\" height=\"223\" border=\"0\" alt=\"\" title=\"\" />\r\n<div>&nbsp;</div>\r\n<div>&nbsp;</div><img style=\"margin: 0px;\" src=\"/images/5bceb3bdb203a675eb550320c0e06400.png\" width=\"377\" height=\"223\" border=\"0\" alt=\"\" title=\"\" />\r\n<div>&nbsp;</div>\r\n<div>&nbsp;</div><img style=\"margin: 0px;\" src=\"/images/21220d30049815860f4436b2f2887ad2.png\" width=\"377\" height=\"223\" border=\"0\" alt=\"\" title=\"\" /><br />\r\n<div>&nbsp;</div>\r\n<div>&nbsp;</div><img style=\"margin: 0px;\" src=\"/images/ea59356c3ea6cbe5c469fc094eb3da9a.png\" width=\"377\" height=\"293\" border=\"0\" alt=\"\" title=\"\" />\r\n<div>&nbsp;</div>\r\n<div>&nbsp;</div></td></tr></tbody></table>','\r\nВход в панель управления\r\n&nbsp;\r\nЧтобы открыть панель управления сайтом&nbsp;(сокр.&nbsp;ПУПС)&nbsp;нужно набрать в адресной строке браузера адрес сайта и добавить в к нему /admin&nbsp;\r\nНапример: http://rampagecms/admin\r\n&nbsp;\r\nВы увидите форму входа в которую нужно ввести логин и пароль которые вы указали при установки системы. После успешной авторизации вы попадете на рабочий стол панели управления.\r\n&nbsp;\r\nРабочий стол&nbsp;\r\n&nbsp;\r\nНа рабочем столе, для быстрого доступа, автоматически закрепляются страницы сайта, с которым недавно велась работа. Если нужно отредактировать страницу иконки которой нет на рабочем столе, её можно найти в Навигационном меню. \r\n&nbsp;\r\nНавигационное меню&nbsp;\r\n&nbsp;\r\nЧтобы открыть меню, нажмите на большую яркую кнопку в левом верхнем углу. В выпадающем окне будет отображено дерево сайта, со всеми страницами у которых включена опция \"Показывать в меню на сайте\" (об этом подробнее читайте в разделе Структура сайта). Навигационное меню доступно не только на рабочем столе, но и&nbsp;в любом разделе панели управления.\r\n&nbsp;\r\nРедактирование страницы&nbsp;\r\n&nbsp;\r\nЧтобы отредактировать страницу нажмите на ее название или иконку на Рабочем столе или в Навигационном меню. При этом откроется страница показывающая весь доступный для редактирования контент на выбранной странице. Это может быть как текст, так и управляемые модули (подробнее в разделе Модули).&nbsp;\r\n&nbsp;\r\nТекстовый контент&nbsp;\r\n&nbsp;\r\nДля редактирования текста на страницах используется визуальный текстовый редактор. Он позволяет форматировать текст, форму и размеры шрифта, создавать и редактировать списки, таблицы, ссылки на внутренние и внешние страницы и файлы, загружать и внедрять в текст страницы изображения и много другое.\r\n\r\n&nbsp;\r\n&nbsp;\r\n&nbsp;\r\n&nbsp;\r\n&nbsp;\r\n&nbsp;\r\n&nbsp;\r\n&nbsp;','6',0,0,0),
    (20,'Структура сайта',10,'<img style=\"margin: 0px 0px 0px 10px;\" src=\"/images/58d7bdd610ac9c467d8928483a5edcb9.png\" width=\"449\" height=\"304\" align=\"right\" border=\"0\" alt=\"\" title=\"\" />\r\n<div>На вкладке Структура сайта в левой части экрана расположено дерево сайта. Работая с эти деревом вы можете создавать, изменять, перемещать и удалять страницы сайта, а также изменять их параметры.</div>\r\n<div>&nbsp;</div>\r\n<div><strong>Параметры страницы&nbsp;</strong></div>\r\n<div><strong>&nbsp;</strong></div>\r\n<div>Выберите любой элемент дерева, нажав на него левой клавишей мыши. В правой части экрана отобразится таблица параметров. Каждую страницу можно<strong>&nbsp;</strong>настроить индивидуально. Помимо параметров, каждая страница также может содержать некоторе количество элементов контента и модулей. Чтобы их увидеть перейдите на соседнюю вкладку.</div>\r\n<div>&nbsp;</div>\r\n<div><strong>Модули и контент</strong>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>Если в шаблоне страницы прописаны шаблоны текстового контента или вызовов модулей, то эти элементы будут отображены в этом списке. Шаблон может быть как определенный, вызывающий&nbsp;конкретный текст или метод модуля, так и общий, просто отмечая место, куда можно вывести какой-то контент. В любом случае этот шаблон можно перегрузить, полностью изменив вызываемый контент. Это позволяем гибко настраивать содержимое страниц, не плодя множество макетов.</div>\r\n<div>&nbsp;</div>\r\n<div><strong>Макет страницы</strong>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>В параметрах страницы можно указать её макет в выпадающем списке. Его содержимое формируется на основе содержимого директории <strong>Public/Layouts</strong></div>\r\n<div>Все HTML файлы, чьи имена не начинаются с символа подчеркивания, расположенные в этой директории, считаются основными макетами страниц.</div>\r\n<div>В макетах используется специальный синтаксис чтобы указать системе определенные значения.</div>\r\n<div>&nbsp;</div>\r\n<div><strong>Создание и удаление страниц</strong>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>Чтобы создать или удалить страницу нажмите на любой элемент дерева правой клавишей мыши. В появившемся меню выберите нужный пункт.</div>','\r\nНа вкладке Структура сайта в левой части экрана расположено дерево сайта. Работая с эти деревом вы можете создавать, изменять, перемещать и удалять страницы сайта, а также изменять их параметры.\r\n&nbsp;\r\nПараметры страницы&nbsp;\r\n&nbsp;\r\nВыберите любой элемент дерева, нажав на него левой клавишей мыши. В правой части экрана отобразится таблица параметров. Каждую страницу можно&nbsp;настроить индивидуально. Помимо параметров, каждая страница также может содержать некоторе количество элементов контента и модулей. Чтобы их увидеть перейдите на соседнюю вкладку.\r\n&nbsp;\r\nМодули и контент&nbsp;\r\n&nbsp;\r\nЕсли в шаблоне страницы прописаны шаблоны текстового контента или вызовов модулей, то эти элементы будут отображены в этом списке. Шаблон может быть как определенный, вызывающий&nbsp;конкретный текст или метод модуля, так и общий, просто отмечая место, куда можно вывести какой-то контент. В любом случае этот шаблон можно перегрузить, полностью изменив вызываемый контент. Это позволяем гибко настраивать содержимое страниц, не плодя множество макетов.\r\n&nbsp;\r\nМакет страницы&nbsp;\r\n&nbsp;\r\nВ параметрах страницы можно указать её макет в выпадающем списке. Его содержимое формируется на основе содержимого директории Public/Layouts\r\nВсе HTML файлы, чьи имена не начинаются с символа подчеркивания, расположенные в этой директории, считаются основными макетами страниц.\r\nВ макетах используется специальный синтаксис чтобы указать системе определенные значения.\r\n&nbsp;\r\nСоздание и удаление страниц&nbsp;\r\n&nbsp;\r\nЧтобы создать или удалить страницу нажмите на любой элемент дерева правой клавишей мыши. В появившемся меню выберите нужный пункт.','7',0,0,0),
    (21,'Модули',10,'<div>Модули делятся на <em>простые</em> и <em>сложные</em>. Простые модули не имеют внутреннего состояния, а значит не&nbsp;требуют установки. Они просто выполняют определенную функцию. Например модули <em>Меню</em> выводит меню сайта. Такие модули не имею интерфейсов администрирования в панели управления сайтом.</div>\r\n<div>&nbsp;</div>\r\n<div>Сложные модули сначала необходимо установить. В процессе установки они создают отдельную таблицу в базе данных где хранят нужную им информацию. Например модуль <em>Посты</em> подходит для создания группы записей на сайте, это может быть блог, новости, статьи. Функционально эти раздели идентичны, но отличаются по наполнению и предназначению. Поэтому один модуль может быть установлен три раза, давая возможность работать с одним кодом, как с тремя отдельными модулями.</div>\r\n<div>&nbsp;</div>\r\n<div>Код модулей распологается в директории <strong>Module</strong></div>','Модули делятся на простые и сложные. Простые модули не имеют внутреннего состояния, а значит не&nbsp;требуют установки. Они просто выполняют определенную функцию. Например модули Меню выводит меню сайта. Такие модули не имею интерфейсов администрирования в панели управления сайтом.\r\n&nbsp;\r\nСложные модули сначала необходимо установить. В процессе установки они создают отдельную таблицу в базе данных где хранят нужную им информацию. Например модуль Посты подходит для создания группы записей на сайте, это может быть блог, новости, статьи. Функционально эти раздели идентичны, но отличаются по наполнению и предназначению. Поэтому один модуль может быть установлен три раза, давая возможность работать с одним кодом, как с тремя отдельными модулями.\r\n&nbsp;\r\nКод модулей распологается в директории Module','8',0,0,0),
    (22,'Пользователи',10,'<div>По умолчанию в системе есть только один пользователь - Администратор. Но также реализована возможность создавать других пользователей и назначать им различные уровни доступа. Например можно создать группу пользователей, которая дает доступ в панель управления, то только к определенным ее разделам, тем самым позволяя давать ограниченный административный доступ, не боясь, что пользователи могут сделать что-то лишнее.&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>В параметрах страницы также можно указать группы&nbsp; пользователей которые имеют к ней доступ, тем самым создавая на сайте закрытые разделы, доступные только определенным группам зарегистрированных пользователей.</div>','По умолчанию в системе есть только один пользователь - Администратор. Но также реализована возможность создавать других пользователей и назначать им различные уровни доступа. Например можно создать группу пользователей, которая дает доступ в панель управления, то только к определенным ее разделам, тем самым позволяя давать ограниченный административный доступ, не боясь, что пользователи могут сделать что-то лишнее.&nbsp;\r\n&nbsp;\r\nВ параметрах страницы также можно указать группы&nbsp; пользователей которые имеют к ней доступ, тем самым создавая на сайте закрытые разделы, доступные только определенным группам зарегистрированных пользователей.','9',0,0,0),
    (23,'Настройки',10,'<div>В разделе Настройки можно задать различные параметры сайта, например название, описание, ключевые слова, указать конфигурацию почтового сервера и задать адреса для входящей и исходящей электронной почты, которые будут использованы для системой при отправке сообщений с сайта и для отображения адреса обратной связи. Настройки времени позволяют точно настроить отображение времени на сайте не привязываясь к времени сервера, а настройки доступа помогут ограничить потенциально опасный доступ к сайту.</div>\r\n<div>&nbsp;</div>','В разделе Настройки можно задать различные параметры сайта, например название, описание, ключевые слова, указать конфигурацию почтового сервера и задать адреса для входящей и исходящей электронной почты, которые будут использованы для системой при отправке сообщений с сайта и для отображения адреса обратной связи. Настройки времени позволяют точно настроить отображение времени на сайте не привязываясь к времени сервера, а настройки доступа помогут ограничить потенциально опасный доступ к сайту.\r\n&nbsp;','10',0,0,0),
    (24,'Системные функции',10,'<div><img style=\"margin: 0px 0px 10px 10px;\" src=\"/images/c7db3c5103ad591ef038ce44ff4f0424.jpeg\" width=\"406\" height=\"320\" align=\"right\" border=\"0\" alt=\"\" title=\"\" />&nbsp;</div>\r\n<div>Системные функции касаются задач связанных с работой системы и предназначены для администраторов и веб-мастеров.</div>\r\n<div><strong>&nbsp;</strong></div>\r\n<div><strong>Кеширование</strong>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>На данный момент система кеширует только некоторые собственные значения, в частности дерево сайта и определенные настройки. В будущем планируется еще добавить кеширование страниц.</div>\r\n<div>&nbsp;</div>\r\n<div><strong>Блокировка</strong>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>В случае проведения каких-либо работ на сайте, например обновление системы, доступ к публичной части&nbsp;сайта можно заблокировать. При этом доступ к панели управления останется.</div>\r\n<div>&nbsp;</div>\r\n<div><strong>Отладка</strong></div>\r\n<div><strong>&nbsp;</strong></div>\r\n<div>Система отслеживает и обрабатывает ошибки, которые могут возникнуть на сайте. Кроме того есть логирование многих операций, чтобы определить этап на котором что-то произошло. В обычном режиме вся эта информация не отображается на экране. Вместо этого идет запись в файл, а также возможно оправка сообщений об ошибках на почту администратора. Если же разработчику или администратору требуется иметь эту информацию перед глазами, то можно включить режим отладки. В таком случае все сообщения будут выводится на экран, либо в консоль разработчика.</div>','&nbsp;\r\nСистемные функции касаются задач связанных с работой системы и предназначены для администраторов и веб-мастеров.\r\n&nbsp;\r\nКеширование&nbsp;\r\n&nbsp;\r\nНа данный момент система кеширует только некоторые собственные значения, в частности дерево сайта и определенные настройки. В будущем планируется еще добавить кеширование страниц.\r\n&nbsp;\r\nБлокировка&nbsp;\r\n&nbsp;\r\nВ случае проведения каких-либо работ на сайте, например обновление системы, доступ к публичной части&nbsp;сайта можно заблокировать. При этом доступ к панели управления останется.\r\n&nbsp;\r\nОтладка\r\n&nbsp;\r\nСистема отслеживает и обрабатывает ошибки, которые могут возникнуть на сайте. Кроме того есть логирование многих операций, чтобы определить этап на котором что-то произошло. В обычном режиме вся эта информация не отображается на экране. Вместо этого идет запись в файл, а также возможно оправка сообщений об ошибках на почту администратора. Если же разработчику или администратору требуется иметь эту информацию перед глазами, то можно включить режим отладки. В таком случае все сообщения будут выводится на экран, либо в консоль разработчика.','11',0,0,0);
