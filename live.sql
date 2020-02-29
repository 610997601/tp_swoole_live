//球队表
CREATE TABLE `live_team`(
    `id` tinyint(1) unsigned NOT NULL auto_increment,
    `name` varchar(20) NOT NULL DEFAULT '',
    `image` varchar(20) NOT NULL DEFAULT '' COMMENT '球队log',
    `type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '分区:0东西1西部',
    `create_time` int(10) unsigned NOT NULL DEFAULT 0,
    `update_time` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8;

//直播表
CREATE TABLE `live_game`(
    `id` int(10) unsigned NOT NULL auto_increment,
    `team_one_id` tinyint(1) unsigned NOT NULL,
    `team_two_id` tinyint(1) unsigned NOT NULL,
    `team_one_score` int(10) unsigned NOT NULL,
    `team_two_score` int(10) unsigned NOT NULL,
    `narrator` varchar(20) NOT NULL DEFAULT '' COMMENT '解说员姓名',
    `image` varchar(20) NOT NULL DEFAULT '',
    `status` tinyint(1) unsigned NOT NULL COMMENT '比赛状态',
    `start_time` int(10) unsigned NOT NULL DEFAULT 0,
    `create_time` int(10) unsigned NOT NULL DEFAULT 0,
    `update_time` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8;

//球员表
CREATE TABLE `live_player`(
    `id` int(10) unsigned NOT NULL auto_increment,
    `name` varchar(20) NOT NULL DEFAULT '' COMMENT '球员姓名',
    `image` varchar(20) NOT NULL DEFAULT '' COMMENT '球员头像',
    `age` tinyint(1) unsigned NOT NULL DEFAULT 0,
    `position` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '球员打的位置',
    `create_time` int(10) unsigned NOT NULL DEFAULT 0,
    `update_time` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8;

//赛事赛况表
CREATE TABLE `live_outs`(
    `id` int(10) unsigned NOT NULL auto_increment,
    `game_id` int(10) unsigned NOT NULL DEFAULT 0,
    `team_id` tinyint(1) unsigned NOT NULL DEFAULT 0,
    `content` varchar(200) NOT NULL DEFAULT '' COMMENT '解说内容',
    `image` varchar(20) NOT NULL DEFAULT '' COMMENT '解说时发的图片',
    `type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '表示打到第几节了',
    `status` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '球员打的位置',
    `create_time` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8;

//聊天室表
CREATE TABLE `live_chart`(
    `id` int(10) unsigned NOT NULL auto_increment,
    `game_id` int(10) unsigned NOT NULL DEFAULT 0,
    `user_id` int(10) unsigned NOT NULL DEFAULT 0,
    `content` varchar(200) NOT NULL DEFAULT '' COMMENT '聊天内容',
    `create_time` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT charset=utf8;