ALTER TABLE `#__services_registrations` CHANGE `webinar` `service` INT(11) UNSIGNED NOT NULL;
ALTER TABLE `#__services_registrations` ADD COLUMN `service_type` VARCHAR(10) NOT NULL DEFAULT 'webinar' AFTER `service`;
ALTER TABLE `#__services_meeting_requests` ADD COLUMN `alias` VARCHAR(50) NOT NULL AFTER `state`;
ALTER TABLE `#__services_roundtable_requests` ADD COLUMN `alias` VARCHAR(50) NOT NULL AFTER `state`;