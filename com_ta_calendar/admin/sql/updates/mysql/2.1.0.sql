ALTER TABLE `#__ta_calendar_events` ADD `30dayalert` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `#__ta_calendar_events` ADD `7dayalert` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `#__ta_calendar_user_settings` ADD `alerts` tinyint(1) NOT NULL DEFAULT '1';