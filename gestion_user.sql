-- create database 
-- alter table user add column date_crea datetime default null;
-- not null

CREATE DATABASE 
`gestion_user` 
DEFAULT CHARACTER 
SET utf8 
COLLATE utf8_general_ci;

-- create table user but before we must use it ...

USE `gestion_user` ;

DROP TABLE IF EXISTS `user`; 

CREATE TABLE `user` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'no',
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  AUTO_INCREMENT=1 ;

-- insert data 
INSERT INTO `user` (`name`) 
VALUES ('Antoine'), ('Paul'), ('Cécile'), ('Naoudi'), ('Fenley');

-- en console pour ajouter un champ à la table
-- ALTER TABLE `user` ADD COLUMN `date_crea` DATETIME DEFAULT NULL;


