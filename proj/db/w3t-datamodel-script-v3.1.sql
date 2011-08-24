SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP DATABASE IF EXISTS `w3tdb` ;
CREATE DATABASEv IF NOT EXISTS `w3tdb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `w3tdb` ;

-- -----------------------------------------------------
-- Table `w3tdb`.`age_range`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`age_range` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`age_range` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `description` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`user` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`user` (
  `user_account` VARCHAR(50) NOT NULL ,
  `first_name` VARCHAR(45) NOT NULL ,
  `age_range_id` INT NULL DEFAULT 1 ,
  `ethnicity` VARCHAR(45) NULL DEFAULT 'N/A' ,
  `pwd` VARCHAR(50) NOT NULL ,
  `register_time` TIMESTAMP NOT NULL ,
  `opt_in` TINYINT(1)  NULL DEFAULT true ,
  `verified` TINYINT(1)  NULL DEFAULT false ,
  `deleted` TINYINT(1)  NULL DEFAULT false ,
  PRIMARY KEY (`user_account`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`store_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`store_type` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`store_type` (
  `store_type` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`store_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`store`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`store` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`store` (
  `store_account` VARCHAR(50) NOT NULL ,
  `pwd` VARCHAR(50) NOT NULL ,
  `store_name` VARCHAR(50) NOT NULL ,
  `parent_store_account` VARCHAR(50) NULL ,
  `store_type` VARCHAR(50) NULL DEFAULT 'normal' ,
  `register_time` TIMESTAMP NOT NULL ,
  `verified` TINYINT(1)  NULL DEFAULT false ,
  PRIMARY KEY (`store_account`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`receipt_source`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`receipt_source` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`receipt_source` (
  `source` VARCHAR(20) NOT NULL ,
  PRIMARY KEY (`source`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`currency`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`currency` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`currency` (
  `mark` VARCHAR(5) NOT NULL ,
  `code` VARCHAR(10) NULL ,
  PRIMARY KEY (`mark`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`receipt`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`receipt` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`receipt` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `store_define_id` VARCHAR(50) NULL ,
  `store_account` VARCHAR(50) NOT NULL ,
  `user_account` VARCHAR(50) NOT NULL ,
  `receipt_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `extra_cost` DECIMAL(10,2) NULL DEFAULT 0 ,
  `cut_down_cost` DECIMAL(10,2) NULL DEFAULT 0 ,
  `sub_total_cost` DECIMAL(10,2) NULL DEFAULT 0 ,
  `tax` DECIMAL(10,2) NOT NULL ,
  `total_cost` DECIMAL(10,2) NOT NULL ,
  `currency_mark` VARCHAR(5) NOT NULL DEFAULT '$' ,
  `source` VARCHAR(20) NULL DEFAULT 'default' ,
  `img` BLOB NULL ,
  `deleted` TINYINT(1)  NULL DEFAULT false ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`receipt_item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`receipt_item` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`receipt_item` (
  `item_id` INT NULL ,
  `receipt_id` INT NOT NULL ,
  `item_name` VARCHAR(300) NOT NULL ,
  `item_qty` INT NOT NULL ,
  `item_discount` DECIMAL(10,2) NOT NULL ,
  `item_price` DECIMAL(10,2) NOT NULL ,
  `deleted` TINYINT(1)  NULL DEFAULT false ,
  PRIMARY KEY (`item_name`, `receipt_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`contact_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`contact_type` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`contact_type` (
  `contact_type` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`contact_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`contact`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`contact` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`contact` (
  `value` VARCHAR(100) NOT NULL ,
  `store_account` VARCHAR(50) NULL ,
  `user_account` VARCHAR(50) NULL ,
  `contact_type` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`value`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`address`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`address` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`address` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `add1` VARCHAR(100) NULL ,
  `add2` VARCHAR(100) NULL ,
  `zipcode` VARCHAR(50) NOT NULL ,
  `city` VARCHAR(100) NULL ,
  `state` VARCHAR(100) NULL ,
  `country` VARCHAR(100) NOT NULL ,
  `store_account` VARCHAR(50) NULL ,
  `user_account` VARCHAR(50) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`service`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`service` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`service` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `service_name` VARCHAR(50) NULL ,
  `discription` TEXT NULL ,
  `price` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`store_service`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`store_service` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`store_service` (
  `store_account` VARCHAR(50) NOT NULL ,
  `service_id` INT NOT NULL ,
  PRIMARY KEY (`store_account`, `service_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`tag` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`tag` (
  `tag` VARCHAR(20) NOT NULL ,
  `user_account` VARCHAR(50) NOT NULL ,
  PRIMARY KEY (`tag`, `user_account`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `w3tdb`.`receipt_tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `w3tdb`.`receipt_tag` ;

CREATE  TABLE IF NOT EXISTS `w3tdb`.`receipt_tag` (
  `tag` VARCHAR(20) NOT NULL ,
  `user_account` VARCHAR(50) NOT NULL ,
  `receipt_id` INT NOT NULL ,
  PRIMARY KEY (`tag`, `user_account`, `receipt_id`) )
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
