-- MariaDB dump converted from SQL Server
-- Database: jxm_news
-- Generated: 2026-06-27

CREATE DATABASE IF NOT EXISTS `jxm_news` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `jxm_news`;

SET FOREIGN_KEY_CHECKS=0;

-- Table: `Category`
DROP TABLE IF EXISTS `Category`;
CREATE TABLE `Category` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `FK_News`
DROP TABLE IF EXISTS `FK_News`;
CREATE TABLE `FK_News` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(250) NULL,
  `slug` VARCHAR(250) NULL,
  `date` DATETIME NULL,
  `fkcontent` LONGTEXT NULL,
  `fksubcontent` VARCHAR(500) NULL,
  `categoryId` INT NULL DEFAULT 1,
  `IsStatus` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Foreign Key Constraints
ALTER TABLE `FK_News` ADD CONSTRAINT `FK_New_Category` FOREIGN KEY (`categoryId`) REFERENCES `Category` (`Id`);

SET FOREIGN_KEY_CHECKS=1;