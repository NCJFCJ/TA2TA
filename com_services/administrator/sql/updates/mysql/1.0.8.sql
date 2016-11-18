ALTER TABLE `#__services_registrations` ADD COLUMN `address` VARCHAR(255) NOT NULL AFTER `position`;
ALTER TABLE `#__services_registrations` ADD COLUMN `address2` VARCHAR(255) NOT NULL AFTER `address`;
ALTER TABLE `#__services_registrations` ADD COLUMN `phone` VARCHAR(20) NOT NULL AFTER `address2`;
ALTER TABLE `#__services_registrations` ADD COLUMN `fax` VARCHAR(20) NOT NULL AFTER `phone`;