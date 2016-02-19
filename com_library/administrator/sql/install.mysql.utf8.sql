CREATE TABLE IF NOT EXISTS `#__library` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`state` TINYINT(1) NOT NULL DEFAULT '1' ,
	`org` INT(11) NOT NULL , 
	`project` INT(11) ,
	`name` VARCHAR(150) NOT NULL ,
	`description` TEXT NOT NULL ,
	`base_file_name` VARCHAR(150) NOT NULL ,
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
	`created_by` INT(11) NOT NULL ,
	`modified` DATETIME NULL DEFAULT NULL ,
	`modified_by` INT(11) NOT NULL ,
	`deleted` DATETIME NULL DEFAULT NULL ,
	`deleted_by` INT(11) NOT NULL ,
	`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__library_target_audiences` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`library_item` int(11) NOT NULL ,
	`target_audience` int(11) NOT NULL ,
	PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

ALTER TABLE `#__library_target_audiences` ADD UNIQUE `Unique` ( `library_item` , `target_audience` );

CREATE TABLE IF NOT EXISTS `#__library_access_tokens` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`resource` int(11) NOT NULL,
	`token` varchar(64) NOT NULL,
	`exp_date` DATETIME NOT NULL,
	PRIMARY KEY(`id`)
) DEFAULT COLLATE=utf8_general_ci;

ALTER TABLE `#__library_access_tokens` ADD UNIQUE `Unique` (`token`);

CREATE TRIGGER library_token_expiration_date BEFORE INSERT ON `#__library_access_tokens` FOR EACH ROW SET NEW.exp_date = IFNULL(NEW.exp_date,NOW()), NEW.exp_date= TIMESTAMPADD(DAY,30,NOW());