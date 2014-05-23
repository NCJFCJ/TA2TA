CREATE TABLE IF NOT EXISTS `#__grant_programs` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NOT NULL DEFAULT '1',
`name` VARCHAR(50)  NOT NULL ,
`fund` VARCHAR(3)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00',
`created_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

