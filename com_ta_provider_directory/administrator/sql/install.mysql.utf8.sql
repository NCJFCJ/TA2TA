CREATE TABLE IF NOT EXISTS `#__tapd_project_programs` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`project` INT NOT NULL,
	`program` INT NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__tapd_provider_projects` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`state` TINYINT(1) NOT NULL DEFAULT '1',
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_by` INT(11) NOT NULL,
	`modified` DATETIME NULL DEFAULT NULL,
	`modified_by` INT(11) NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	`summary` TEXT NOT NULL,
	`provider` INT NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__tapd_project_contacts` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`ordering` INT(11) NOT NULL,
	`state` TINYINT(1) NOT NULL DEFAULT '1',
	`created_by` INT(11) NOT NUL ,
	`first_name` VARCHAR(30) NOT NULL,
	`last_name` VARCHAR(30) NOT NULL,
	`title` VARCHAR(150) NOT NULL,
	`phone` VARCHAR(15) NOT NULL,
	`email` VARCHAR(150) NOT NULL,
	`project` INT NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;