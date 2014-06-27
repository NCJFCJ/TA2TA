CREATE TABLE IF NOT EXISTS `#__ta_providers` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`state` TINYINT(1)  NOT NULL ,
`name` VARCHAR(150)  NOT NULL ,
`website` VARCHAR(255) NOT NULL ,
`logo` VARCHAR(50) ,
`created` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`modified_by` INT(11) NOT NULL ,
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;