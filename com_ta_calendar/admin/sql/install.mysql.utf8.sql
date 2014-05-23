CREATE TABLE IF NOT EXISTS `#__ta_calendar_events` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`state` TINYINT(1)  NOT NULL DEFAULT '1',
	`org` int(11) NOT NULL,
	`start` DATETIME NOT NULL ,
	`end` DATETIME NOT NULL ,
	`title` VARCHAR(255) NOT NULL ,
	`summary` TEXT NOT NULL ,
	`type` INT NOT NULL ,
	`event_url` VARCHAR(150)  NOT NULL ,
	`registration_url` VARCHAR(150)  NOT NULL ,
	`grant_program` INT NOT NULL ,
	`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ,
	`created_by` INT(11)  NOT NULL ,
	`modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ,
	`modified_by` INT(11) NOT NULL ,
	`deleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ,
	`deleted_by` INT(11) NOT NULL ,
	`checked_out` INT(11) NOT NULL ,
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
	`approved` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ,
	`approved_by` INT(11) NOT NULL ,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__ta_calendar_event_types` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`state` TINYINT(1)  NOT NULL DEFAULT '1',
	`name` VARCHAR(50)  NOT NULL ,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__ta_calendar_event_types` (`name`) VALUES
	('Conference'),
	('Meeting'),
	('Teleconf'),
	('Training'),
	('Webinar');

CREATE TABLE IF NOT EXISTS `#__ta_calendar_topic_areas` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`state` TINYINT(1)  NOT NULL DEFAULT '1',
	`name` VARCHAR(50)  NOT NULL ,
	`checked_out` INT(11)  NOT NULL ,
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_by` INT(11)  NOT NULL ,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__ta_calendar_event_topic_areas` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`topic_area` INT NOT NULL ,
	`event` INT NOT NULL ,
	PRIMARY KEY (`id`),
	UNIQUE INDEX (`topic_area`,`event`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__ta_calendar_event_target_audiences` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`target_audience` INT NOT NULL ,
	`event` INT NOT NULL ,
	PRIMARY KEY (`id`),
	UNIQUE INDEX (`target_audience`,`event`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__ta_calendar_user_settings` (
	`user` int(11) NOT NULL,
	`timezone` varchar(50) NOT NULL,
	`view` varchar(5) NOT NULL,
	`filters` text,
	PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__ta_calendar_timezones` (
	`abbr` varchar(5) NOT NULL,
	`description` varchar(50) NOT NULL,
	PRIMARY KEY (`abbr`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__ta_calendar_timezones` VALUES
('AST','America/Puerto_Rico'),
('EDT','America/New_York'),
('CDT','America/Chicago'),
('MDT','America/Boise'),
('MST','America/Phoenix'),
('PDT','America/Los_Angeles'),
('AKDT','America/Juneau'),
('HST','Pacific/Honolulu'),
('ChST','Pacific/Guam'),
('SST','Pacific/Samoa'),
('WAKT','Pacific/Wake');