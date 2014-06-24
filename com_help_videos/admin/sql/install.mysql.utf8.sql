CREATE TABLE IF NOT EXISTS `#__help_videos`(
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`state` TINYINT(1) NOT NULL DEFAULT '1',
	`title` VARCHAR(100) NOT NULL,
	`summary` TEXT NOT NULL,
	`youtube_id` VARCHAR(11) NOT NULL,
	`category` INT(11) NOT NULL,
	`duration` INT(11) NOT NULL,
	`published` DATETIME NOT NULL,
	`checked_out` INT(11) NOT NULL,
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__help_videos_categories` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`state` TINYINT(1) NOT NULL DEFAULT '1',
	`name` VARCHAR(50) NOT NULL,
	`checked_out` INT(11) NOT NULL,
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;