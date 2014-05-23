ALTER TABLE `#__ta_calendar_events` DROP `grant_program`;
ALTER TABLE `#__ta_calendar_events` ADD `provider_project` int(11) NOT NULL AFTER `registration_url`;
ALTER TABLE `#__ta_calendar_events` ADD `open` tinyint(1) NOT NULL AFTER `event_url`;
CREATE TABLE IF NOT EXISTS `#__ta_calendar_event_programs` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`event` INT NOT NULL ,
	`program` INT NOT NULL ,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;