# SQL file to run when updating to 2.1.0
ALTER TABLE `#__tapd_provider_projects` ADD `award_number` VARCHAR(15) AFTER `summary`;