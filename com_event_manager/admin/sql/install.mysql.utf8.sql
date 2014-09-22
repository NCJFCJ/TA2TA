CREATE TABLE IF NOT EXISTS `#__event_manager_events`(
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL ,
	`type` INT(11) NOT NULL,
	`org` int(11) NOT NULL,
	`start` DATETIME NOT NULL ,
	`end` DATETIME NOT NULL ,
	`grant_program` INT NOT NULL ,
	`project` INT NOT NULL,
	`event_url` VARCHAR(150)  NOT NULL ,
	`summary` TEXT NOT NULL ,
	`notes` TEXT NOT NULL,
	`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ,
	`created_by` INT(11)  NOT NULL ,
	`modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ,
	`modified_by` INT(11) NOT NULL ,
	`approved` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ,
	`approved_by` INT(11) NOT NULL ,
	PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__event_manager_event_target_audiences` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`target_audience` INT NOT NULL ,
	`event` INT NOT NULL ,
	PRIMARY KEY (`id`),
	UNIQUE INDEX (`target_audience`,`event`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__event_manager_event_topic_areas` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`topic_area` INT NOT NULL ,
	`event` INT NOT NULL ,
	PRIMARY KEY (`id`),
	UNIQUE INDEX (`topic_area`,`event`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__event_manager_meetings` (
	`event` int(11) UNSIGNED,
	`registration_service` TINYINT(1) NOT NULL ,
	`assistance_request` TEXT NOT NULL ,
	`registration_url` VARCHAR(150)  NOT NULL ,
	PRIMARY KEY (`event`),
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__event_manager_roundtables` (
	`event` int(11) UNSIGNED,
	`proposed_topic` VARCHAR(255) NOT NULL ,
	`proposed_dates` VARCHAR(255) NOT NULL ,
	`proposed_locations` VARCHAR(255) NOT NULL ,
	`proposed_length` VARCHAR(100) NOT NULL ,
	`number_participants` INT(5) NOT NULL ,
	`similar_topic_partner` TINYINT(1) NOT NULL ,
	`benefits_to_dv` TEXT NOT NULL ,
	`advance_mission` TEXT NOT NULL ,
	`goals` TEXT NOT NULL ,
	PRIMARY KEY (`event`),
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__event_manager_webinars` (
	`event` INT(11) UNSIGNED,
	`registration_service` TINYINT(1) NOT NULL ,
	`registration_url` VARCHAR(150)  NOT NULL ,
	`number_participants` INT(5) NOT NULL ,
	`number_staff` INT(5) NOT NULL ,
	`features` VARCHAR(255) NOT NULL ,
	`asl_request` TINYINT(1) NOT NULL ,
	PRIMARY KEY (`event`),
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;












